<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Permission Model
 * Handles database operations for user permissions
 */
class User_permission_model extends CI_Model {

    private $modules = array(
        'Customers', 'Policies', 'Claims', 'Sales', 'Receipts', 'Payments',
        'Debit Notes', 'Credit Notes', 'Accounting', 'Reports', 'HR',
        'Settings', 'Users', 'Backup'
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all system modules
     */
    public function get_all_modules() {
        return $this->modules;
    }

    /**
     * Get all users
     */
    public function get_users() {
        $this->db->select('u.*, r.role_name, b.branch_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
        $this->db->join('branches b', 'b.branch_id = u.branch_id', 'left');
        $this->db->where('u.is_active', 1);
        $this->db->order_by('u.username', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get single user
     */
    public function get_user($user_id) {
        $this->db->select('u.*, r.role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
        $this->db->where('u.user_id', $user_id);

        return $this->db->get()->row();
    }

    /**
     * Get all roles
     */
    public function get_roles() {
        $this->db->select('*');
        $this->db->from('roles');
        $this->db->order_by('role_name', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get single role
     */
    public function get_role($role_id) {
        $this->db->select('*');
        $this->db->from('roles');
        $this->db->where('role_id', $role_id);

        return $this->db->get()->row();
    }

    /**
     * Get user permissions
     */
    public function get_user_permissions($user_id) {
        $this->db->select('*');
        $this->db->from('user_permissions');
        $this->db->where('user_id', $user_id);

        $result = $this->db->get()->result();

        // Convert to associative array for easy access
        $permissions = array();
        foreach ($result as $row) {
            $permissions[$row->module_name] = array(
                'can_view' => $row->can_view,
                'can_create' => $row->can_create,
                'can_edit' => $row->can_edit,
                'can_delete' => $row->can_delete,
                'can_approve' => $row->can_approve,
                'can_export' => $row->can_export
            );
        }

        return $permissions;
    }

    /**
     * Get role permissions
     */
    public function get_role_permissions($role_id) {
        $this->db->select('*');
        $this->db->from('role_permissions');
        $this->db->where('role_id', $role_id);

        $result = $this->db->get()->result();

        // Convert to associative array for easy access
        $permissions = array();
        foreach ($result as $row) {
            $permissions[$row->module_name] = array(
                'can_view' => $row->can_view,
                'can_create' => $row->can_create,
                'can_edit' => $row->can_edit,
                'can_delete' => $row->can_delete,
                'can_approve' => $row->can_approve,
                'can_export' => $row->can_export
            );
        }

        return $permissions;
    }

    /**
     * Update user permissions
     */
    public function update_user_permissions($user_id, $permissions) {
        $this->db->trans_start();

        // Delete existing permissions
        $this->db->where('user_id', $user_id);
        $this->db->delete('user_permissions');

        // Insert new permissions
        if ($permissions && is_array($permissions)) {
            foreach ($permissions as $module => $perms) {
                $data = array(
                    'user_id' => $user_id,
                    'module_name' => $module,
                    'can_view' => isset($perms['view']) ? 1 : 0,
                    'can_create' => isset($perms['create']) ? 1 : 0,
                    'can_edit' => isset($perms['edit']) ? 1 : 0,
                    'can_delete' => isset($perms['delete']) ? 1 : 0,
                    'can_approve' => isset($perms['approve']) ? 1 : 0,
                    'can_export' => isset($perms['export']) ? 1 : 0
                );

                $this->db->insert('user_permissions', $data);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Update role permissions
     */
    public function update_role_permissions($role_id, $permissions) {
        $this->db->trans_start();

        // Delete existing permissions
        $this->db->where('role_id', $role_id);
        $this->db->delete('role_permissions');

        // Insert new permissions
        if ($permissions && is_array($permissions)) {
            foreach ($permissions as $module => $perms) {
                $data = array(
                    'role_id' => $role_id,
                    'module_name' => $module,
                    'can_view' => isset($perms['view']) ? 1 : 0,
                    'can_create' => isset($perms['create']) ? 1 : 0,
                    'can_edit' => isset($perms['edit']) ? 1 : 0,
                    'can_delete' => isset($perms['delete']) ? 1 : 0,
                    'can_approve' => isset($perms['approve']) ? 1 : 0,
                    'can_export' => isset($perms['export']) ? 1 : 0
                );

                $this->db->insert('role_permissions', $data);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Check if user has permission
     * First checks user-specific permissions, then falls back to role permissions
     */
    public function check_permission($user_id, $module_name, $permission_type = 'view') {
        $permission_column = 'can_' . $permission_type;

        // Check user-specific permissions first
        $this->db->select($permission_column);
        $this->db->from('user_permissions');
        $this->db->where('user_id', $user_id);
        $this->db->where('module_name', $module_name);

        $user_perm = $this->db->get()->row();

        if ($user_perm) {
            return $user_perm->$permission_column == 1;
        }

        // Fall back to role permissions
        $this->db->select('u.role_id, rp.' . $permission_column);
        $this->db->from('users u');
        $this->db->join('role_permissions rp', 'rp.role_id = u.role_id', 'left');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('rp.module_name', $module_name);

        $role_perm = $this->db->get()->row();

        if ($role_perm) {
            return $role_perm->$permission_column == 1;
        }

        // Default deny
        return false;
    }
}
