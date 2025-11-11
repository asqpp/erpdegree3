<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Permissions Controller
 * Handles user access control and permissions management
 */
class User_permissions extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_permission_model');
        $this->load->library('session');
        $this->load->helper('url');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        // Check if user has admin role
        if ($this->session->userdata('role_id') != 1) {
            show_error('You do not have permission to access this page', 403);
        }
    }

    /**
     * List all users with permissions
     */
    public function index() {
        $data['title'] = 'User Access Control';
        $data['users'] = $this->User_permission_model->get_users();
        $data['modules'] = $this->User_permission_model->get_all_modules();

        $this->load->view('templates/header', $data);
        $this->load->view('user_permissions/list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Manage user permissions
     */
    public function manage($user_id) {
        $data['title'] = 'Manage User Permissions';
        $data['user'] = $this->User_permission_model->get_user($user_id);

        if (!$data['user']) {
            show_404();
        }

        if ($this->input->post()) {
            $permissions = $this->input->post('permissions');

            if ($this->User_permission_model->update_user_permissions($user_id, $permissions)) {
                $this->session->set_flashdata('success', 'User permissions updated successfully');
                redirect('user_permissions');
            } else {
                $this->session->set_flashdata('error', 'Failed to update user permissions');
            }
        }

        $data['modules'] = $this->User_permission_model->get_all_modules();
        $data['user_permissions'] = $this->User_permission_model->get_user_permissions($user_id);
        $data['role_permissions'] = $this->User_permission_model->get_role_permissions($data['user']->role_id);

        $this->load->view('templates/header', $data);
        $this->load->view('user_permissions/manage', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Manage role permissions
     */
    public function role_permissions() {
        $data['title'] = 'Role Permissions';
        $data['roles'] = $this->User_permission_model->get_roles();

        $this->load->view('templates/header', $data);
        $this->load->view('user_permissions/roles', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Manage specific role permissions
     */
    public function manage_role($role_id) {
        $data['title'] = 'Manage Role Permissions';
        $data['role'] = $this->User_permission_model->get_role($role_id);

        if (!$data['role']) {
            show_404();
        }

        if ($this->input->post()) {
            $permissions = $this->input->post('permissions');

            if ($this->User_permission_model->update_role_permissions($role_id, $permissions)) {
                $this->session->set_flashdata('success', 'Role permissions updated successfully');
                redirect('user_permissions/role_permissions');
            } else {
                $this->session->set_flashdata('error', 'Failed to update role permissions');
            }
        }

        $data['modules'] = $this->User_permission_model->get_all_modules();
        $data['role_permissions'] = $this->User_permission_model->get_role_permissions($role_id);

        $this->load->view('templates/header', $data);
        $this->load->view('user_permissions/manage_role', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Check if user has permission
     */
    public function check_permission($module, $permission_type = 'view') {
        $user_id = $this->session->userdata('user_id');
        $has_permission = $this->User_permission_model->check_permission($user_id, $module, $permission_type);

        echo json_encode(array('has_permission' => $has_permission));
    }
}
