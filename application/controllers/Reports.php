<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reports Controller
 * Centralized reporting system for all modules
 */
class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Reports_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
    }

    // Reports Dashboard
    public function index() {
        $data['page_title'] = 'Reports & Analytics';
        $data['breadcrumbs'] = [['title' => 'Reports']];
        $data['main_content'] = 'reports/dashboard';
        $this->load->view('templates/modern_layout', $data);
    }

    // Financial Reports
    public function financial() {
        $from_date = $this->input->get('from_date') ?: date('Y-01-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['revenue_report'] = $this->Reports_model->get_revenue_report($from_date, $to_date);
        $data['expense_report'] = $this->Reports_model->get_expense_report($from_date, $to_date);

        $data['page_title'] = 'Financial Reports';
        $data['breadcrumbs'] = [['title' => 'Reports'], ['title' => 'Financial']];
        $data['main_content'] = 'reports/financial';
        $this->load->view('templates/modern_layout', $data);
    }

    // Insurance Reports
    public function insurance() {
        $from_date = $this->input->get('from_date') ?: date('Y-01-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['policy_report'] = $this->Reports_model->get_policy_report($from_date, $to_date);
        $data['claims_report'] = $this->Reports_model->get_claims_report($from_date, $to_date);

        $data['page_title'] = 'Insurance Reports';
        $data['breadcrumbs'] = [['title' => 'Reports'], ['title' => 'Insurance']];
        $data['main_content'] = 'reports/insurance';
        $this->load->view('templates/modern_layout', $data);
    }

    // Sales Reports
    public function sales() {
        $from_date = $this->input->get('from_date') ?: date('Y-m-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-t');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['sales_report'] = $this->Reports_model->get_sales_report($from_date, $to_date);
        $data['agent_performance'] = $this->Reports_model->get_agent_performance($from_date, $to_date);

        $data['page_title'] = 'Sales Reports';
        $data['breadcrumbs'] = [['title' => 'Reports'], ['title' => 'Sales']];
        $data['main_content'] = 'reports/sales';
        $this->load->view('templates/modern_layout', $data);
    }

    // Compliance Reports
    public function compliance() {
        $data['vat_report'] = $this->Reports_model->get_vat_compliance();
        $data['insurance_authority_report'] = $this->Reports_model->get_ia_compliance();

        $data['page_title'] = 'Compliance Reports';
        $data['breadcrumbs'] = [['title' => 'Reports'], ['title' => 'Compliance']];
        $data['main_content'] = 'reports/compliance';
        $this->load->view('templates/modern_layout', $data);
    }

    // Customer Analytics
    public function customers() {
        $data['customer_stats'] = $this->Reports_model->get_customer_analytics();

        $data['page_title'] = 'Customer Analytics';
        $data['breadcrumbs'] = [['title' => 'Reports'], ['title' => 'Customer Analytics']];
        $data['main_content'] = 'reports/customers';
        $this->load->view('templates/modern_layout', $data);
    }

    // Export Report (Generic)
    public function export($report_type) {
        $from_date = $this->input->get('from_date') ?: date('Y-01-01');
        $to_date = $this->input->get('to_date') ?: date('Y-m-d');

        $data = $this->Reports_model->export_report($report_type, $from_date, $to_date);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $report_type . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Write headers
        if (!empty($data)) {
            fputcsv($output, array_keys((array)$data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($output, (array)$row);
            }
        }

        fclose($output);
    }
}
