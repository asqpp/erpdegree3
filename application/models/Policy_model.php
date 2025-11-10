<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Policy Model
 * Handles all policy-related database operations
 */
class Policy_model extends CI_Model {

    private $table = 'policies';
    private $endorsements_table = 'policy_endorsements';
    private $schedule_table = 'premium_schedule';
    private $cancellations_table = 'policy_cancellations';

    /**
     * Get policies with pagination and filters
     */
    public function get_policies($limit, $offset, $search = null, $status = null, $policy_type = null, $customer_id = null, $date_from = null, $date_to = null) {
        $this->db->select('policies.*, customers.name as customer_name, policy_types.name as policy_type_name, currencies.symbol as currency_symbol, agents.name as agent_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->join('currencies', 'currencies.id = policies.currency_id', 'left');
        $this->db->join('agents', 'agents.id = policies.agent_id', 'left');

        // Apply filters
        if ($search) {
            $this->db->group_start();
            $this->db->like('policies.policy_no', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->or_like('policies.vehicle_plate_no', $search);
            $this->db->group_end();
        }

        if ($status) {
            $this->db->where('policies.status', $status);
        }

        if ($policy_type) {
            $this->db->where('policies.policy_type_id', $policy_type);
        }

        if ($customer_id) {
            $this->db->where('policies.customer_id', $customer_id);
        }

        if ($date_from) {
            $this->db->where('policies.issue_date >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('policies.issue_date <=', $date_to);
        }

        $this->db->order_by('policies.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count policies with filters
     */
    public function count_policies($search = null, $status = null, $policy_type = null, $customer_id = null, $date_from = null, $date_to = null) {
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');

        if ($search) {
            $this->db->group_start();
            $this->db->like('policies.policy_no', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->or_like('policies.vehicle_plate_no', $search);
            $this->db->group_end();
        }

        if ($status) {
            $this->db->where('policies.status', $status);
        }

        if ($policy_type) {
            $this->db->where('policies.policy_type_id', $policy_type);
        }

        if ($customer_id) {
            $this->db->where('policies.customer_id', $customer_id);
        }

        if ($date_from) {
            $this->db->where('policies.issue_date >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('policies.issue_date <=', $date_to);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get single policy by ID
     */
    public function get_policy($id) {
        $this->db->select('policies.*, customers.name as customer_name, customers.email as customer_email, customers.phone as customer_phone, policy_types.name as policy_type_name, currencies.code as currency_code, currencies.symbol as currency_symbol, agents.name as agent_name, brokers.name as broker_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->join('currencies', 'currencies.id = policies.currency_id', 'left');
        $this->db->join('agents', 'agents.id = policies.agent_id', 'left');
        $this->db->join('brokers', 'brokers.id = policies.broker_id', 'left');
        $this->db->where('policies.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Insert new policy
     */
    public function insert_policy($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update policy
     */
    public function update_policy($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Generate unique policy number
     */
    public function generate_policy_number($policy_type_id) {
        $policy_type = $this->get_policy_type($policy_type_id);
        $prefix = $policy_type ? strtoupper(substr($policy_type->code, 0, 3)) : 'POL';
        $year = date('Y');

        // Get last policy number for this type and year
        $this->db->select('policy_no');
        $this->db->from($this->table);
        $this->db->like('policy_no', $prefix . '-' . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();

        if ($last) {
            $last_num = (int) substr($last->policy_no, -5);
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($new_num, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get policy types
     */
    public function get_policy_types() {
        $this->db->where('is_active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get('policy_types')->result();
    }

    /**
     * Get single policy type
     */
    public function get_policy_type($id) {
        return $this->db->get_where('policy_types', ['id' => $id])->row();
    }

    /**
     * Get currencies
     */
    public function get_currencies() {
        $this->db->where('is_active', 1);
        $this->db->order_by('is_base', 'DESC');
        $this->db->order_by('name', 'ASC');
        return $this->db->get('currencies')->result();
    }

    /**
     * Get agents
     */
    public function get_agents() {
        $this->db->where('is_active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get('agents')->result();
    }

    /**
     * Get brokers
     */
    public function get_brokers() {
        $this->db->where('is_active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get('brokers')->result();
    }

    /**
     * Get premium schedule
     */
    public function get_premium_schedule($policy_id) {
        $this->db->where('policy_id', $policy_id);
        $this->db->order_by('installment_no', 'ASC');
        return $this->db->get($this->schedule_table)->result();
    }

    /**
     * Insert premium schedule
     */
    public function insert_premium_schedule($data) {
        return $this->db->insert($this->schedule_table, $data);
    }

    /**
     * Update premium schedule status
     */
    public function update_schedule_status($id, $status, $paid_date = null, $paid_amount = null) {
        $data = ['status' => $status];
        if ($paid_date) {
            $data['paid_date'] = $paid_date;
        }
        if ($paid_amount) {
            $data['paid_amount'] = $paid_amount;
        }
        $this->db->where('id', $id);
        return $this->db->update($this->schedule_table, $data);
    }

    /**
     * Get endorsements
     */
    public function get_endorsements($policy_id) {
        $this->db->select('policy_endorsements.*, users.name as created_by_name');
        $this->db->from($this->endorsements_table);
        $this->db->join('users', 'users.id = policy_endorsements.created_by', 'left');
        $this->db->where('policy_id', $policy_id);
        $this->db->order_by('endorsement_date', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert endorsement
     */
    public function insert_endorsement($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->endorsements_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Generate endorsement number
     */
    public function generate_endorsement_number($policy_id) {
        $policy = $this->get_policy($policy_id);
        if (!$policy) {
            return 'END-' . time();
        }

        // Count existing endorsements
        $this->db->where('policy_id', $policy_id);
        $count = $this->db->count_all_results($this->endorsements_table);

        return $policy->policy_no . '/END-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Cancel policy
     */
    public function cancel_policy($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->cancellations_table, $data);
    }

    /**
     * Get policy claims
     */
    public function get_policy_claims($policy_id, $limit = 10) {
        $this->db->select('claims.*, claim_types.name as claim_type_name');
        $this->db->from('claims');
        $this->db->join('claim_types', 'claim_types.id = claims.claim_type_id', 'left');
        $this->db->where('policy_id', $policy_id);
        $this->db->order_by('claim_date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get policy documents
     */
    public function get_policy_documents($policy_id) {
        $this->db->select('policy_documents.*, users.name as uploaded_by_name');
        $this->db->from('policy_documents');
        $this->db->join('users', 'users.id = policy_documents.uploaded_by', 'left');
        $this->db->where('policy_id', $policy_id);
        $this->db->order_by('uploaded_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get policy activities
     */
    public function get_policy_activities($policy_id, $limit = 20) {
        $this->db->select('audit_logs.*, users.name as user_name');
        $this->db->from('audit_logs');
        $this->db->join('users', 'users.id = audit_logs.user_id', 'left');
        $this->db->where('table_name', 'policies');
        $this->db->where('record_id', $policy_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Log policy activity
     */
    public function log_activity($policy_id, $action, $old_values = null, $new_values = null) {
        $data = [
            'table_name' => 'policies',
            'record_id' => $policy_id,
            'action' => $action,
            'old_values' => $old_values ? json_encode($old_values) : NULL,
            'new_values' => $new_values ? json_encode($new_values) : NULL,
            'user_id' => $this->session->userdata('user_id') ?: 1,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('audit_logs', $data);
    }

    /**
     * Get all policies (for export)
     */
    public function get_all_policies() {
        $this->db->select('policies.*, customers.name as customer_name, policy_types.name as policy_type_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get policies expiring soon
     */
    public function get_expiring_soon($days = 30, $limit = 10) {
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d', strtotime('+' . $days . ' days'));

        $this->db->select('policies.*, customers.name as customer_name, customers.email as customer_email, policy_types.name as policy_type_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.id = policies.customer_id', 'left');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->where('policies.status', 'active');
        $this->db->where('policies.expiry_date >=', $from_date);
        $this->db->where('policies.expiry_date <=', $to_date);
        $this->db->order_by('policies.expiry_date', 'ASC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get policy statistics
     */
    public function get_statistics() {
        $stats = [];

        // Total policies
        $stats['total'] = $this->db->count_all($this->table);

        // Active policies
        $this->db->where('status', 'active');
        $stats['active'] = $this->db->count_all_results($this->table);

        // Expired policies
        $this->db->where('status', 'expired');
        $stats['expired'] = $this->db->count_all_results($this->table);

        // Cancelled policies
        $this->db->where('status', 'cancelled');
        $stats['cancelled'] = $this->db->count_all_results($this->table);

        // By type
        $this->db->select('policy_types.name, COUNT(*) as count');
        $this->db->from($this->table);
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->where('policies.status', 'active');
        $this->db->group_by('policies.policy_type_id');
        $types = $this->db->get()->result();
        $stats['by_type'] = [];
        foreach ($types as $type) {
            $stats['by_type'][$type->name] = $type->count;
        }

        // Total sum insured
        $this->db->select_sum('sum_insured');
        $this->db->where('status', 'active');
        $result = $this->db->get($this->table)->row();
        $stats['total_sum_insured'] = $result->sum_insured ?: 0;

        // Total premium
        $this->db->select_sum('total_premium');
        $this->db->where('status', 'active');
        $result = $this->db->get($this->table)->row();
        $stats['total_premium'] = $result->total_premium ?: 0;

        // New this month
        $this->db->where('created_at >=', date('Y-m-01 00:00:00'));
        $stats['new_this_month'] = $this->db->count_all_results($this->table);

        // Expiring this month
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
        $this->db->where('status', 'active');
        $this->db->where('expiry_date >=', $from_date);
        $this->db->where('expiry_date <=', $to_date);
        $stats['expiring_this_month'] = $this->db->count_all_results($this->table);

        return $stats;
    }

    /**
     * Get premium collection summary
     */
    public function get_premium_collection($year = null) {
        if (!$year) {
            $year = date('Y');
        }

        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $this->db->select_sum('total_premium');
            $this->db->where('YEAR(issue_date)', $year);
            $this->db->where('MONTH(issue_date)', $month);
            $this->db->where('status !=', 'cancelled');
            $result = $this->db->get($this->table)->row();

            $data[] = $result->total_premium ?: 0;
        }

        return $data;
    }
}
