<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Customers Controller
 * Manages customer CRUD operations, KYC, and portal access
 */
class Customers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Customer_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'upload', 'session']);

        // Check if user is logged in (add your auth check here)
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
    }

    /**
     * List all customers
     */
    public function index() {
        // Get search parameters
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $customer_type = $this->input->get('customer_type');
        $kyc_status = $this->input->get('kyc_status');

        // Pagination configuration
        $config['base_url'] = base_url('customers/index');
        $config['total_rows'] = $this->Customer_model->count_customers($search, $status, $customer_type, $kyc_status);
        $config['per_page'] = 20;
        $config['use_page_numbers'] = TRUE;

        $this->load->library('pagination', $config);

        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        // Get customers
        $data['customers'] = $this->Customer_model->get_customers($config['per_page'], $offset, $search, $status, $customer_type, $kyc_status);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($config['total_rows'] / $config['per_page']);

        // Search parameters for form
        $data['search'] = $search;
        $data['filter_status'] = $status;
        $data['filter_customer_type'] = $customer_type;
        $data['filter_kyc_status'] = $kyc_status;

        $data['page_title'] = 'Customers';
        $data['breadcrumbs'] = [
            ['title' => 'Customers']
        ];
        $data['main_content'] = 'customers/list';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * View customer details
     */
    public function view($id) {
        $customer = $this->Customer_model->get_customer($id);

        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('customers');
        }

        $data['customer'] = $customer;
        $data['contacts'] = $this->Customer_model->get_customer_contacts($id);
        $data['addresses'] = $this->Customer_model->get_customer_addresses($id);
        $data['documents'] = $this->Customer_model->get_customer_documents($id);
        $data['policies'] = $this->Customer_model->get_customer_policies($id);
        $data['activities'] = $this->Customer_model->get_customer_activities($id);

        $data['page_title'] = 'Customer Details - ' . $customer->name;
        $data['breadcrumbs'] = [
            ['title' => 'Customers', 'url' => base_url('customers')],
            ['title' => $customer->name]
        ];
        $data['main_content'] = 'customers/view';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Add new customer
     */
    public function add() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Customer Name', 'required|trim');
            $this->form_validation->set_rules('customer_type', 'Customer Type', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[customers.email]');
            $this->form_validation->set_rules('phone', 'Phone', 'required');

            if ($this->form_validation->run() == TRUE) {
                $customer_data = [
                    'code' => $this->Customer_model->generate_customer_code(),
                    'name' => $this->input->post('name'),
                    'customer_type' => $this->input->post('customer_type'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'mobile' => $this->input->post('mobile'),
                    'emirates_id' => $this->input->post('emirates_id'),
                    'passport_no' => $this->input->post('passport_no'),
                    'trn_no' => $this->input->post('trn_no'),
                    'date_of_birth' => $this->input->post('date_of_birth') ?: NULL,
                    'gender' => $this->input->post('gender'),
                    'nationality' => $this->input->post('nationality'),
                    'language' => $this->input->post('language') ?: 'en',
                    'credit_limit' => $this->input->post('credit_limit') ?: 0,
                    'credit_days' => $this->input->post('credit_days') ?: 0,
                    'customer_group_id' => $this->input->post('customer_group_id') ?: NULL,
                    'agent_id' => $this->input->post('agent_id') ?: NULL,
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                    'kyc_status' => 'pending',
                    'portal_access' => $this->input->post('portal_access') ? 1 : 0,
                    'notes' => $this->input->post('notes'),
                    'created_by' => $this->session->userdata('user_id') ?: 1
                ];

                $customer_id = $this->Customer_model->insert_customer($customer_data);

                if ($customer_id) {
                    // Add primary address if provided
                    if ($this->input->post('address_line_1')) {
                        $address_data = [
                            'customer_id' => $customer_id,
                            'address_type' => 'billing',
                            'address_line_1' => $this->input->post('address_line_1'),
                            'address_line_2' => $this->input->post('address_line_2'),
                            'city' => $this->input->post('city'),
                            'emirate' => $this->input->post('emirate'),
                            'po_box' => $this->input->post('po_box'),
                            'country' => $this->input->post('country') ?: 'UAE',
                            'is_primary' => 1
                        ];
                        $this->Customer_model->insert_address($address_data);
                    }

                    $this->session->set_flashdata('success', 'Customer added successfully');
                    redirect('customers/view/' . $customer_id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to add customer');
                }
            }
        }

        $data['page_title'] = 'Add New Customer';
        $data['breadcrumbs'] = [
            ['title' => 'Customers', 'url' => base_url('customers')],
            ['title' => 'Add New']
        ];
        $data['customer_groups'] = $this->Customer_model->get_customer_groups();
        $data['agents'] = $this->Customer_model->get_agents();
        $data['main_content'] = 'customers/form';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Edit customer
     */
    public function edit($id) {
        $customer = $this->Customer_model->get_customer($id);

        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('customers');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Customer Name', 'required|trim');
            $this->form_validation->set_rules('customer_type', 'Customer Type', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email_unique[' . $id . ']');
            $this->form_validation->set_rules('phone', 'Phone', 'required');

            if ($this->form_validation->run() == TRUE) {
                $customer_data = [
                    'name' => $this->input->post('name'),
                    'customer_type' => $this->input->post('customer_type'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'mobile' => $this->input->post('mobile'),
                    'emirates_id' => $this->input->post('emirates_id'),
                    'passport_no' => $this->input->post('passport_no'),
                    'trn_no' => $this->input->post('trn_no'),
                    'date_of_birth' => $this->input->post('date_of_birth') ?: NULL,
                    'gender' => $this->input->post('gender'),
                    'nationality' => $this->input->post('nationality'),
                    'language' => $this->input->post('language'),
                    'credit_limit' => $this->input->post('credit_limit'),
                    'credit_days' => $this->input->post('credit_days'),
                    'customer_group_id' => $this->input->post('customer_group_id') ?: NULL,
                    'agent_id' => $this->input->post('agent_id') ?: NULL,
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                    'portal_access' => $this->input->post('portal_access') ? 1 : 0,
                    'notes' => $this->input->post('notes'),
                    'updated_by' => $this->session->userdata('user_id') ?: 1
                ];

                if ($this->Customer_model->update_customer($id, $customer_data)) {
                    $this->session->set_flashdata('success', 'Customer updated successfully');
                    redirect('customers/view/' . $id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to update customer');
                }
            }
        }

        $data['customer'] = $customer;
        $data['page_title'] = 'Edit Customer - ' . $customer->name;
        $data['breadcrumbs'] = [
            ['title' => 'Customers', 'url' => base_url('customers')],
            ['title' => $customer->name, 'url' => base_url('customers/view/' . $id)],
            ['title' => 'Edit']
        ];
        $data['customer_groups'] = $this->Customer_model->get_customer_groups();
        $data['agents'] = $this->Customer_model->get_agents();
        $data['main_content'] = 'customers/form';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Delete customer
     */
    public function delete($id) {
        $customer = $this->Customer_model->get_customer($id);

        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('customers');
        }

        // Check if customer has policies
        if ($this->Customer_model->has_policies($id)) {
            $this->session->set_flashdata('error', 'Cannot delete customer with existing policies');
            redirect('customers/view/' . $id);
        }

        if ($this->Customer_model->delete_customer($id)) {
            $this->session->set_flashdata('success', 'Customer deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete customer');
        }

        redirect('customers');
    }

    /**
     * Upload KYC document
     */
    public function upload_document($customer_id) {
        if (!$this->input->post()) {
            show_404();
        }

        $customer = $this->Customer_model->get_customer($customer_id);
        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Customer not found']);
            return;
        }

        $config['upload_path'] = './uploads/kyc/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
        $config['max_size'] = 5120; // 5MB
        $config['file_name'] = 'kyc_' . $customer_id . '_' . time();

        // Create directory if not exists
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->upload->initialize($config);

        if ($this->upload->do_upload('document')) {
            $file_data = $this->upload->data();

            $document_data = [
                'customer_id' => $customer_id,
                'document_type' => $this->input->post('document_type'),
                'document_name' => $this->input->post('document_name'),
                'file_name' => $file_data['file_name'],
                'file_path' => $config['upload_path'] . $file_data['file_name'],
                'file_size' => $file_data['file_size'],
                'file_type' => $file_data['file_ext'],
                'uploaded_by' => $this->session->userdata('user_id') ?: 1
            ];

            if ($this->Customer_model->insert_document($document_data)) {
                echo json_encode(['success' => true, 'message' => 'Document uploaded successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save document']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => $this->upload->display_errors('', '')]);
        }
    }

    /**
     * Update KYC status
     */
    public function update_kyc_status($customer_id) {
        if (!$this->input->post()) {
            show_404();
        }

        $kyc_status = $this->input->post('kyc_status');
        $notes = $this->input->post('notes');

        $data = [
            'kyc_status' => $kyc_status,
            'kyc_verified_at' => ($kyc_status == 'approved') ? date('Y-m-d H:i:s') : NULL,
            'kyc_verified_by' => ($kyc_status == 'approved') ? ($this->session->userdata('user_id') ?: 1) : NULL
        ];

        if ($this->Customer_model->update_customer($customer_id, $data)) {
            // Log activity
            $this->Customer_model->log_activity($customer_id, 'KYC status updated to: ' . $kyc_status . ($notes ? ' - ' . $notes : ''));

            echo json_encode(['success' => true, 'message' => 'KYC status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update KYC status']);
        }
    }

    /**
     * Toggle portal access
     */
    public function toggle_portal($customer_id) {
        $customer = $this->Customer_model->get_customer($customer_id);

        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Customer not found']);
            return;
        }

        $new_status = !$customer->portal_access;

        if ($this->Customer_model->update_customer($customer_id, ['portal_access' => $new_status])) {
            $this->Customer_model->log_activity($customer_id, 'Portal access ' . ($new_status ? 'enabled' : 'disabled'));
            echo json_encode(['success' => true, 'portal_access' => $new_status]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update portal access']);
        }
    }

    /**
     * Check email unique (for edit form validation)
     */
    public function check_email_unique($email, $customer_id) {
        $exists = $this->Customer_model->check_email_exists($email, $customer_id);
        if ($exists) {
            $this->form_validation->set_message('check_email_unique', 'The {field} is already in use');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Export customers to CSV
     */
    public function export() {
        $customers = $this->Customer_model->get_all_customers();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Code', 'Name', 'Type', 'Email', 'Phone', 'Emirates ID', 'Status', 'KYC Status', 'Created Date']);

        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer->code,
                $customer->name,
                ucfirst($customer->customer_type),
                $customer->email,
                $customer->phone,
                $customer->emirates_id,
                $customer->is_active ? 'Active' : 'Inactive',
                ucfirst($customer->kyc_status),
                date('d/m/Y', strtotime($customer->created_at))
            ]);
        }

        fclose($output);
    }
}
