<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Credit Notes Controller
 * Handles credit notes issued to customers
 */
class Credit_notes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Credit_note_model');
        $this->load->library('session');
        $this->load->helper('url');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    /**
     * List all credit notes
     */
    public function index() {
        $data['title'] = 'Credit Notes';

        // Pagination
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;

        // Filters
        $filters = array(
            'search' => $this->input->get('search'),
            'status' => $this->input->get('status'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $data['credit_notes'] = $this->Credit_note_model->get_credit_notes($limit, $offset, $filters);
        $data['total_records'] = $this->Credit_note_model->count_credit_notes($filters);
        $data['statistics'] = $this->Credit_note_model->get_statistics();
        $data['filters'] = $filters;

        $this->load->view('templates/header', $data);
        $this->load->view('credit_notes/list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new credit note
     */
    public function add() {
        $data['title'] = 'New Credit Note';

        if ($this->input->post()) {
            // Validate input
            $this->load->library('form_validation');
            $this->form_validation->set_rules('credit_note_date', 'Credit Note Date', 'required');
            $this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
            $this->form_validation->set_rules('subtotal', 'Subtotal', 'required|numeric');

            if ($this->form_validation->run() === TRUE) {
                // Calculate VAT and total
                $subtotal = $this->input->post('subtotal');
                $vat_percentage = $this->input->post('vat_percentage') ? $this->input->post('vat_percentage') : 5.00;
                $vat_amount = ($subtotal * $vat_percentage) / 100;
                $total_amount = $subtotal + $vat_amount;

                // Prepare credit note data
                $credit_note_data = array(
                    'credit_note_number' => $this->Credit_note_model->generate_credit_note_number(),
                    'credit_note_date' => $this->input->post('credit_note_date'),
                    'customer_id' => $this->input->post('customer_id'),
                    'customer_name' => $this->input->post('customer_name'),
                    'reference_type' => $this->input->post('reference_type'),
                    'reference_id' => $this->input->post('reference_id'),
                    'reference_number' => $this->input->post('reference_number'),
                    'reason' => $this->input->post('reason'),
                    'subtotal' => $subtotal,
                    'vat_percentage' => $vat_percentage,
                    'vat_amount' => $vat_amount,
                    'total_amount' => $total_amount,
                    'status' => 'issued',
                    'created_by' => $this->session->userdata('user_id'),
                    'company_id' => $this->session->userdata('company_id'),
                    'branch_id' => $this->session->userdata('branch_id'),
                    'financial_year_id' => $this->session->userdata('financial_year_id')
                );

                // Get credit note items
                $items = $this->input->post('items');

                // Insert credit note
                $credit_note_id = $this->Credit_note_model->insert_credit_note($credit_note_data, $items);

                if ($credit_note_id) {
                    $this->session->set_flashdata('success', 'Credit note created successfully');
                    redirect('credit_notes/view/' . $credit_note_id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to create credit note');
                }
            }
        }

        // Load dropdown data
        $data['customers'] = $this->Credit_note_model->get_customers();
        $data['accounts'] = $this->Credit_note_model->get_accounts();

        $this->load->view('templates/header', $data);
        $this->load->view('credit_notes/form', $data);
        $this->load->view('templates/footer');
    }

    /**
     * View credit note details
     */
    public function view($id) {
        $data['title'] = 'Credit Note Details';
        $data['credit_note'] = $this->Credit_note_model->get_credit_note($id);

        if (!$data['credit_note']) {
            show_404();
        }

        $data['items'] = $this->Credit_note_model->get_credit_note_items($id);
        $data['journal_entry'] = $this->Credit_note_model->get_journal_entry($id);

        $this->load->view('templates/header', $data);
        $this->load->view('credit_notes/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Post credit note to accounts
     */
    public function post($id) {
        $credit_note = $this->Credit_note_model->get_credit_note($id);

        if (!$credit_note) {
            show_404();
        }

        if ($credit_note->status !== 'issued') {
            $this->session->set_flashdata('error', 'Only issued credit notes can be posted');
            redirect('credit_notes/view/' . $id);
        }

        if ($this->Credit_note_model->post_credit_note($id)) {
            $this->session->set_flashdata('success', 'Credit note posted to accounts successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to post credit note');
        }

        redirect('credit_notes/view/' . $id);
    }

    /**
     * Print credit note
     */
    public function print_note($id) {
        $data['credit_note'] = $this->Credit_note_model->get_credit_note($id);

        if (!$data['credit_note']) {
            show_404();
        }

        $data['items'] = $this->Credit_note_model->get_credit_note_items($id);
        $data['company'] = $this->Credit_note_model->get_company_info();

        $this->load->view('credit_notes/print', $data);
    }

    /**
     * Delete credit note
     */
    public function delete($id) {
        $credit_note = $this->Credit_note_model->get_credit_note($id);

        if (!$credit_note) {
            show_404();
        }

        if ($credit_note->status === 'posted') {
            $this->session->set_flashdata('error', 'Cannot delete posted credit note');
            redirect('credit_notes');
        }

        if ($this->Credit_note_model->delete_credit_note($id)) {
            $this->session->set_flashdata('success', 'Credit note deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete credit note');
        }

        redirect('credit_notes');
    }

    /**
     * Export credit notes to CSV
     */
    public function export() {
        $filters = array(
            'search' => $this->input->get('search'),
            'status' => $this->input->get('status'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $credit_notes = $this->Credit_note_model->get_credit_notes(10000, 0, $filters);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="credit_notes_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, array('Credit Note Number', 'Date', 'Customer', 'Reference', 'Amount', 'VAT', 'Total', 'Status'));

        // CSV data
        foreach ($credit_notes as $note) {
            fputcsv($output, array(
                $note->credit_note_number,
                $note->credit_note_date,
                $note->customer_name,
                $note->reference_number,
                $note->subtotal,
                $note->vat_amount,
                $note->total_amount,
                ucfirst($note->status)
            ));
        }

        fclose($output);
    }
}
