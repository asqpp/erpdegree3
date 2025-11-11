<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Model
 * Handles database operations for authentication
 */
class Auth_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        $this->db->select('u.*, r.role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
        $this->db->where('u.email', $email);
        $this->db->where('u.is_active', 1);
        $this->db->limit(1);

        $user = $this->db->get()->row();

        if ($user && password_verify($password, $user->password)) {
            // Update last login
            $this->db->where('user_id', $user->user_id);
            $this->db->update('users', array(
                'last_login' => date('Y-m-d H:i:s'),
                'last_login_ip' => $this->input->ip_address()
            ));

            return $user;
        }

        return false;
    }

    /**
     * Register new user
     */
    public function register($data) {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    /**
     * Get user by email
     */
    public function get_user_by_email($email) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email);
        $this->db->where('is_active', 1);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Get user by ID
     */
    public function get_user($user_id) {
        $this->db->select('u.*, r.role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
        $this->db->where('u.user_id', $user_id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Save remember me token
     */
    public function save_remember_token($user_id, $token) {
        $data = array(
            'remember_token' => $token,
            'remember_token_expiry' => date('Y-m-d H:i:s', strtotime('+30 days'))
        );

        $this->db->where('user_id', $user_id);
        $this->db->update('users', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get user by remember token
     */
    public function get_user_by_remember_token($token) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('remember_token', $token);
        $this->db->where('remember_token_expiry >', date('Y-m-d H:i:s'));
        $this->db->where('is_active', 1);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Save password reset token
     */
    public function save_reset_token($user_id, $token, $expiry) {
        $data = array(
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        );

        $this->db->where('user_id', $user_id);
        $this->db->update('users', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get user by reset token
     */
    public function get_user_by_reset_token($token) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('reset_token', $token);
        $this->db->where('reset_token_expiry >', date('Y-m-d H:i:s'));
        $this->db->where('is_active', 1);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Update password
     */
    public function update_password($user_id, $password) {
        $this->db->where('user_id', $user_id);
        $this->db->update('users', array('password' => $password));

        return $this->db->affected_rows() > 0;
    }

    /**
     * Clear reset token
     */
    public function clear_reset_token($user_id) {
        $data = array(
            'reset_token' => null,
            'reset_token_expiry' => null
        );

        $this->db->where('user_id', $user_id);
        $this->db->update('users', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Verify current password
     */
    public function verify_current_password($user_id, $password) {
        $this->db->select('password');
        $this->db->from('users');
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);

        $user = $this->db->get()->row();

        if ($user && password_verify($password, $user->password)) {
            return true;
        }

        return false;
    }

    /**
     * Log user activity
     */
    public function log_activity($user_id, $action_type, $description) {
        $data = array(
            'user_id' => $user_id,
            'action_type' => $action_type,
            'table_name' => 'users',
            'record_id' => $user_id,
            'description' => $description,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('audit_logs', $data);
    }

    /**
     * Check if email exists
     */
    public function email_exists($email) {
        $this->db->where('email', $email);
        $this->db->from('users');
        return $this->db->count_all_results() > 0;
    }

    /**
     * Check if username exists
     */
    public function username_exists($username) {
        $this->db->where('username', $username);
        $this->db->from('users');
        return $this->db->count_all_results() > 0;
    }
}
