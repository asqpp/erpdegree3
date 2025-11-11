<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Claims Controller
 * Manages insurance claim registration, investigation, approval, and settlement
 */
class Claims extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Claim_model');
        $this->load->model('Policy_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'upload', 'session']);
    }

    /**
     * List all claims
     */
    public function index() {
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $claim_type = $this->input->get('claim_type');

        $config['base_url'] = base_url('claims/index');
        $config['total_rows'] = $this->Claim_model->count_claims($search, $status, $claim_type);
        $config['per_page'] = 20;

        $this->load->library('pagination', $config);
        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        $data['claims'] = $this->Claim_model->get_claims($config['per_page'], $offset, $search, $status, $claim_type);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($config['total_rows'] / $config['per_page']);

        $data['search'] = $search;
        $data['filter_status'] = $status;
        $data['filter_claim_type'] = $claim_type;
        $data['claim_types'] = $this->Claim_model->get_claim_types();

        $data['page_title'] = 'Claims Management';
        $data['breadcrumbs'] = [['title' => 'Claims']];
        $data['main_content'] = 'claims/list';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * View claim details
     */
    public function view($id) {
        $claim = $this->Claim_model->get_claim($id);
        if (!$claim) {
            $this->session->set_flashdata('error', 'Claim not found');
            redirect('claims');
        }

        $data['claim'] = $claim;
        $data['policy'] = $this->Policy_model->get_policy($claim->policy_id);
        $data['documents'] = $this->Claim_model->get_claim_documents($id);
        $data['activities'] = $this->Claim_model->get_claim_activities($id);

        $data['page_title'] = 'Claim Details - ' . $claim->claim_no;
        $data['breadcrumbs'] = [
            ['title' => 'Claims', 'url' => base_url('claims')],
            ['title' => $claim->claim_no]
        ];
        $data['main_content'] = 'claims/view';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Register new claim
     */
    public function add() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('policy_id', 'Policy', 'required|numeric');
            $this->form_validation->set_rules('claim_type_id', 'Claim Type', 'required|numeric');
            $this->form_validation->set_rules('claim_date', 'Claim Date', 'required');
            $this->form_validation->set_rules('claim_amount', 'Claim Amount', 'required|numeric');

            if ($this->form_validation->run() == TRUE) {
                $claim_data = [
                    'claim_no' => $this->Claim_model->generate_claim_number(),
                    'policy_id' => $this->input->post('policy_id'),
                    'claim_type_id' => $this->input->post('claim_type_id'),
                    'claim_date' => $this->input->post('claim_date'),
                    'loss_date' => $this->input->post('loss_date'),
                    'claim_amount' => $this->input->post('claim_amount'),
                    'description' => $this->input->post('description'),
                    'status' => 'registered',
                    'created_by' => $this->session->userdata('user_id') ?: 1
                ];

                $claim_id = $this->Claim_model->insert_claim($claim_data);
                if ($claim_id) {
                    $this->Claim_model->log_activity($claim_id, 'Claim registered');
                    $this->session->set_flashdata('success', 'Claim registered successfully');
                    redirect('claims/view/' . $claim_id);
                }
            }
        }

        $data['page_title'] = 'Register New Claim';
        $data['breadcrumbs'] = [
            ['title' => 'Claims', 'url' => base_url('claims')],
            ['title' => 'Register New']
        ];
        $data['policies'] = $this->Policy_model->get_all_policies();
        $data['claim_types'] = $this->Claim_model->get_claim_types();
        $data['main_content'] = 'claims/form';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Update claim status
     */
    public function update_status($claim_id) {
        if (!$this->input->post()) {
            show_404();
        }

        $status = $this->input->post('status');
        $notes = $this->input->post('notes');
        $approved_amount = $this->input->post('approved_amount');

        $data = ['status' => $status];

        if ($status == 'approved' && $approved_amount) {
            $data['approved_amount'] = $approved_amount;
            $data['approved_date'] = date('Y-m-d');
            $data['approved_by'] = $this->session->userdata('user_id') ?: 1;
        } elseif ($status == 'settled') {
            $data['settlement_date'] = date('Y-m-d');
            $data['settled_by'] = $this->session->userdata('user_id') ?: 1;
        }

        if ($this->Claim_model->update_claim($claim_id, $data)) {
            $this->Claim_model->log_activity($claim_id, 'Status updated to: ' . $status . ($notes ? ' - ' . $notes : ''));
            echo json_encode(['success' => true, 'message' => 'Claim status updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Export claims to CSV
     */
    public function export() {
        $claims = $this->Claim_model->get_all_claims();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="claims_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Claim No', 'Policy No', 'Type', 'Claim Date', 'Amount', 'Approved', 'Status']);

        foreach ($claims as $claim) {
            fputcsv($output, [
                $claim->claim_no,
                $claim->policy_no,
                $claim->claim_type_name,
                date('d/m/Y', strtotime($claim->claim_date)),
                $claim->claim_amount,
                $claim->approved_amount ?: 0,
                ucfirst($claim->status)
            ]);
        }

        fclose($output);
    }
}
