<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Credit Note Model
 * Handles database operations for credit notes
 */
class Credit_note_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get credit notes with pagination and filters
     */
    public function get_credit_notes($limit, $offset, $filters = array()) {
        $this->db->select('cn.*, u.username as created_by_name, c.customer_number');
        $this->db->from('credit_notes cn');
        $this->db->join('users u', 'u.user_id = cn.created_by', 'left');
        $this->db->join('customers c', 'c.customer_id = cn.customer_id', 'left');

        // Apply filters
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('cn.credit_note_number', $filters['search']);
            $this->db->or_like('cn.customer_name', $filters['search']);
            $this->db->or_like('cn.reference_number', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('cn.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('cn.credit_note_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('cn.credit_note_date <=', $filters['to_date']);
        }

        $this->db->order_by('cn.credit_note_date', 'DESC');
        $this->db->order_by('cn.credit_note_id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count credit notes for pagination
     */
    public function count_credit_notes($filters = array()) {
        $this->db->from('credit_notes cn');

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('cn.credit_note_number', $filters['search']);
            $this->db->or_like('cn.customer_name', $filters['search']);
            $this->db->or_like('cn.reference_number', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('cn.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('cn.credit_note_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('cn.credit_note_date <=', $filters['to_date']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get single credit note by ID
     */
    public function get_credit_note($id) {
        $this->db->select('cn.*, u.username as created_by_name, c.customer_number');
        $this->db->from('credit_notes cn');
        $this->db->join('users u', 'u.user_id = cn.created_by', 'left');
        $this->db->join('customers c', 'c.customer_id = cn.customer_id', 'left');
        $this->db->where('cn.credit_note_id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get credit note items
     */
    public function get_credit_note_items($credit_note_id) {
        $this->db->select('cni.*, coa.account_name, coa.account_code');
        $this->db->from('credit_note_items cni');
        $this->db->join('chart_of_accounts coa', 'coa.account_id = cni.account_id', 'left');
        $this->db->where('cni.credit_note_id', $credit_note_id);
        $this->db->order_by('cni.item_id', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Insert credit note with items
     */
    public function insert_credit_note($credit_note_data, $items) {
        $this->db->trans_start();

        // Insert credit note
        $this->db->insert('credit_notes', $credit_note_data);
        $credit_note_id = $this->db->insert_id();

        // Insert credit note items
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                $item_data = array(
                    'credit_note_id' => $credit_note_id,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'quantity' => isset($item['quantity']) ? $item['quantity'] : 1.00,
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['amount']
                );
                $this->db->insert('credit_note_items', $item_data);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $credit_note_id : false;
    }

    /**
     * Post credit note to accounts (create journal entry)
     */
    public function post_credit_note($credit_note_id) {
        $credit_note = $this->get_credit_note($credit_note_id);

        if (!$credit_note || $credit_note->status !== 'issued') {
            return false;
        }

        $this->db->trans_start();

        // Create journal entry
        $je_data = array(
            'entry_number' => $this->generate_je_number(),
            'entry_date' => $credit_note->credit_note_date,
            'entry_type' => 'Credit Note',
            'reference_type' => 'Credit Note',
            'reference_id' => $credit_note_id,
            'reference_number' => $credit_note->credit_note_number,
            'narration' => 'Credit Note - ' . $credit_note->customer_name . ' - ' . $credit_note->reason,
            'total_debit' => $credit_note->total_amount,
            'total_credit' => $credit_note->total_amount,
            'status' => 'posted',
            'created_by' => $this->session->userdata('user_id'),
            'company_id' => $credit_note->company_id,
            'branch_id' => $credit_note->branch_id,
            'financial_year_id' => $credit_note->financial_year_id
        );

        $this->db->insert('journal_entries', $je_data);
        $je_id = $this->db->insert_id();

        // Get items
        $items = $this->get_credit_note_items($credit_note_id);

        // Debit: Various accounts (from items) + VAT
        if ($items) {
            foreach ($items as $item) {
                $this->db->insert('journal_entry_lines', array(
                    'journal_entry_id' => $je_id,
                    'account_id' => $item->account_id,
                    'debit_amount' => $item->amount,
                    'credit_amount' => 0,
                    'description' => $item->description
                ));
            }
        }

        // Debit: VAT Input account
        if ($credit_note->vat_amount > 0) {
            $this->db->insert('journal_entry_lines', array(
                'journal_entry_id' => $je_id,
                'account_id' => $this->get_vat_input_account_id(),
                'debit_amount' => $credit_note->vat_amount,
                'credit_amount' => 0,
                'description' => 'VAT on Credit Note'
            ));
        }

        // Credit: Customer Account (reducing receivable)
        $this->db->insert('journal_entry_lines', array(
            'journal_entry_id' => $je_id,
            'account_id' => $this->get_customer_account_id(),
            'debit_amount' => 0,
            'credit_amount' => $credit_note->total_amount,
            'description' => 'Credit Note to ' . $credit_note->customer_name
        ));

        // Update credit note status
        $this->db->where('credit_note_id', $credit_note_id);
        $this->db->update('credit_notes', array('status' => 'posted'));

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Delete credit note
     */
    public function delete_credit_note($id) {
        $this->db->trans_start();

        // Delete credit note items
        $this->db->where('credit_note_id', $id);
        $this->db->delete('credit_note_items');

        // Delete credit note
        $this->db->where('credit_note_id', $id);
        $this->db->delete('credit_notes');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Generate credit note number (CN-YYYY-NNNNN)
     */
    public function generate_credit_note_number() {
        $year = date('Y');

        $this->db->select('credit_note_number');
        $this->db->from('credit_notes');
        $this->db->like('credit_note_number', 'CN-' . $year, 'after');
        $this->db->order_by('credit_note_id', 'DESC');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        if ($result) {
            $parts = explode('-', $result->credit_note_number);
            $number = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
        } else {
            $number = 1;
        }

        return 'CN-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate journal entry number
     */
    private function generate_je_number() {
        $year = date('Y');

        $this->db->select('entry_number');
        $this->db->from('journal_entries');
        $this->db->like('entry_number', 'JE-' . $year, 'after');
        $this->db->order_by('journal_entry_id', 'DESC');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        if ($result) {
            $parts = explode('-', $result->entry_number);
            $number = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
        } else {
            $number = 1;
        }

        return 'JE-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get journal entry for credit note
     */
    public function get_journal_entry($credit_note_id) {
        $this->db->select('je.*, u.username as created_by_name');
        $this->db->from('journal_entries je');
        $this->db->join('users u', 'u.user_id = je.created_by', 'left');
        $this->db->where('je.reference_type', 'Credit Note');
        $this->db->where('je.reference_id', $credit_note_id);

        $je = $this->db->get()->row();

        if ($je) {
            $this->db->select('jel.*, coa.account_name, coa.account_code');
            $this->db->from('journal_entry_lines jel');
            $this->db->join('chart_of_accounts coa', 'coa.account_id = jel.account_id', 'left');
            $this->db->where('jel.journal_entry_id', $je->journal_entry_id);
            $je->lines = $this->db->get()->result();
        }

        return $je;
    }

    /**
     * Get credit note statistics
     */
    public function get_statistics() {
        $stats = array(
            'total_credit_notes' => 0,
            'total_amount' => 0,
            'issued' => 0,
            'posted' => 0,
            'month_total' => 0
        );

        // Total credit notes
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('credit_notes');
        $result = $this->db->get()->row();

        if ($result) {
            $stats['total_credit_notes'] = $result->count;
            $stats['total_amount'] = $result->amount ? $result->amount : 0;
        }

        // Count by status
        $this->db->select('COUNT(*) as count');
        $this->db->from('credit_notes');
        $this->db->where('status', 'issued');
        $stats['issued'] = $this->db->get()->row()->count;

        $this->db->select('COUNT(*) as count');
        $this->db->from('credit_notes');
        $this->db->where('status', 'posted');
        $stats['posted'] = $this->db->get()->row()->count;

        // This month's total
        $this->db->select('SUM(total_amount) as amount');
        $this->db->from('credit_notes');
        $this->db->where('MONTH(credit_note_date)', date('m'));
        $this->db->where('YEAR(credit_note_date)', date('Y'));
        $result = $this->db->get()->row();
        $stats['month_total'] = $result && $result->amount ? $result->amount : 0;

        return $stats;
    }

    /**
     * Get customers for dropdown
     */
    public function get_customers() {
        $this->db->select('customer_id, customer_number, customer_name');
        $this->db->from('customers');
        $this->db->where('is_active', 1);
        $this->db->order_by('customer_name', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get chart of accounts for dropdown
     */
    public function get_accounts() {
        $this->db->select('account_id, account_code, account_name, account_group, account_subgroup');
        $this->db->from('chart_of_accounts');
        $this->db->where('is_active', 1);
        $this->db->order_by('account_code', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get customer account ID (Accounts Receivable)
     */
    private function get_customer_account_id() {
        $this->db->select('account_id');
        $this->db->from('chart_of_accounts');
        $this->db->where('account_code', '1200'); // Accounts Receivable
        $this->db->or_like('account_name', 'Accounts Receivable');
        $this->db->limit(1);

        $result = $this->db->get()->row();
        return $result ? $result->account_id : null;
    }

    /**
     * Get VAT Input account ID
     */
    private function get_vat_input_account_id() {
        $this->db->select('account_id');
        $this->db->from('chart_of_accounts');
        $this->db->where('account_code', '1500'); // VAT Receivable
        $this->db->or_like('account_name', 'VAT Input');
        $this->db->limit(1);

        $result = $this->db->get()->row();
        return $result ? $result->account_id : null;
    }

    /**
     * Get company info
     */
    public function get_company_info() {
        $this->db->select('*');
        $this->db->from('companies');
        $this->db->limit(1);

        return $this->db->get()->row();
    }
}
