<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model {

    // Revenue Report
    public function get_revenue_report($from_date, $to_date) {
        $this->db->select('DATE(issue_date) as date, policy_types.name as type, COUNT(*) as count, SUM(total_premium) as total');
        $this->db->from('policies');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->where('issue_date >=', $from_date);
        $this->db->where('issue_date <=', $to_date);
        $this->db->where('status !=', 'cancelled');
        $this->db->group_by('DATE(issue_date), policy_types.name');
        $this->db->order_by('date', 'DESC');
        return $this->db->get()->result();
    }

    // Expense Report
    public function get_expense_report($from_date, $to_date) {
        // Claims
        $this->db->select('SUM(approved_amount) as total_claims');
        $this->db->where('settlement_date >=', $from_date);
        $this->db->where('settlement_date <=', $to_date);
        $this->db->where('status', 'settled');
        $claims = $this->db->get('claims')->row();

        // Commissions
        $this->db->select('SUM(commission_amount) as total_commissions');
        $this->db->where('payment_date >=', $from_date);
        $this->db->where('payment_date <=', $to_date);
        $commissions = $this->db->get('agent_commissions')->row();

        return [
            'claims' => $claims->total_claims ?: 0,
            'commissions' => $commissions->total_commissions ?: 0,
            'total' => ($claims->total_claims ?: 0) + ($commissions->total_commissions ?: 0)
        ];
    }

    // Policy Report
    public function get_policy_report($from_date, $to_date) {
        $this->db->select('policy_types.name as type, COUNT(*) as count, SUM(sum_insured) as total_si, SUM(total_premium) as total_premium');
        $this->db->from('policies');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->where('issue_date >=', $from_date);
        $this->db->where('issue_date <=', $to_date);
        $this->db->group_by('policy_types.name');
        return $this->db->get()->result();
    }

    // Claims Report
    public function get_claims_report($from_date, $to_date) {
        $this->db->select('claim_types.name as type, COUNT(*) as count, SUM(claim_amount) as total_claimed, SUM(approved_amount) as total_approved');
        $this->db->from('claims');
        $this->db->join('claim_types', 'claim_types.id = claims.claim_type_id', 'left');
        $this->db->where('claim_date >=', $from_date);
        $this->db->where('claim_date <=', $to_date);
        $this->db->group_by('claim_types.name');
        return $this->db->get()->result();
    }

    // Sales Report
    public function get_sales_report($from_date, $to_date) {
        $this->db->select('DATE(created_at) as date, COUNT(*) as count, SUM(premium_amount) as total_value, status');
        $this->db->where('created_at >=', $from_date);
        $this->db->where('created_at <=', $to_date);
        $this->db->group_by('DATE(created_at), status');
        $this->db->order_by('date', 'DESC');
        return $this->db->get('quotations')->result();
    }

    // Agent Performance
    public function get_agent_performance($from_date, $to_date) {
        $this->db->select('agents.name, COUNT(policies.id) as policies_sold, SUM(policies.total_premium) as total_premium, SUM(agent_commissions.commission_amount) as total_commission');
        $this->db->from('agents');
        $this->db->join('policies', 'policies.agent_id = agents.id', 'left');
        $this->db->join('agent_commissions', 'agent_commissions.agent_id = agents.id', 'left');
        $this->db->where('policies.issue_date >=', $from_date);
        $this->db->where('policies.issue_date <=', $to_date);
        $this->db->group_by('agents.id');
        $this->db->order_by('total_premium', 'DESC');
        return $this->db->get()->result();
    }

    // VAT Compliance
    public function get_vat_compliance() {
        $current_month = date('Y-m-01');
        $current_month_end = date('Y-m-t');

        $this->db->select_sum('vat_amount');
        $this->db->where('issue_date >=', $current_month);
        $this->db->where('issue_date <=', $current_month_end);
        $result = $this->db->get('policies')->row();

        return [
            'period' => date('F Y'),
            'output_vat' => $result->vat_amount ?: 0,
            'input_vat' => 0,
            'net_vat' => $result->vat_amount ?: 0
        ];
    }

    // Insurance Authority Compliance
    public function get_ia_compliance() {
        $quarter_start = date('Y-m-01', strtotime('first day of -2 months'));
        $quarter_end = date('Y-m-t');

        $this->db->select('COUNT(*) as policies_issued, SUM(total_premium) as total_premium, SUM(sum_insured) as total_si');
        $this->db->where('issue_date >=', $quarter_start);
        $this->db->where('issue_date <=', $quarter_end);
        $policies = $this->db->get('policies')->row();

        $this->db->select('COUNT(*) as claims_registered, SUM(approved_amount) as claims_paid');
        $this->db->where('claim_date >=', $quarter_start);
        $this->db->where('claim_date <=', $quarter_end);
        $claims = $this->db->get('claims')->row();

        return [
            'period' => 'Q' . ceil(date('m') / 3) . ' ' . date('Y'),
            'policies_issued' => $policies->policies_issued,
            'total_premium' => $policies->total_premium ?: 0,
            'total_si' => $policies->total_si ?: 0,
            'claims_registered' => $claims->claims_registered,
            'claims_paid' => $claims->claims_paid ?: 0
        ];
    }

    // Customer Analytics
    public function get_customer_analytics() {
        // Total customers
        $total = $this->db->count_all('customers');

        // New this month
        $this->db->where('created_at >=', date('Y-m-01'));
        $new_this_month = $this->db->count_all_results('customers');

        // By type
        $this->db->select('customer_type, COUNT(*) as count');
        $this->db->group_by('customer_type');
        $by_type = $this->db->get('customers')->result();

        // Top customers by premium
        $this->db->select('customers.name, SUM(policies.total_premium) as total_premium, COUNT(policies.id) as policy_count');
        $this->db->from('customers');
        $this->db->join('policies', 'policies.customer_id = customers.id', 'left');
        $this->db->group_by('customers.id');
        $this->db->order_by('total_premium', 'DESC');
        $this->db->limit(10);
        $top_customers = $this->db->get()->result();

        return [
            'total' => $total,
            'new_this_month' => $new_this_month,
            'by_type' => $by_type,
            'top_customers' => $top_customers
        ];
    }

    // Export Report
    public function export_report($report_type, $from_date, $to_date) {
        switch ($report_type) {
            case 'policies':
                return $this->get_policy_export($from_date, $to_date);
            case 'claims':
                return $this->get_claims_export($from_date, $to_date);
            case 'financial':
                return $this->get_financial_export($from_date, $to_date);
            default:
                return [];
        }
    }

    private function get_policy_export($from_date, $to_date) {
        $this->db->select('policies.policy_no, customers.name as customer, policy_types.name as type, policies.issue_date, policies.expiry_date, policies.sum_insured, policies.total_premium, policies.status');
        $this->db->from('policies');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->where('issue_date >=', $from_date);
        $this->db->where('issue_date <=', $to_date);
        return $this->db->get()->result();
    }

    private function get_claims_export($from_date, $to_date) {
        $this->db->select('claims.claim_no, policies.policy_no, customers.name as customer, claim_types.name as type, claims.claim_date, claims.claim_amount, claims.approved_amount, claims.status');
        $this->db->from('claims');
        $this->db->join('policies', 'policies.id = claims.policy_id', 'left');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('claim_types', 'claim_types.id = claims.claim_type_id', 'left');
        $this->db->where('claim_date >=', $from_date);
        $this->db->where('claim_date <=', $to_date);
        return $this->db->get()->result();
    }

    private function get_financial_export($from_date, $to_date) {
        $this->db->select('DATE(issue_date) as date, SUM(total_premium) as revenue, "Policy Premium" as category');
        $this->db->where('issue_date >=', $from_date);
        $this->db->where('issue_date <=', $to_date);
        $this->db->group_by('DATE(issue_date)');
        return $this->db->get('policies')->result();
    }
}
