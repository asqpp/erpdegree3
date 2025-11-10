<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Company Setting Model
 * Handles database operations for company settings
 */
class Company_setting_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get company settings
     */
    public function get_company_settings() {
        $this->db->select('cs.*, e.emirate_name');
        $this->db->from('company_settings cs');
        $this->db->join('emirates e', 'e.emirate_id = cs.emirate_id', 'left');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        // If no settings exist, create default
        if (!$result) {
            $this->create_default_settings();
            return $this->get_company_settings();
        }

        return $result;
    }

    /**
     * Update company settings
     */
    public function update_company_settings($data) {
        $this->db->trans_start();

        $company_id = $this->session->userdata('company_id');

        // Check if settings exist
        $this->db->where('company_id', $company_id);
        $existing = $this->db->get('company_settings')->row();

        if ($existing) {
            // Update existing settings
            $this->db->where('company_id', $company_id);
            $this->db->update('company_settings', $data);
        } else {
            // Insert new settings
            $data['company_id'] = $company_id;
            $this->db->insert('company_settings', $data);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Update company name in companies table
     */
    public function update_company_name($company_name) {
        $company_id = $this->session->userdata('company_id');

        $this->db->where('company_id', $company_id);
        $this->db->update('companies', array('company_name' => $company_name));

        return $this->db->affected_rows() > 0;
    }

    /**
     * Update backup settings
     */
    public function update_backup_settings($data) {
        $company_id = $this->session->userdata('company_id');

        $this->db->where('company_id', $company_id);
        $this->db->update('company_settings', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Create default company settings
     */
    private function create_default_settings() {
        $company_id = $this->session->userdata('company_id');

        // Get company name
        $this->db->select('company_name');
        $this->db->from('companies');
        $this->db->where('company_id', $company_id);
        $company = $this->db->get()->row();

        $default_data = array(
            'company_id' => $company_id,
            'company_name' => $company ? $company->company_name : 'Insurance Company',
            'country' => 'United Arab Emirates',
            'base_currency' => 'AED',
            'date_format' => 'd/m/Y',
            'time_zone' => 'Asia/Dubai',
            'default_vat_percentage' => 5.00,
            'fiscal_year_start' => '01-01',
            'fiscal_year_end' => '12-31',
            'backup_enabled' => 1,
            'backup_frequency' => 'daily',
            'backup_path' => './backups/'
        );

        $this->db->insert('company_settings', $default_data);
    }

    /**
     * Get emirates for dropdown
     */
    public function get_emirates() {
        $this->db->select('*');
        $this->db->from('emirates');
        $this->db->order_by('emirate_name', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get currencies for dropdown
     */
    public function get_currencies() {
        $this->db->select('*');
        $this->db->from('currencies');
        $this->db->where('is_active', 1);
        $this->db->order_by('currency_code', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get audit logs
     */
    public function get_audit_logs($limit, $offset, $filters = array()) {
        $this->db->select('al.*, u.username');
        $this->db->from('audit_logs al');
        $this->db->join('users u', 'u.user_id = al.user_id', 'left');

        // Apply filters
        if (!empty($filters['user_id'])) {
            $this->db->where('al.user_id', $filters['user_id']);
        }

        if (!empty($filters['action_type'])) {
            $this->db->where('al.action_type', $filters['action_type']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('DATE(al.created_at) >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('DATE(al.created_at) <=', $filters['to_date']);
        }

        $this->db->order_by('al.log_id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count audit logs
     */
    public function count_audit_logs($filters = array()) {
        $this->db->from('audit_logs al');

        if (!empty($filters['user_id'])) {
            $this->db->where('al.user_id', $filters['user_id']);
        }

        if (!empty($filters['action_type'])) {
            $this->db->where('al.action_type', $filters['action_type']);
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('DATE(al.created_at) >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('DATE(al.created_at) <=', $filters['to_date']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get users for dropdown
     */
    public function get_users() {
        $this->db->select('user_id, username, full_name');
        $this->db->from('users');
        $this->db->where('is_active', 1);
        $this->db->order_by('username', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Log audit trail
     */
    public function log_audit($action_type, $table_name, $record_id, $old_data = null, $new_data = null) {
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'action_type' => $action_type,
            'table_name' => $table_name,
            'record_id' => $record_id,
            'old_data' => $old_data ? json_encode($old_data) : null,
            'new_data' => $new_data ? json_encode($new_data) : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        );

        return $this->db->insert('audit_logs', $data);
    }
}
