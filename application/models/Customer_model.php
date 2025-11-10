<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Customer Model
 * Handles all customer-related database operations
 */
class Customer_model extends CI_Model {

    private $table = 'customers';
    private $contacts_table = 'customer_contacts';
    private $addresses_table = 'customer_addresses';
    private $documents_table = 'customer_documents';

    /**
     * Get customers with pagination and filters
     */
    public function get_customers($limit, $offset, $search = null, $status = null, $customer_type = null, $kyc_status = null) {
        $this->db->select('customers.*, customer_groups.name as group_name, agents.name as agent_name');
        $this->db->from($this->table);
        $this->db->join('customer_groups', 'customer_groups.id = customers.customer_group_id', 'left');
        $this->db->join('agents', 'agents.id = customers.agent_id', 'left');

        // Apply filters
        if ($search) {
            $this->db->group_start();
            $this->db->like('customers.code', $search);
            $this->db->or_like('customers.name', $search);
            $this->db->or_like('customers.email', $search);
            $this->db->or_like('customers.phone', $search);
            $this->db->or_like('customers.emirates_id', $search);
            $this->db->group_end();
        }

        if ($status !== null && $status !== '') {
            $this->db->where('customers.is_active', $status);
        }

        if ($customer_type) {
            $this->db->where('customers.customer_type', $customer_type);
        }

        if ($kyc_status) {
            $this->db->where('customers.kyc_status', $kyc_status);
        }

        $this->db->order_by('customers.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count customers with filters
     */
    public function count_customers($search = null, $status = null, $customer_type = null, $kyc_status = null) {
        $this->db->from($this->table);

        if ($search) {
            $this->db->group_start();
            $this->db->like('code', $search);
            $this->db->or_like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->or_like('emirates_id', $search);
            $this->db->group_end();
        }

        if ($status !== null && $status !== '') {
            $this->db->where('is_active', $status);
        }

        if ($customer_type) {
            $this->db->where('customer_type', $customer_type);
        }

        if ($kyc_status) {
            $this->db->where('kyc_status', $kyc_status);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get single customer by ID
     */
    public function get_customer($id) {
        $this->db->select('customers.*, customer_groups.name as group_name, agents.name as agent_name');
        $this->db->from($this->table);
        $this->db->join('customer_groups', 'customer_groups.id = customers.customer_group_id', 'left');
        $this->db->join('agents', 'agents.id = customers.agent_id', 'left');
        $this->db->where('customers.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Insert new customer
     */
    public function insert_customer($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update customer
     */
    public function update_customer($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete customer
     */
    public function delete_customer($id) {
        // Delete related records first
        $this->db->where('customer_id', $id);
        $this->db->delete($this->contacts_table);

        $this->db->where('customer_id', $id);
        $this->db->delete($this->addresses_table);

        $this->db->where('customer_id', $id);
        $this->db->delete($this->documents_table);

        // Delete customer
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Generate unique customer code
     */
    public function generate_customer_code() {
        $prefix = 'CUST';
        $year = date('Y');

        // Get last customer code for this year
        $this->db->select('code');
        $this->db->from($this->table);
        $this->db->like('code', $prefix . '-' . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();

        if ($last) {
            // Extract number and increment
            $last_num = (int) substr($last->code, -4);
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if email exists (for another customer)
     */
    public function check_email_exists($email, $exclude_id = null) {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Check if customer has policies
     */
    public function has_policies($customer_id) {
        $this->db->where('customer_id', $customer_id);
        return $this->db->count_all_results('policies') > 0;
    }

    /**
     * Get customer contacts
     */
    public function get_customer_contacts($customer_id) {
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('is_primary', 'DESC');
        return $this->db->get($this->contacts_table)->result();
    }

    /**
     * Get customer addresses
     */
    public function get_customer_addresses($customer_id) {
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('is_primary', 'DESC');
        return $this->db->get($this->addresses_table)->result();
    }

    /**
     * Insert address
     */
    public function insert_address($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->addresses_table, $data);
    }

    /**
     * Get customer documents
     */
    public function get_customer_documents($customer_id) {
        $this->db->select('customer_documents.*, users.name as uploaded_by_name');
        $this->db->from($this->documents_table);
        $this->db->join('users', 'users.id = customer_documents.uploaded_by', 'left');
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('uploaded_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert document
     */
    public function insert_document($data) {
        $data['uploaded_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->documents_table, $data);
    }

    /**
     * Get customer policies
     */
    public function get_customer_policies($customer_id, $limit = 10) {
        $this->db->select('policies.*, policy_types.name as policy_type_name, currencies.symbol as currency_symbol');
        $this->db->from('policies');
        $this->db->join('policy_types', 'policy_types.id = policies.policy_type_id', 'left');
        $this->db->join('currencies', 'currencies.id = policies.currency_id', 'left');
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('issue_date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get customer activities/audit log
     */
    public function get_customer_activities($customer_id, $limit = 20) {
        $this->db->select('audit_logs.*, users.name as user_name');
        $this->db->from('audit_logs');
        $this->db->join('users', 'users.id = audit_logs.user_id', 'left');
        $this->db->where('table_name', 'customers');
        $this->db->where('record_id', $customer_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Log customer activity
     */
    public function log_activity($customer_id, $action, $old_values = null, $new_values = null) {
        $data = [
            'table_name' => 'customers',
            'record_id' => $customer_id,
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
     * Get customer groups
     */
    public function get_customer_groups() {
        $this->db->where('is_active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get('customer_groups')->result();
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
     * Get all customers (for export)
     */
    public function get_all_customers() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get customer statistics
     */
    public function get_statistics() {
        $stats = [];

        // Total customers
        $stats['total'] = $this->db->count_all($this->table);

        // Active customers
        $this->db->where('is_active', 1);
        $stats['active'] = $this->db->count_all_results($this->table);

        // By type
        $this->db->select('customer_type, COUNT(*) as count');
        $this->db->group_by('customer_type');
        $types = $this->db->get($this->table)->result();
        $stats['by_type'] = [];
        foreach ($types as $type) {
            $stats['by_type'][$type->customer_type] = $type->count;
        }

        // KYC status
        $this->db->select('kyc_status, COUNT(*) as count');
        $this->db->group_by('kyc_status');
        $kyc = $this->db->get($this->table)->result();
        $stats['by_kyc'] = [];
        foreach ($kyc as $k) {
            $stats['by_kyc'][$k->kyc_status] = $k->count;
        }

        // New this month
        $this->db->where('created_at >=', date('Y-m-01 00:00:00'));
        $stats['new_this_month'] = $this->db->count_all_results($this->table);

        return $stats;
    }
}
