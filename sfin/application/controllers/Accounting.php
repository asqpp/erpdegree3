<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Accounting Controller
 * Manages chart of accounts, journal entries, AR/AP, and financial statements
 */
class Accounting extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Accounting_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
    }

    // Chart of Accounts
    public function chart_of_accounts() {
        $data['accounts'] = $this->Accounting_model->get_chart_of_accounts();
        $data['page_title'] = 'Chart of Accounts';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'Chart of Accounts']];
        $data['main_content'] = 'accounting/chart_of_accounts';
        $this->load->view('templates/modern_layout', $data);
    }

    // Journal Entries
    public function journal_entries() {
        $config['base_url'] = base_url('accounting/journal_entries');
        $config['total_rows'] = $this->Accounting_model->count_journal_entries();
        $config['per_page'] = 20;

        $this->load->library('pagination', $config);
        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        $data['journal_entries'] = $this->Accounting_model->get_journal_entries($config['per_page'], $offset);
        $data['page_title'] = 'Journal Entries';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'Journal Entries']];
        $data['main_content'] = 'accounting/journal_entries';
        $this->load->view('templates/modern_layout', $data);
    }

    // Add Journal Entry
    public function add_journal_entry() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('entry_date', 'Entry Date', 'required');
            $this->form_validation->set_rules('description', 'Description', 'required');

            if ($this->form_validation->run() == TRUE) {
                $entry_data = [
                    'entry_no' => $this->Accounting_model->generate_entry_number(),
                    'entry_date' => $this->input->post('entry_date'),
                    'description' => $this->input->post('description'),
                    'created_by' => $this->session->userdata('user_id') ?: 1
                ];

                $entry_id = $this->Accounting_model->insert_journal_entry($entry_data);

                // Add line items
                $accounts = $this->input->post('account_id');
                $debits = $this->input->post('debit');
                $credits = $this->input->post('credit');

                foreach ($accounts as $index => $account_id) {
                    if ($account_id) {
                        $line_data = [
                            'journal_entry_id' => $entry_id,
                            'account_id' => $account_id,
                            'debit_amount' => $debits[$index] ?: 0,
                            'credit_amount' => $credits[$index] ?: 0
                        ];
                        $this->Accounting_model->insert_journal_line($line_data);
                    }
                }

                $this->session->set_flashdata('success', 'Journal entry created successfully');
                redirect('accounting/journal_entries');
            }
        }

        $data['accounts'] = $this->Accounting_model->get_chart_of_accounts();
        $data['page_title'] = 'Add Journal Entry';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'Journal Entries'], ['title' => 'Add New']];
        $data['main_content'] = 'accounting/journal_entry_form';
        $this->load->view('templates/modern_layout', $data);
    }

    // Accounts Receivable
    public function accounts_receivable() {
        $data['receivables'] = $this->Accounting_model->get_accounts_receivable();
        $data['page_title'] = 'Accounts Receivable';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'Accounts Receivable']];
        $data['main_content'] = 'accounting/accounts_receivable';
        $this->load->view('templates/modern_layout', $data);
    }

    // Accounts Payable
    public function accounts_payable() {
        $data['payables'] = $this->Accounting_model->get_accounts_payable();
        $data['page_title'] = 'Accounts Payable';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'Accounts Payable']];
        $data['main_content'] = 'accounting/accounts_payable';
        $this->load->view('templates/modern_layout', $data);
    }

    // Profit & Loss Statement
    public function profit_loss() {
        $from_date = $this->input->get('from_date') ?: date('Y-01-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['revenue'] = $this->Accounting_model->get_revenue($from_date, $to_date);
        $data['expenses'] = $this->Accounting_model->get_expenses($from_date, $to_date);
        $data['net_profit'] = $data['revenue'] - $data['expenses'];

        $data['page_title'] = 'Profit & Loss Statement';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'P&L']];
        $data['main_content'] = 'accounting/profit_loss';
        $this->load->view('templates/modern_layout', $data);
    }

    // Balance Sheet
    public function balance_sheet() {
        $as_of_date = $this->input->get('as_of_date') ?: date('Y-m-d');

        $data['as_of_date'] = $as_of_date;
        $data['assets'] = $this->Accounting_model->get_assets($as_of_date);
        $data['liabilities'] = $this->Accounting_model->get_liabilities($as_of_date);
        $data['equity'] = $this->Accounting_model->get_equity($as_of_date);

        $data['page_title'] = 'Balance Sheet';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'Balance Sheet']];
        $data['main_content'] = 'accounting/balance_sheet';
        $this->load->view('templates/modern_layout', $data);
    }

    // VAT Reports
    public function vat_reports() {
        $from_date = $this->input->get('from_date') ?: date('Y-m-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-t');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['vat_summary'] = $this->Accounting_model->get_vat_summary($from_date, $to_date);

        $data['page_title'] = 'VAT Reports';
        $data['breadcrumbs'] = [['title' => 'Accounting'], ['title' => 'VAT Reports']];
        $data['main_content'] = 'accounting/vat_reports';
        $this->load->view('templates/modern_layout', $data);
    }
}
