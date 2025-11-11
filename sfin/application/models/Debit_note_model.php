<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Debit Note Model
 * Handles database operations for debit notes
 */
class Debit_note_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get debit notes with pagination and filters
     */
    public function get_debit_notes($limit, $offset, $filters = array()) {
        $this->db->select('dn.*, u.username as created_by_name');
        $this->db->from('debit_notes dn');
        $this->db->join('users u', 'u.user_id = dn.created_by', 'left');

        // Apply filters
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('dn.debit_note_number', $filters['search']);
            $this->db->or_like('dn.supplier_name', $filters['search']);
            $this->db->or_like('dn.reference_number', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('dn.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('dn.debit_note_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('dn.debit_note_date <=', $filters['to_date']);
        }

        $this->db->order_by('dn.debit_note_date', 'DESC');
        $this->db->order_by('dn.debit_note_id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count debit notes for pagination
     */
    public function count_debit_notes($filters = array()) {
        $this->db->from('debit_notes dn');

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('dn.debit_note_number', $filters['search']);
            $this->db->or_like('dn.supplier_name', $filters['search']);
            $this->db->or_like('dn.reference_number', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('dn.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('dn.debit_note_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('dn.debit_note_date <=', $filters['to_date']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get single debit note by ID
     */
    public function get_debit_note($id) {
        $this->db->select('dn.*, u.username as created_by_name');
        $this->db->from('debit_notes dn');
        $this->db->join('users u', 'u.user_id = dn.created_by', 'left');
        $this->db->where('dn.debit_note_id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get debit note items
     */
    public function get_debit_note_items($debit_note_id) {
        $this->db->select('dni.*, coa.account_name, coa.account_code');
        $this->db->from('debit_note_items dni');
        $this->db->join('chart_of_accounts coa', 'coa.account_id = dni.account_id', 'left');
        $this->db->where('dni.debit_note_id', $debit_note_id);
        $this->db->order_by('dni.item_id', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Insert debit note with items
     */
    public function insert_debit_note($debit_note_data, $items) {
        $this->db->trans_start();

        // Insert debit note
        $this->db->insert('debit_notes', $debit_note_data);
        $debit_note_id = $this->db->insert_id();

        // Insert debit note items
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                $item_data = array(
                    'debit_note_id' => $debit_note_id,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'quantity' => isset($item['quantity']) ? $item['quantity'] : 1.00,
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['amount']
                );
                $this->db->insert('debit_note_items', $item_data);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $debit_note_id : false;
    }

    /**
     * Post debit note to accounts (create journal entry)
     */
    public function post_debit_note($debit_note_id) {
        $debit_note = $this->get_debit_note($debit_note_id);

        if (!$debit_note || $debit_note->status !== 'issued') {
            return false;
        }

        $this->db->trans_start();

        // Create journal entry
        $je_data = array(
            'entry_number' => $this->generate_je_number(),
            'entry_date' => $debit_note->debit_note_date,
            'entry_type' => 'Debit Note',
            'reference_type' => 'Debit Note',
            'reference_id' => $debit_note_id,
            'reference_number' => $debit_note->debit_note_number,
            'narration' => 'Debit Note - ' . $debit_note->supplier_name . ' - ' . $debit_note->reason,
            'total_debit' => $debit_note->total_amount,
            'total_credit' => $debit_note->total_amount,
            'status' => 'posted',
            'created_by' => $this->session->userdata('user_id'),
            'company_id' => $debit_note->company_id,
            'branch_id' => $debit_note->branch_id,
            'financial_year_id' => $debit_note->financial_year_id
        );

        $this->db->insert('journal_entries', $je_data);
        $je_id = $this->db->insert_id();

        // Get items
        $items = $this->get_debit_note_items($debit_note_id);

        // Debit: Supplier Account (reducing payable)
        $this->db->insert('journal_entry_lines', array(
            'journal_entry_id' => $je_id,
            'account_id' => $this->get_supplier_account_id(),
            'debit_amount' => $debit_note->total_amount,
            'credit_amount' => 0,
            'description' => 'Debit Note to ' . $debit_note->supplier_name
        ));

        // Credit: Various accounts (from items) + VAT
        if ($items) {
            foreach ($items as $item) {
                $this->db->insert('journal_entry_lines', array(
                    'journal_entry_id' => $je_id,
                    'account_id' => $item->account_id,
                    'debit_amount' => 0,
                    'credit_amount' => $item->amount,
                    'description' => $item->description
                ));
            }
        }

        // Credit: VAT Output account
        if ($debit_note->vat_amount > 0) {
            $this->db->insert('journal_entry_lines', array(
                'journal_entry_id' => $je_id,
                'account_id' => $this->get_vat_output_account_id(),
                'debit_amount' => 0,
                'credit_amount' => $debit_note->vat_amount,
                'description' => 'VAT on Debit Note'
            ));
        }

        // Update debit note status
        $this->db->where('debit_note_id', $debit_note_id);
        $this->db->update('debit_notes', array('status' => 'posted'));

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Delete debit note
     */
    public function delete_debit_note($id) {
        $this->db->trans_start();

        // Delete debit note items
        $this->db->where('debit_note_id', $id);
        $this->db->delete('debit_note_items');

        // Delete debit note
        $this->db->where('debit_note_id', $id);
        $this->db->delete('debit_notes');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Generate debit note number (DN-YYYY-NNNNN)
     */
    public function generate_debit_note_number() {
        $year = date('Y');

        $this->db->select('debit_note_number');
        $this->db->from('debit_notes');
        $this->db->like('debit_note_number', 'DN-' . $year, 'after');
        $this->db->order_by('debit_note_id', 'DESC');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        if ($result) {
            $parts = explode('-', $result->debit_note_number);
            $number = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
        } else {
            $number = 1;
        }

        return 'DN-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
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
     * Get journal entry for debit note
     */
    public function get_journal_entry($debit_note_id) {
        $this->db->select('je.*, u.username as created_by_name');
        $this->db->from('journal_entries je');
        $this->db->join('users u', 'u.user_id = je.created_by', 'left');
        $this->db->where('je.reference_type', 'Debit Note');
        $this->db->where('je.reference_id', $debit_note_id);

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
     * Get debit note statistics
     */
    public function get_statistics() {
        $stats = array(
            'total_debit_notes' => 0,
            'total_amount' => 0,
            'issued' => 0,
            'posted' => 0,
            'month_total' => 0
        );

        // Total debit notes
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('debit_notes');
        $result = $this->db->get()->row();

        if ($result) {
            $stats['total_debit_notes'] = $result->count;
            $stats['total_amount'] = $result->amount ? $result->amount : 0;
        }

        // Count by status
        $this->db->select('COUNT(*) as count');
        $this->db->from('debit_notes');
        $this->db->where('status', 'issued');
        $stats['issued'] = $this->db->get()->row()->count;

        $this->db->select('COUNT(*) as count');
        $this->db->from('debit_notes');
        $this->db->where('status', 'posted');
        $stats['posted'] = $this->db->get()->row()->count;

        // This month's total
        $this->db->select('SUM(total_amount) as amount');
        $this->db->from('debit_notes');
        $this->db->where('MONTH(debit_note_date)', date('m'));
        $this->db->where('YEAR(debit_note_date)', date('Y'));
        $result = $this->db->get()->row();
        $stats['month_total'] = $result && $result->amount ? $result->amount : 0;

        return $stats;
    }

    /**
     * Get suppliers for dropdown
     */
    public function get_suppliers() {
        $this->db->select('supplier_id, supplier_name');
        $this->db->from('suppliers');
        $this->db->where('is_active', 1);
        $this->db->order_by('supplier_name', 'ASC');

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
     * Get supplier account ID (Accounts Payable)
     */
    private function get_supplier_account_id() {
        $this->db->select('account_id');
        $this->db->from('chart_of_accounts');
        $this->db->where('account_code', '2100'); // Accounts Payable
        $this->db->or_like('account_name', 'Accounts Payable');
        $this->db->limit(1);

        $result = $this->db->get()->row();
        return $result ? $result->account_id : null;
    }

    /**
     * Get VAT Output account ID
     */
    private function get_vat_output_account_id() {
        $this->db->select('account_id');
        $this->db->from('chart_of_accounts');
        $this->db->where('account_code', '2300'); // VAT Payable
        $this->db->or_like('account_name', 'VAT Output');
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
