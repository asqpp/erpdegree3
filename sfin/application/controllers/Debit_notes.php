<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Debit Notes Controller
 * Handles debit notes issued to suppliers
 */
class Debit_notes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Debit_note_model');
        $this->load->library('session');
        $this->load->helper('url');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    /**
     * List all debit notes
     */
    public function index() {
        $data['title'] = 'Debit Notes';

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

        $data['debit_notes'] = $this->Debit_note_model->get_debit_notes($limit, $offset, $filters);
        $data['total_records'] = $this->Debit_note_model->count_debit_notes($filters);
        $data['statistics'] = $this->Debit_note_model->get_statistics();
        $data['filters'] = $filters;

        $this->load->view('templates/header', $data);
        $this->load->view('debit_notes/list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new debit note
     */
    public function add() {
        $data['title'] = 'New Debit Note';

        if ($this->input->post()) {
            // Validate input
            $this->load->library('form_validation');
            $this->form_validation->set_rules('debit_note_date', 'Debit Note Date', 'required');
            $this->form_validation->set_rules('supplier_name', 'Supplier Name', 'required');
            $this->form_validation->set_rules('subtotal', 'Subtotal', 'required|numeric');

            if ($this->form_validation->run() === TRUE) {
                // Calculate VAT and total
                $subtotal = $this->input->post('subtotal');
                $vat_percentage = $this->input->post('vat_percentage') ? $this->input->post('vat_percentage') : 5.00;
                $vat_amount = ($subtotal * $vat_percentage) / 100;
                $total_amount = $subtotal + $vat_amount;

                // Prepare debit note data
                $debit_note_data = array(
                    'debit_note_number' => $this->Debit_note_model->generate_debit_note_number(),
                    'debit_note_date' => $this->input->post('debit_note_date'),
                    'supplier_id' => $this->input->post('supplier_id'),
                    'supplier_name' => $this->input->post('supplier_name'),
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

                // Get debit note items
                $items = $this->input->post('items');

                // Insert debit note
                $debit_note_id = $this->Debit_note_model->insert_debit_note($debit_note_data, $items);

                if ($debit_note_id) {
                    $this->session->set_flashdata('success', 'Debit note created successfully');
                    redirect('debit_notes/view/' . $debit_note_id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to create debit note');
                }
            }
        }

        // Load dropdown data
        $data['suppliers'] = $this->Debit_note_model->get_suppliers();
        $data['accounts'] = $this->Debit_note_model->get_accounts();

        $this->load->view('templates/header', $data);
        $this->load->view('debit_notes/form', $data);
        $this->load->view('templates/footer');
    }

    /**
     * View debit note details
     */
    public function view($id) {
        $data['title'] = 'Debit Note Details';
        $data['debit_note'] = $this->Debit_note_model->get_debit_note($id);

        if (!$data['debit_note']) {
            show_404();
        }

        $data['items'] = $this->Debit_note_model->get_debit_note_items($id);
        $data['journal_entry'] = $this->Debit_note_model->get_journal_entry($id);

        $this->load->view('templates/header', $data);
        $this->load->view('debit_notes/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Post debit note to accounts
     */
    public function post($id) {
        $debit_note = $this->Debit_note_model->get_debit_note($id);

        if (!$debit_note) {
            show_404();
        }

        if ($debit_note->status !== 'issued') {
            $this->session->set_flashdata('error', 'Only issued debit notes can be posted');
            redirect('debit_notes/view/' . $id);
        }

        if ($this->Debit_note_model->post_debit_note($id)) {
            $this->session->set_flashdata('success', 'Debit note posted to accounts successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to post debit note');
        }

        redirect('debit_notes/view/' . $id);
    }

    /**
     * Print debit note
     */
    public function print_note($id) {
        $data['debit_note'] = $this->Debit_note_model->get_debit_note($id);

        if (!$data['debit_note']) {
            show_404();
        }

        $data['items'] = $this->Debit_note_model->get_debit_note_items($id);
        $data['company'] = $this->Debit_note_model->get_company_info();

        $this->load->view('debit_notes/print', $data);
    }

    /**
     * Delete debit note
     */
    public function delete($id) {
        $debit_note = $this->Debit_note_model->get_debit_note($id);

        if (!$debit_note) {
            show_404();
        }

        if ($debit_note->status === 'posted') {
            $this->session->set_flashdata('error', 'Cannot delete posted debit note');
            redirect('debit_notes');
        }

        if ($this->Debit_note_model->delete_debit_note($id)) {
            $this->session->set_flashdata('success', 'Debit note deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete debit note');
        }

        redirect('debit_notes');
    }

    /**
     * Export debit notes to CSV
     */
    public function export() {
        $filters = array(
            'search' => $this->input->get('search'),
            'status' => $this->input->get('status'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $debit_notes = $this->Debit_note_model->get_debit_notes(10000, 0, $filters);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="debit_notes_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, array('Debit Note Number', 'Date', 'Supplier', 'Reference', 'Amount', 'VAT', 'Total', 'Status'));

        // CSV data
        foreach ($debit_notes as $note) {
            fputcsv($output, array(
                $note->debit_note_number,
                $note->debit_note_date,
                $note->supplier_name,
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
