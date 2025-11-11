<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Receipts Controller
 * Handles receipt and payment vouchers with items and journal entries
 */
class Receipts extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Receipt_model');
        $this->load->library('session');
        $this->load->helper('url');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    /**
     * List all receipts with pagination and filters
     */
    public function index() {
        $data['title'] = 'Receipt Vouchers';

        // Pagination
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;

        // Filters
        $filters = array(
            'search' => $this->input->get('search'),
            'voucher_type' => $this->input->get('voucher_type'),
            'payment_method' => $this->input->get('payment_method'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $data['receipts'] = $this->Receipt_model->get_receipts($limit, $offset, $filters);
        $data['total_records'] = $this->Receipt_model->count_receipts($filters);
        $data['statistics'] = $this->Receipt_model->get_statistics();
        $data['filters'] = $filters;

        $this->load->view('templates/header', $data);
        $this->load->view('receipts/list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * List all payment vouchers
     */
    public function payments() {
        $data['title'] = 'Payment Vouchers';

        // Pagination
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;

        // Filters
        $filters = array(
            'search' => $this->input->get('search'),
            'voucher_type' => 'payment',
            'payment_method' => $this->input->get('payment_method'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $data['receipts'] = $this->Receipt_model->get_receipts($limit, $offset, $filters);
        $data['total_records'] = $this->Receipt_model->count_receipts($filters);
        $data['statistics'] = $this->Receipt_model->get_payment_statistics();
        $data['filters'] = $filters;

        $this->load->view('templates/header', $data);
        $this->load->view('receipts/payments_list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new receipt voucher
     */
    public function add() {
        $data['title'] = 'New Receipt Voucher';

        if ($this->input->post()) {
            // Validate input
            $this->load->library('form_validation');
            $this->form_validation->set_rules('voucher_date', 'Voucher Date', 'required');
            $this->form_validation->set_rules('party_name', 'Party Name', 'required');
            $this->form_validation->set_rules('total_amount', 'Total Amount', 'required|numeric');
            $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');

            if ($this->form_validation->run() === TRUE) {
                // Prepare receipt data
                $receipt_data = array(
                    'voucher_number' => $this->Receipt_model->generate_voucher_number('receipt'),
                    'voucher_type' => 'receipt',
                    'voucher_date' => $this->input->post('voucher_date'),
                    'party_name' => $this->input->post('party_name'),
                    'party_type' => $this->input->post('party_type'),
                    'party_id' => $this->input->post('party_id'),
                    'payment_method' => $this->input->post('payment_method'),
                    'bank_account_id' => $this->input->post('bank_account_id'),
                    'cheque_number' => $this->input->post('cheque_number'),
                    'cheque_date' => $this->input->post('cheque_date'),
                    'total_amount' => $this->input->post('total_amount'),
                    'narration' => $this->input->post('narration'),
                    'status' => 'approved',
                    'created_by' => $this->session->userdata('user_id'),
                    'company_id' => $this->session->userdata('company_id'),
                    'branch_id' => $this->session->userdata('branch_id'),
                    'financial_year_id' => $this->session->userdata('financial_year_id')
                );

                // Get receipt items
                $items = $this->input->post('items');

                // Insert receipt with items and create journal entry
                $receipt_id = $this->Receipt_model->insert_receipt($receipt_data, $items);

                if ($receipt_id) {
                    $this->session->set_flashdata('success', 'Receipt voucher created successfully');
                    redirect('receipts/view/' . $receipt_id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to create receipt voucher');
                }
            }
        }

        // Load dropdown data
        $data['customers'] = $this->Receipt_model->get_customers();
        $data['accounts'] = $this->Receipt_model->get_accounts();
        $data['bank_accounts'] = $this->Receipt_model->get_bank_accounts();

        $this->load->view('templates/header', $data);
        $this->load->view('receipts/form', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new payment voucher
     */
    public function add_payment() {
        $data['title'] = 'New Payment Voucher';

        if ($this->input->post()) {
            // Validate input
            $this->load->library('form_validation');
            $this->form_validation->set_rules('voucher_date', 'Voucher Date', 'required');
            $this->form_validation->set_rules('party_name', 'Party Name', 'required');
            $this->form_validation->set_rules('total_amount', 'Total Amount', 'required|numeric');
            $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');

            if ($this->form_validation->run() === TRUE) {
                // Prepare payment data
                $payment_data = array(
                    'voucher_number' => $this->Receipt_model->generate_voucher_number('payment'),
                    'voucher_type' => 'payment',
                    'voucher_date' => $this->input->post('voucher_date'),
                    'party_name' => $this->input->post('party_name'),
                    'party_type' => $this->input->post('party_type'),
                    'party_id' => $this->input->post('party_id'),
                    'payment_method' => $this->input->post('payment_method'),
                    'bank_account_id' => $this->input->post('bank_account_id'),
                    'cheque_number' => $this->input->post('cheque_number'),
                    'cheque_date' => $this->input->post('cheque_date'),
                    'total_amount' => $this->input->post('total_amount'),
                    'narration' => $this->input->post('narration'),
                    'status' => 'approved',
                    'created_by' => $this->session->userdata('user_id'),
                    'company_id' => $this->session->userdata('company_id'),
                    'branch_id' => $this->session->userdata('branch_id'),
                    'financial_year_id' => $this->session->userdata('financial_year_id')
                );

                // Get payment items
                $items = $this->input->post('items');

                // Insert payment with items and create journal entry
                $payment_id = $this->Receipt_model->insert_payment($payment_data, $items);

                if ($payment_id) {
                    $this->session->set_flashdata('success', 'Payment voucher created successfully');
                    redirect('receipts/view/' . $payment_id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to create payment voucher');
                }
            }
        }

        // Load dropdown data
        $data['suppliers'] = $this->Receipt_model->get_suppliers();
        $data['accounts'] = $this->Receipt_model->get_accounts();
        $data['bank_accounts'] = $this->Receipt_model->get_bank_accounts();

        $this->load->view('templates/header', $data);
        $this->load->view('receipts/payment_form', $data);
        $this->load->view('templates/footer');
    }

    /**
     * View receipt voucher details
     */
    public function view($id) {
        $data['title'] = 'Receipt Voucher Details';
        $data['receipt'] = $this->Receipt_model->get_receipt($id);

        if (!$data['receipt']) {
            show_404();
        }

        $data['items'] = $this->Receipt_model->get_receipt_items($id);
        $data['journal_entry'] = $this->Receipt_model->get_journal_entry($id);

        $this->load->view('templates/header', $data);
        $this->load->view('receipts/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Delete receipt voucher
     */
    public function delete($id) {
        $receipt = $this->Receipt_model->get_receipt($id);

        if (!$receipt) {
            show_404();
        }

        // Check if receipt can be deleted (not posted to accounts)
        if ($receipt->status === 'posted') {
            $this->session->set_flashdata('error', 'Cannot delete posted voucher');
            redirect('receipts');
        }

        if ($this->Receipt_model->delete_receipt($id)) {
            $this->session->set_flashdata('success', 'Receipt voucher deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete receipt voucher');
        }

        redirect('receipts');
    }

    /**
     * Print receipt voucher
     */
    public function print_voucher($id) {
        $data['receipt'] = $this->Receipt_model->get_receipt($id);

        if (!$data['receipt']) {
            show_404();
        }

        $data['items'] = $this->Receipt_model->get_receipt_items($id);
        $data['company'] = $this->Receipt_model->get_company_info();

        $this->load->view('receipts/print', $data);
    }

    /**
     * Export receipts to CSV
     */
    public function export() {
        $filters = array(
            'search' => $this->input->get('search'),
            'voucher_type' => $this->input->get('voucher_type'),
            'payment_method' => $this->input->get('payment_method'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date')
        );

        $receipts = $this->Receipt_model->get_receipts(10000, 0, $filters);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="receipts_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, array('Voucher Number', 'Type', 'Date', 'Party Name', 'Payment Method', 'Amount', 'Status'));

        // CSV data
        foreach ($receipts as $receipt) {
            fputcsv($output, array(
                $receipt->voucher_number,
                ucfirst($receipt->voucher_type),
                $receipt->voucher_date,
                $receipt->party_name,
                $receipt->payment_method,
                $receipt->total_amount,
                ucfirst($receipt->status)
            ));
        }

        fclose($output);
    }
}
