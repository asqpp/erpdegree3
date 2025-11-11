<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 * Handles user authentication, login, logout, registration, password reset
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('url', 'form', 'security'));
    }

    /**
     * Login page
     */
    public function index() {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() === TRUE) {
                $email = $this->input->post('email');
                $password = $this->input->post('password');
                $remember = $this->input->post('remember');

                $user = $this->Auth_model->login($email, $password);

                if ($user) {
                    // Set session data
                    $session_data = array(
                        'user_id' => $user->user_id,
                        'username' => $user->username,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'role_name' => $user->role_name,
                        'company_id' => $user->company_id,
                        'branch_id' => $user->branch_id,
                        'logged_in' => TRUE
                    );

                    $this->session->set_userdata($session_data);

                    // Log login activity
                    $this->Auth_model->log_activity($user->user_id, 'login', 'User logged in');

                    // Remember me functionality
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $this->Auth_model->save_remember_token($user->user_id, $token);
                        set_cookie('remember_token', $token, 2592000); // 30 days
                    }

                    $this->session->set_flashdata('success', 'Welcome back, ' . $user->full_name . '!');
                    redirect('dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Invalid email or password');
                }
            }
        }

        $data['title'] = 'Login - Insurance ERP';
        $this->load->view('auth/login', $data);
    }

    /**
     * Login (alias for index)
     */
    public function login() {
        $this->index();
    }

    /**
     * Logout
     */
    public function logout() {
        $user_id = $this->session->userdata('user_id');

        if ($user_id) {
            // Log logout activity
            $this->Auth_model->log_activity($user_id, 'logout', 'User logged out');
        }

        // Remove remember me cookie
        delete_cookie('remember_token');

        // Destroy session
        $this->session->sess_destroy();

        $this->session->set_flashdata('success', 'You have been logged out successfully');
        redirect('auth/login');
    }

    /**
     * Register new user
     */
    public function register() {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|max_length[50]|is_unique[users.username]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('full_name', 'Full Name', 'required|min_length[3]|max_length[100]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

            if ($this->form_validation->run() === TRUE) {
                $user_data = array(
                    'username' => $this->input->post('username'),
                    'email' => $this->input->post('email'),
                    'full_name' => $this->input->post('full_name'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role_id' => 2, // Default role (User)
                    'company_id' => 1, // Default company
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );

                $user_id = $this->Auth_model->register($user_data);

                if ($user_id) {
                    // Log registration activity
                    $this->Auth_model->log_activity($user_id, 'register', 'New user registered');

                    $this->session->set_flashdata('success', 'Registration successful! Please login.');
                    redirect('auth/login');
                } else {
                    $this->session->set_flashdata('error', 'Registration failed. Please try again.');
                }
            }
        }

        $data['title'] = 'Register - Insurance ERP';
        $this->load->view('auth/register', $data);
    }

    /**
     * Forgot password
     */
    public function forgot_password() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

            if ($this->form_validation->run() === TRUE) {
                $email = $this->input->post('email');
                $user = $this->Auth_model->get_user_by_email($email);

                if ($user) {
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $this->Auth_model->save_reset_token($user->user_id, $token, $expiry);

                    // Send reset email (implement email sending)
                    $reset_link = base_url('auth/reset_password/' . $token);

                    // TODO: Send email with reset link
                    // For now, just show the link
                    $this->session->set_flashdata('success', 'Password reset link: ' . $reset_link);

                    // Log activity
                    $this->Auth_model->log_activity($user->user_id, 'forgot_password', 'Password reset requested');
                } else {
                    // Don't reveal if email exists or not (security)
                    $this->session->set_flashdata('success', 'If the email exists, a reset link has been sent.');
                }

                redirect('auth/forgot_password');
            }
        }

        $data['title'] = 'Forgot Password - Insurance ERP';
        $this->load->view('auth/forgot_password', $data);
    }

    /**
     * Reset password
     */
    public function reset_password($token = null) {
        if (!$token) {
            redirect('auth/forgot_password');
        }

        $user = $this->Auth_model->get_user_by_reset_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid or expired reset token');
            redirect('auth/forgot_password');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

            if ($this->form_validation->run() === TRUE) {
                $new_password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);

                if ($this->Auth_model->update_password($user->user_id, $new_password)) {
                    // Clear reset token
                    $this->Auth_model->clear_reset_token($user->user_id);

                    // Log activity
                    $this->Auth_model->log_activity($user->user_id, 'reset_password', 'Password was reset');

                    $this->session->set_flashdata('success', 'Password reset successful! Please login.');
                    redirect('auth/login');
                } else {
                    $this->session->set_flashdata('error', 'Failed to reset password. Please try again.');
                }
            }
        }

        $data['title'] = 'Reset Password - Insurance ERP';
        $data['token'] = $token;
        $data['user'] = $user;
        $this->load->view('auth/reset_password', $data);
    }

    /**
     * Check if user is logged in (for AJAX)
     */
    public function check_session() {
        $logged_in = $this->session->userdata('logged_in') ? true : false;
        echo json_encode(array('logged_in' => $logged_in));
    }

    /**
     * Change password (for logged in users)
     */
    public function change_password() {
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('current_password', 'Current Password', 'required');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

            if ($this->form_validation->run() === TRUE) {
                $user_id = $this->session->userdata('user_id');
                $current_password = $this->input->post('current_password');
                $new_password = $this->input->post('new_password');

                if ($this->Auth_model->verify_current_password($user_id, $current_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    if ($this->Auth_model->update_password($user_id, $hashed_password)) {
                        // Log activity
                        $this->Auth_model->log_activity($user_id, 'change_password', 'Password changed');

                        $this->session->set_flashdata('success', 'Password changed successfully!');
                        redirect('dashboard');
                    } else {
                        $this->session->set_flashdata('error', 'Failed to change password.');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Current password is incorrect.');
                }
            }
        }

        $data['title'] = 'Change Password - Insurance ERP';
        $this->load->view('auth/change_password', $data);
    }
}
