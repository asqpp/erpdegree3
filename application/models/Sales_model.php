<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

    private $table = 'quotations';

    public function get_quotations($limit, $offset, $search = null, $status = null) {
        $this->db->select('quotations.*, customers.name as customer_name, policy_types.name as policy_type_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = quotations.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = quotations.policy_type_id', 'left');

        if ($search) {
            $this->db->group_start();
            $this->db->like('quotations.quote_no', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->group_end();
        }
        if ($status) $this->db->where('quotations.status', $status);

        $this->db->order_by('quotations.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_quotations($search = null, $status = null) {
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = quotations.customer_id', 'left');

        if ($search) {
            $this->db->group_start();
            $this->db->like('quotations.quote_no', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->group_end();
        }
        if ($status) $this->db->where('quotations.status', $status);

        return $this->db->count_all_results();
    }

    public function get_quotation($id) {
        $this->db->select('quotations.*, customers.name as customer_name, customers.email as customer_email, policy_types.name as policy_type_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = quotations.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = quotations.policy_type_id', 'left');
        $this->db->where('quotations.id', $id);
        return $this->db->get()->row();
    }

    public function insert_quotation($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update_quotation($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function generate_quote_number() {
        $year = date('Y');
        $this->db->like('quote_no', 'QT-' . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get($this->table)->row();

        $new_num = $last ? ((int)substr($last->quote_no, -5)) + 1 : 1;
        return 'QT-' . $year . '-' . str_pad($new_num, 5, '0', STR_PAD_LEFT);
    }

    public function get_policy_types() {
        $this->db->where('is_active', 1);
        return $this->db->get('policy_types')->result();
    }

    public function get_all_quotations() {
        $this->db->select('quotations.*, customers.name as customer_name, policy_types.name as policy_type_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = quotations.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = quotations.policy_type_id', 'left');
        return $this->db->get()->result();
    }

    public function get_pipeline_statistics() {
        $stats = [];

        // Total quotes
        $stats['total'] = $this->db->count_all($this->table);

        // By status
        $this->db->where('status', 'draft');
        $stats['draft'] = $this->db->count_all_results($this->table);

        $this->db->where('status', 'sent');
        $stats['sent'] = $this->db->count_all_results($this->table);

        $this->db->where('status', 'accepted');
        $stats['accepted'] = $this->db->count_all_results($this->table);

        $this->db->where('status', 'converted');
        $stats['converted'] = $this->db->count_all_results($this->table);

        // Total value
        $this->db->select_sum('premium_amount');
        $result = $this->db->get($this->table)->row();
        $stats['total_value'] = $result->premium_amount ?: 0;

        // Conversion rate
        $stats['conversion_rate'] = $stats['total'] > 0 ? round(($stats['converted'] / $stats['total']) * 100, 2) : 0;

        return $stats;
    }

    public function get_commission_report($from_date, $to_date) {
        $this->db->select('agent_commissions.*, agents.name as agent_name, policies.policy_no');
        $this->db->from('agent_commissions');
        $this->db->join('agents', 'agents.id = agent_commissions.agent_id', 'left');
        $this->db->join('policies', 'policies.id = agent_commissions.policy_id', 'left');
        $this->db->where('agent_commissions.created_at >=', $from_date);
        $this->db->where('agent_commissions.created_at <=', $to_date);
        $this->db->order_by('agent_commissions.created_at', 'DESC');
        return $this->db->get()->result();
    }
}
