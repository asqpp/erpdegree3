<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Claim_model extends CI_Model {

    private $table = 'claims';

    public function get_claims($limit, $offset, $search = null, $status = null, $claim_type = null) {
        $this->db->select('claims.*, policies.policy_no, customers.name as customer_name, claim_types.name as claim_type_name');
        $this->db->from($this->table);
        $this->db->join('policies', 'policies.id = claims.policy_id', 'left');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('claim_types', 'claim_types.id = claims.claim_type_id', 'left');

        if ($search) {
            $this->db->group_start();
            $this->db->like('claims.claim_no', $search);
            $this->db->or_like('policies.policy_no', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->group_end();
        }
        if ($status) $this->db->where('claims.status', $status);
        if ($claim_type) $this->db->where('claims.claim_type_id', $claim_type);

        $this->db->order_by('claims.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_claims($search = null, $status = null, $claim_type = null) {
        $this->db->from($this->table);
        $this->db->join('policies', 'policies.id = claims.policy_id', 'left');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');

        if ($search) {
            $this->db->group_start();
            $this->db->like('claims.claim_no', $search);
            $this->db->or_like('policies.policy_no', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->group_end();
        }
        if ($status) $this->db->where('claims.status', $status);
        if ($claim_type) $this->db->where('claims.claim_type_id', $claim_type);

        return $this->db->count_all_results();
    }

    public function get_claim($id) {
        $this->db->select('claims.*, policies.policy_no, policies.sum_insured, customers.name as customer_name, claim_types.name as claim_type_name');
        $this->db->from($this->table);
        $this->db->join('policies', 'policies.id = claims.policy_id', 'left');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('claim_types', 'claim_types.id = claims.claim_type_id', 'left');
        $this->db->where('claims.id', $id);
        return $this->db->get()->row();
    }

    public function insert_claim($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update_claim($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function generate_claim_number() {
        $year = date('Y');
        $this->db->like('claim_no', 'CLM-' . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get($this->table)->row();

        $new_num = $last ? ((int)substr($last->claim_no, -5)) + 1 : 1;
        return 'CLM-' . $year . '-' . str_pad($new_num, 5, '0', STR_PAD_LEFT);
    }

    public function get_claim_types() {
        $this->db->where('is_active', 1);
        return $this->db->get('claim_types')->result();
    }

    public function get_claim_documents($claim_id) {
        $this->db->where('claim_id', $claim_id);
        return $this->db->get('claim_documents')->result();
    }

    public function get_claim_activities($claim_id) {
        $this->db->select('audit_logs.*, users.name as user_name');
        $this->db->from('audit_logs');
        $this->db->join('users', 'users.id = audit_logs.user_id', 'left');
        $this->db->where('table_name', 'claims');
        $this->db->where('record_id', $claim_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function log_activity($claim_id, $action) {
        $data = [
            'table_name' => 'claims',
            'record_id' => $claim_id,
            'action' => $action,
            'user_id' => $this->session->userdata('user_id') ?: 1,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('audit_logs', $data);
    }

    public function get_all_claims() {
        $this->db->select('claims.*, policies.policy_no, customers.name as customer_name, claim_types.name as claim_type_name');
        $this->db->from($this->table);
        $this->db->join('policies', 'policies.id = claims.policy_id', 'left');
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('claim_types', 'claim_types.id = claims.claim_type_id', 'left');
        return $this->db->get()->result();
    }

    public function get_statistics() {
        $stats = [];
        $stats['total'] = $this->db->count_all($this->table);

        $this->db->where('status', 'registered');
        $stats['registered'] = $this->db->count_all_results($this->table);

        $this->db->where('status', 'approved');
        $stats['approved'] = $this->db->count_all_results($this->table);

        $this->db->where('status', 'settled');
        $stats['settled'] = $this->db->count_all_results($this->table);

        $this->db->select_sum('claim_amount');
        $result = $this->db->get($this->table)->row();
        $stats['total_claimed'] = $result->claim_amount ?: 0;

        $this->db->select_sum('approved_amount');
        $result = $this->db->get($this->table)->row();
        $stats['total_approved'] = $result->approved_amount ?: 0;

        return $stats;
    }
}
