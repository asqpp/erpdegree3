<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Company Settings Controller
 * Handles company information and settings management
 */
class Company_settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Company_setting_model');
        $this->load->library('session');
        $this->load->helper(array('url', 'form'));

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    /**
     * View company settings
     */
    public function index() {
        $data['title'] = 'Company Settings';
        $data['settings'] = $this->Company_setting_model->get_company_settings();
        $data['emirates'] = $this->Company_setting_model->get_emirates();

        $this->load->view('templates/header', $data);
        $this->load->view('company_settings/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Edit company settings
     */
    public function edit() {
        $data['title'] = 'Edit Company Settings';

        if ($this->input->post()) {
            // Validate input
            $this->load->library('form_validation');
            $this->form_validation->set_rules('company_name', 'Company Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone', 'required');

            if ($this->form_validation->run() === TRUE) {
                // Prepare company settings data
                $settings_data = array(
                    'company_name' => $this->input->post('company_name'),
                    'trade_license_number' => $this->input->post('trade_license_number'),
                    'tax_registration_number' => $this->input->post('tax_registration_number'),
                    'address_line1' => $this->input->post('address_line1'),
                    'address_line2' => $this->input->post('address_line2'),
                    'city' => $this->input->post('city'),
                    'emirate_id' => $this->input->post('emirate_id'),
                    'country' => $this->input->post('country'),
                    'po_box' => $this->input->post('po_box'),
                    'phone' => $this->input->post('phone'),
                    'fax' => $this->input->post('fax'),
                    'email' => $this->input->post('email'),
                    'website' => $this->input->post('website'),
                    'fiscal_year_start' => $this->input->post('fiscal_year_start'),
                    'fiscal_year_end' => $this->input->post('fiscal_year_end'),
                    'base_currency' => $this->input->post('base_currency'),
                    'date_format' => $this->input->post('date_format'),
                    'time_zone' => $this->input->post('time_zone'),
                    'default_vat_percentage' => $this->input->post('default_vat_percentage')
                );

                // Handle logo upload
                if (!empty($_FILES['logo']['name'])) {
                    $upload_config = array(
                        'upload_path' => './uploads/company/',
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size' => 2048,
                        'file_name' => 'company_logo_' . time()
                    );

                    $this->load->library('upload', $upload_config);

                    if ($this->upload->do_upload('logo')) {
                        $upload_data = $this->upload->data();
                        $settings_data['logo_path'] = 'uploads/company/' . $upload_data['file_name'];
                    }
                }

                if ($this->Company_setting_model->update_company_settings($settings_data)) {
                    // Update company name in companies table
                    $this->Company_setting_model->update_company_name($this->input->post('company_name'));

                    $this->session->set_flashdata('success', 'Company settings updated successfully');
                    redirect('company_settings');
                } else {
                    $this->session->set_flashdata('error', 'Failed to update company settings');
                }
            }
        }

        $data['settings'] = $this->Company_setting_model->get_company_settings();
        $data['emirates'] = $this->Company_setting_model->get_emirates();
        $data['currencies'] = $this->Company_setting_model->get_currencies();

        $this->load->view('templates/header', $data);
        $this->load->view('company_settings/edit', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Manage backup settings
     */
    public function backup_settings() {
        $data['title'] = 'Backup Settings';

        if ($this->input->post()) {
            $backup_data = array(
                'backup_enabled' => $this->input->post('backup_enabled') ? 1 : 0,
                'backup_frequency' => $this->input->post('backup_frequency'),
                'backup_path' => $this->input->post('backup_path')
            );

            if ($this->Company_setting_model->update_backup_settings($backup_data)) {
                $this->session->set_flashdata('success', 'Backup settings updated successfully');
                redirect('company_settings/backup_settings');
            } else {
                $this->session->set_flashdata('error', 'Failed to update backup settings');
            }
        }

        $data['settings'] = $this->Company_setting_model->get_company_settings();

        $this->load->view('templates/header', $data);
        $this->load->view('company_settings/backup_settings', $data);
        $this->load->view('templates/footer');
    }

    /**
     * View audit logs
     */
    public function audit_logs() {
        $data['title'] = 'Audit Logs';

        // Pagination
        $limit = 50;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;

        // Filters
        $filters = array(
            'user_id' => $this->input->get('user_id'),
            'action_type' => $this->input->get('action_type'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $data['logs'] = $this->Company_setting_model->get_audit_logs($limit, $offset, $filters);
        $data['total_records'] = $this->Company_setting_model->count_audit_logs($filters);
        $data['users'] = $this->Company_setting_model->get_users();
        $data['filters'] = $filters;

        $this->load->view('templates/header', $data);
        $this->load->view('company_settings/audit_logs', $data);
        $this->load->view('templates/footer');
    }
}
