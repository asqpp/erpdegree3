<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting_model extends CI_Model {

    // Chart of Accounts
    public function get_chart_of_accounts() {
        $this->db->order_by('account_code', 'ASC');
        return $this->db->get('chart_of_accounts')->result();
    }

    public function insert_account($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('chart_of_accounts', $data);
    }

    // Journal Entries
    public function get_journal_entries($limit, $offset) {
        $this->db->select('journal_entries.*, users.name as created_by_name');
        $this->db->from('journal_entries');
        $this->db->join('users', 'users.id = journal_entries.created_by', 'left');
        $this->db->order_by('entry_date', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_journal_entries() {
        return $this->db->count_all('journal_entries');
    }

    public function insert_journal_entry($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('journal_entries', $data);
        return $this->db->insert_id();
    }

    public function insert_journal_line($data) {
        return $this->db->insert('journal_entry_lines', $data);
    }

    public function generate_entry_number() {
        $year = date('Y');
        $this->db->like('entry_no', 'JE-' . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('journal_entries')->row();

        $new_num = $last ? ((int)substr($last->entry_no, -5)) + 1 : 1;
        return 'JE-' . $year . '-' . str_pad($new_num, 5, '0', STR_PAD_LEFT);
    }

    // Accounts Receivable
    public function get_accounts_receivable() {
        $this->db->select('premium_schedule.*, policies.policy_no, customers.name as customer_name');
        $this->db->from('premium_schedule');
        $this->db->join('policies', 'policies.id = premium_schedule.policy_id', 'left');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->where('premium_schedule.status', 'pending');
        $this->db->order_by('due_date', 'ASC');
        return $this->db->get()->result();
    }

    // Accounts Payable
    public function get_accounts_payable() {
        $this->db->select('agent_commissions.*, agents.name as agent_name');
        $this->db->from('agent_commissions');
        $this->db->join('agents', 'agents.id = agent_commissions.agent_id', 'left');
        $this->db->where('agent_commissions.payment_status', 'pending');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    // Financial Statements
    public function get_revenue($from_date, $to_date) {
        $this->db->select_sum('total_premium');
        $this->db->where('issue_date >=', $from_date);
        $this->db->where('issue_date <=', $to_date);
        $this->db->where('status !=', 'cancelled');
        $result = $this->db->get('policies')->row();
        return $result->total_premium ?: 0;
    }

    public function get_expenses($from_date, $to_date) {
        // Claims settled
        $this->db->select_sum('approved_amount');
        $this->db->where('settlement_date >=', $from_date);
        $this->db->where('settlement_date <=', $to_date);
        $this->db->where('status', 'settled');
        $result = $this->db->get('claims')->row();
        $claims = $result->approved_amount ?: 0;

        // Commissions paid
        $this->db->select_sum('commission_amount');
        $this->db->where('payment_date >=', $from_date);
        $this->db->where('payment_date <=', $to_date);
        $this->db->where('payment_status', 'paid');
        $result = $this->db->get('agent_commissions')->row();
        $commissions = $result->commission_amount ?: 0;

        return $claims + $commissions;
    }

    public function get_assets($as_of_date) {
        // Cash + AR
        $ar = $this->get_total_ar($as_of_date);
        return $ar;
    }

    public function get_liabilities($as_of_date) {
        // AP
        $ap = $this->get_total_ap($as_of_date);
        return $ap;
    }

    public function get_equity($as_of_date) {
        $assets = $this->get_assets($as_of_date);
        $liabilities = $this->get_liabilities($as_of_date);
        return $assets - $liabilities;
    }

    private function get_total_ar($as_of_date) {
        $this->db->select_sum('amount');
        $this->db->where('due_date <=', $as_of_date);
        $this->db->where('status', 'pending');
        $result = $this->db->get('premium_schedule')->row();
        return $result->amount ?: 0;
    }

    private function get_total_ap($as_of_date) {
        $this->db->select_sum('commission_amount');
        $this->db->where('created_at <=', $as_of_date);
        $this->db->where('payment_status', 'pending');
        $result = $this->db->get('agent_commissions')->row();
        return $result->commission_amount ?: 0;
    }

    // VAT Reports
    public function get_vat_summary($from_date, $to_date) {
        // Output VAT (on sales/premiums)
        $this->db->select_sum('vat_amount');
        $this->db->where('issue_date >=', $from_date);
        $this->db->where('issue_date <=', $to_date);
        $this->db->where('status !=', 'cancelled');
        $result = $this->db->get('policies')->row();
        $output_vat = $result->vat_amount ?: 0;

        // Input VAT (on purchases) - simplified
        $input_vat = 0;

        return [
            'output_vat' => $output_vat,
            'input_vat' => $input_vat,
            'net_vat' => $output_vat - $input_vat
        ];
    }
}
