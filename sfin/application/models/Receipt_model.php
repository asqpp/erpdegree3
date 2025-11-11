<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Receipt Model
 * Handles database operations for receipt and payment vouchers
 */
class Receipt_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get receipts with pagination and filters
     */
    public function get_receipts($limit, $offset, $filters = array()) {
        $this->db->select('rv.*, u.username as created_by_name');
        $this->db->from('receipt_vouchers rv');
        $this->db->join('users u', 'u.user_id = rv.created_by', 'left');

        // Apply filters
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('rv.voucher_number', $filters['search']);
            $this->db->or_like('rv.party_name', $filters['search']);
            $this->db->or_like('rv.narration', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['voucher_type'])) {
            $this->db->where('rv.voucher_type', $filters['voucher_type']);
        }

        if (!empty($filters['payment_method'])) {
            $this->db->where('rv.payment_method', $filters['payment_method']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('rv.voucher_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('rv.voucher_date <=', $filters['to_date']);
        }

        $this->db->order_by('rv.voucher_date', 'DESC');
        $this->db->order_by('rv.receipt_id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count receipts for pagination
     */
    public function count_receipts($filters = array()) {
        $this->db->from('receipt_vouchers rv');

        // Apply same filters as get_receipts
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('rv.voucher_number', $filters['search']);
            $this->db->or_like('rv.party_name', $filters['search']);
            $this->db->or_like('rv.narration', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['voucher_type'])) {
            $this->db->where('rv.voucher_type', $filters['voucher_type']);
        }

        if (!empty($filters['payment_method'])) {
            $this->db->where('rv.payment_method', $filters['payment_method']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('rv.voucher_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('rv.voucher_date <=', $filters['to_date']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get single receipt by ID
     */
    public function get_receipt($id) {
        $this->db->select('rv.*, u.username as created_by_name, ba.account_name as bank_account_name');
        $this->db->from('receipt_vouchers rv');
        $this->db->join('users u', 'u.user_id = rv.created_by', 'left');
        $this->db->join('chart_of_accounts ba', 'ba.account_id = rv.bank_account_id', 'left');
        $this->db->where('rv.receipt_id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get receipt items
     */
    public function get_receipt_items($receipt_id) {
        $this->db->select('ri.*, coa.account_name, coa.account_code');
        $this->db->from('receipt_items ri');
        $this->db->join('chart_of_accounts coa', 'coa.account_id = ri.account_id', 'left');
        $this->db->where('ri.receipt_id', $receipt_id);
        $this->db->order_by('ri.item_id', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Insert receipt voucher with items and journal entry
     */
    public function insert_receipt($receipt_data, $items) {
        $this->db->trans_start();

        // Insert receipt voucher
        $this->db->insert('receipt_vouchers', $receipt_data);
        $receipt_id = $this->db->insert_id();

        // Insert receipt items
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                $item_data = array(
                    'receipt_id' => $receipt_id,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'amount' => $item['amount']
                );
                $this->db->insert('receipt_items', $item_data);
            }
        }

        // Create journal entry
        $this->create_journal_entry($receipt_id, $receipt_data, $items);

        $this->db->trans_complete();

        return $this->db->trans_status() ? $receipt_id : false;
    }

    /**
     * Insert payment voucher with items and journal entry
     */
    public function insert_payment($payment_data, $items) {
        $this->db->trans_start();

        // Insert payment voucher
        $this->db->insert('receipt_vouchers', $payment_data);
        $payment_id = $this->db->insert_id();

        // Insert payment items
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                $item_data = array(
                    'receipt_id' => $payment_id,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'amount' => $item['amount']
                );
                $this->db->insert('receipt_items', $item_data);
            }
        }

        // Create journal entry
        $this->create_journal_entry($payment_id, $payment_data, $items);

        $this->db->trans_complete();

        return $this->db->trans_status() ? $payment_id : false;
    }

    /**
     * Create journal entry for receipt/payment
     */
    private function create_journal_entry($receipt_id, $receipt_data, $items) {
        // Create journal entry header
        $je_data = array(
            'entry_number' => $this->generate_je_number(),
            'entry_date' => $receipt_data['voucher_date'],
            'entry_type' => $receipt_data['voucher_type'] === 'receipt' ? 'Receipt Voucher' : 'Payment Voucher',
            'reference_type' => $receipt_data['voucher_type'] === 'receipt' ? 'Receipt' : 'Payment',
            'reference_id' => $receipt_id,
            'reference_number' => $receipt_data['voucher_number'],
            'narration' => $receipt_data['narration'],
            'total_debit' => $receipt_data['total_amount'],
            'total_credit' => $receipt_data['total_amount'],
            'status' => 'posted',
            'created_by' => $receipt_data['created_by'],
            'company_id' => $receipt_data['company_id'],
            'branch_id' => $receipt_data['branch_id'],
            'financial_year_id' => $receipt_data['financial_year_id']
        );

        $this->db->insert('journal_entries', $je_data);
        $je_id = $this->db->insert_id();

        // Create journal entry lines
        if ($receipt_data['voucher_type'] === 'receipt') {
            // Debit: Bank/Cash account
            $this->db->insert('journal_entry_lines', array(
                'journal_entry_id' => $je_id,
                'account_id' => $receipt_data['bank_account_id'],
                'debit_amount' => $receipt_data['total_amount'],
                'credit_amount' => 0,
                'description' => 'Receipt from ' . $receipt_data['party_name']
            ));

            // Credit: Income/Revenue accounts (from items)
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    $this->db->insert('journal_entry_lines', array(
                        'journal_entry_id' => $je_id,
                        'account_id' => $item['account_id'],
                        'debit_amount' => 0,
                        'credit_amount' => $item['amount'],
                        'description' => $item['description']
                    ));
                }
            }
        } else {
            // Payment voucher
            // Debit: Expense accounts (from items)
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    $this->db->insert('journal_entry_lines', array(
                        'journal_entry_id' => $je_id,
                        'account_id' => $item['account_id'],
                        'debit_amount' => $item['amount'],
                        'credit_amount' => 0,
                        'description' => $item['description']
                    ));
                }
            }

            // Credit: Bank/Cash account
            $this->db->insert('journal_entry_lines', array(
                'journal_entry_id' => $je_id,
                'account_id' => $receipt_data['bank_account_id'],
                'debit_amount' => 0,
                'credit_amount' => $receipt_data['total_amount'],
                'description' => 'Payment to ' . $receipt_data['party_name']
            ));
        }

        return $je_id;
    }

    /**
     * Delete receipt voucher
     */
    public function delete_receipt($id) {
        $this->db->trans_start();

        // Delete receipt items
        $this->db->where('receipt_id', $id);
        $this->db->delete('receipt_items');

        // Delete journal entry
        $receipt = $this->get_receipt($id);
        if ($receipt) {
            $this->db->where('reference_type', $receipt->voucher_type === 'receipt' ? 'Receipt' : 'Payment');
            $this->db->where('reference_id', $id);
            $je = $this->db->get('journal_entries')->row();

            if ($je) {
                $this->db->where('journal_entry_id', $je->journal_entry_id);
                $this->db->delete('journal_entry_lines');

                $this->db->where('journal_entry_id', $je->journal_entry_id);
                $this->db->delete('journal_entries');
            }
        }

        // Delete receipt voucher
        $this->db->where('receipt_id', $id);
        $this->db->delete('receipt_vouchers');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Generate voucher number (RV-YYYY-NNNNN or PV-YYYY-NNNNN)
     */
    public function generate_voucher_number($type = 'receipt') {
        $prefix = $type === 'receipt' ? 'RV' : 'PV';
        $year = date('Y');

        // Get last voucher number for this year and type
        $this->db->select('voucher_number');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', $type);
        $this->db->like('voucher_number', $prefix . '-' . $year, 'after');
        $this->db->order_by('receipt_id', 'DESC');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        if ($result) {
            // Extract number and increment
            $parts = explode('-', $result->voucher_number);
            $number = isset($parts[2]) ? intval($parts[2]) + 1 : 1;
        } else {
            $number = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
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
     * Get journal entry for receipt
     */
    public function get_journal_entry($receipt_id) {
        $receipt = $this->get_receipt($receipt_id);

        if (!$receipt) {
            return null;
        }

        $this->db->select('je.*, u.username as created_by_name');
        $this->db->from('journal_entries je');
        $this->db->join('users u', 'u.user_id = je.created_by', 'left');
        $this->db->where('je.reference_type', $receipt->voucher_type === 'receipt' ? 'Receipt' : 'Payment');
        $this->db->where('je.reference_id', $receipt_id);

        $je = $this->db->get()->row();

        if ($je) {
            // Get journal entry lines
            $this->db->select('jel.*, coa.account_name, coa.account_code');
            $this->db->from('journal_entry_lines jel');
            $this->db->join('chart_of_accounts coa', 'coa.account_id = jel.account_id', 'left');
            $this->db->where('jel.journal_entry_id', $je->journal_entry_id);
            $je->lines = $this->db->get()->result();
        }

        return $je;
    }

    /**
     * Get receipt statistics
     */
    public function get_statistics() {
        $stats = array(
            'total_receipts' => 0,
            'total_amount' => 0,
            'today_receipts' => 0,
            'today_amount' => 0,
            'month_receipts' => 0,
            'month_amount' => 0
        );

        // Total receipts
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', 'receipt');
        $result = $this->db->get()->row();

        if ($result) {
            $stats['total_receipts'] = $result->count;
            $stats['total_amount'] = $result->amount ? $result->amount : 0;
        }

        // Today's receipts
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', 'receipt');
        $this->db->where('DATE(voucher_date)', date('Y-m-d'));
        $result = $this->db->get()->row();

        if ($result) {
            $stats['today_receipts'] = $result->count;
            $stats['today_amount'] = $result->amount ? $result->amount : 0;
        }

        // This month's receipts
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', 'receipt');
        $this->db->where('MONTH(voucher_date)', date('m'));
        $this->db->where('YEAR(voucher_date)', date('Y'));
        $result = $this->db->get()->row();

        if ($result) {
            $stats['month_receipts'] = $result->count;
            $stats['month_amount'] = $result->amount ? $result->amount : 0;
        }

        return $stats;
    }

    /**
     * Get payment statistics
     */
    public function get_payment_statistics() {
        $stats = array(
            'total_payments' => 0,
            'total_amount' => 0,
            'today_payments' => 0,
            'today_amount' => 0,
            'month_payments' => 0,
            'month_amount' => 0
        );

        // Total payments
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', 'payment');
        $result = $this->db->get()->row();

        if ($result) {
            $stats['total_payments'] = $result->count;
            $stats['total_amount'] = $result->amount ? $result->amount : 0;
        }

        // Today's payments
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', 'payment');
        $this->db->where('DATE(voucher_date)', date('Y-m-d'));
        $result = $this->db->get()->row();

        if ($result) {
            $stats['today_payments'] = $result->count;
            $stats['today_amount'] = $result->amount ? $result->amount : 0;
        }

        // This month's payments
        $this->db->select('COUNT(*) as count, SUM(total_amount) as amount');
        $this->db->from('receipt_vouchers');
        $this->db->where('voucher_type', 'payment');
        $this->db->where('MONTH(voucher_date)', date('m'));
        $this->db->where('YEAR(voucher_date)', date('Y'));
        $result = $this->db->get()->row();

        if ($result) {
            $stats['month_payments'] = $result->count;
            $stats['month_amount'] = $result->amount ? $result->amount : 0;
        }

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
     * Get bank accounts for dropdown
     */
    public function get_bank_accounts() {
        $this->db->select('account_id, account_code, account_name');
        $this->db->from('chart_of_accounts');
        $this->db->where('account_group', 'Assets');
        $this->db->where_in('account_subgroup', array('Cash', 'Bank'));
        $this->db->where('is_active', 1);
        $this->db->order_by('account_name', 'ASC');

        return $this->db->get()->result();
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
