<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sales Controller
 * Manages quotations, proposals, and sales pipeline
 */
class Sales extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Sales_model');
        $this->load->model('Customer_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
    }

    // List Quotations
    public function index() {
        $search = $this->input->get('search');
        $status = $this->input->get('status');

        $config['base_url'] = base_url('sales/index');
        $config['total_rows'] = $this->Sales_model->count_quotations($search, $status);
        $config['per_page'] = 20;

        $this->load->library('pagination', $config);
        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        $data['quotations'] = $this->Sales_model->get_quotations($config['per_page'], $offset, $search, $status);
        $data['page_title'] = 'Quotations';
        $data['breadcrumbs'] = [['title' => 'Sales'], ['title' => 'Quotations']];
        $data['main_content'] = 'sales/quotations_list';
        $this->load->view('templates/modern_layout', $data);
    }

    // Add Quotation
    public function add_quotation() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('customer_id', 'Customer', 'required');
            $this->form_validation->set_rules('policy_type_id', 'Policy Type', 'required');
            $this->form_validation->set_rules('sum_insured', 'Sum Insured', 'required|numeric');

            if ($this->form_validation->run() == TRUE) {
                $quotation_data = [
                    'quote_no' => $this->Sales_model->generate_quote_number(),
                    'customer_id' => $this->input->post('customer_id'),
                    'policy_type_id' => $this->input->post('policy_type_id'),
                    'sum_insured' => $this->input->post('sum_insured'),
                    'premium_amount' => $this->input->post('premium_amount'),
                    'valid_until' => $this->input->post('valid_until'),
                    'notes' => $this->input->post('notes'),
                    'status' => 'draft',
                    'created_by' => $this->session->userdata('user_id') ?: 1
                ];

                $quote_id = $this->Sales_model->insert_quotation($quotation_data);
                if ($quote_id) {
                    $this->session->set_flashdata('success', 'Quotation created successfully');
                    redirect('sales/view_quotation/' . $quote_id);
                }
            }
        }

        $data['customers'] = $this->Customer_model->get_all_customers();
        $data['policy_types'] = $this->Sales_model->get_policy_types();
        $data['page_title'] = 'Create Quotation';
        $data['breadcrumbs'] = [['title' => 'Sales'], ['title' => 'Quotations'], ['title' => 'Create']];
        $data['main_content'] = 'sales/quotation_form';
        $this->load->view('templates/modern_layout', $data);
    }

    // View Quotation
    public function view_quotation($id) {
        $quotation = $this->Sales_model->get_quotation($id);
        if (!$quotation) {
            $this->session->set_flashdata('error', 'Quotation not found');
            redirect('sales');
        }

        $data['quotation'] = $quotation;
        $data['page_title'] = 'Quotation - ' . $quotation->quote_no;
        $data['breadcrumbs'] = [['title' => 'Sales'], ['title' => 'Quotations'], ['title' => $quotation->quote_no]];
        $data['main_content'] = 'sales/quotation_view';
        $this->load->view('templates/modern_layout', $data);
    }

    // Convert to Policy
    public function convert_to_policy($quote_id) {
        $quotation = $this->Sales_model->get_quotation($quote_id);
        if (!$quotation) {
            $this->session->set_flashdata('error', 'Quotation not found');
            redirect('sales');
        }

        // Update quotation status
        $this->Sales_model->update_quotation($quote_id, ['status' => 'converted']);

        // Redirect to policy creation with pre-filled data
        $this->session->set_userdata('quote_data', (array)$quotation);
        $this->session->set_flashdata('success', 'Quotation ready for conversion');
        redirect('policies/add?quote_id=' . $quote_id);
    }

    // Sales Pipeline
    public function pipeline() {
        $data['pipeline_stats'] = $this->Sales_model->get_pipeline_statistics();
        $data['page_title'] = 'Sales Pipeline';
        $data['breadcrumbs'] = [['title' => 'Sales'], ['title' => 'Pipeline']];
        $data['main_content'] = 'sales/pipeline';
        $this->load->view('templates/modern_layout', $data);
    }

    // Commission Reports
    public function commissions() {
        $from_date = $this->input->get('from_date') ?: date('Y-m-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-t');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['commissions'] = $this->Sales_model->get_commission_report($from_date, $to_date);
        $data['page_title'] = 'Commission Reports';
        $data['breadcrumbs'] = [['title' => 'Sales'], ['title' => 'Commissions']];
        $data['main_content'] = 'sales/commissions';
        $this->load->view('templates/modern_layout', $data);
    }

    // Export Quotations
    public function export() {
        $quotations = $this->Sales_model->get_all_quotations();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="quotations_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Quote No', 'Customer', 'Policy Type', 'Sum Insured', 'Premium', 'Status', 'Date']);

        foreach ($quotations as $quote) {
            fputcsv($output, [
                $quote->quote_no,
                $quote->customer_name,
                $quote->policy_type_name,
                $quote->sum_insured,
                $quote->premium_amount,
                ucfirst($quote->status),
                date('d/m/Y', strtotime($quote->created_at))
            ]);
        }

        fclose($output);
    }
}
