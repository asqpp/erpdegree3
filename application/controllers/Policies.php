<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Policies Controller
 * Manages insurance policy operations including issuance, endorsements, renewals, and cancellations
 */
class Policies extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Policy_model');
        $this->load->model('Customer_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'upload', 'session']);

        // Check if user is logged in
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
    }

    /**
     * List all policies
     */
    public function index() {
        // Get search parameters
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $policy_type = $this->input->get('policy_type');
        $customer_id = $this->input->get('customer_id');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Pagination configuration
        $config['base_url'] = base_url('policies/index');
        $config['total_rows'] = $this->Policy_model->count_policies($search, $status, $policy_type, $customer_id, $date_from, $date_to);
        $config['per_page'] = 20;
        $config['use_page_numbers'] = TRUE;

        $this->load->library('pagination', $config);

        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $config['per_page'];

        // Get policies
        $data['policies'] = $this->Policy_model->get_policies($config['per_page'], $offset, $search, $status, $policy_type, $customer_id, $date_from, $date_to);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($config['total_rows'] / $config['per_page']);

        // Search parameters
        $data['search'] = $search;
        $data['filter_status'] = $status;
        $data['filter_policy_type'] = $policy_type;
        $data['filter_customer_id'] = $customer_id;
        $data['filter_date_from'] = $date_from;
        $data['filter_date_to'] = $date_to;

        // Get filter options
        $data['policy_types'] = $this->Policy_model->get_policy_types();
        $data['customers'] = $this->Customer_model->get_all_customers();

        $data['page_title'] = 'Policies';
        $data['breadcrumbs'] = [
            ['title' => 'Policies']
        ];
        $data['main_content'] = 'policies/list';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * View policy details
     */
    public function view($id) {
        $policy = $this->Policy_model->get_policy($id);

        if (!$policy) {
            $this->session->set_flashdata('error', 'Policy not found');
            redirect('policies');
        }

        $data['policy'] = $policy;
        $data['customer'] = $this->Customer_model->get_customer($policy->customer_id);
        $data['premium_schedule'] = $this->Policy_model->get_premium_schedule($id);
        $data['endorsements'] = $this->Policy_model->get_endorsements($id);
        $data['claims'] = $this->Policy_model->get_policy_claims($id);
        $data['documents'] = $this->Policy_model->get_policy_documents($id);
        $data['activities'] = $this->Policy_model->get_policy_activities($id);

        $data['page_title'] = 'Policy Details - ' . $policy->policy_no;
        $data['breadcrumbs'] = [
            ['title' => 'Policies', 'url' => base_url('policies')],
            ['title' => $policy->policy_no]
        ];
        $data['main_content'] = 'policies/view';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Issue new policy
     */
    public function add() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('customer_id', 'Customer', 'required|numeric');
            $this->form_validation->set_rules('policy_type_id', 'Policy Type', 'required|numeric');
            $this->form_validation->set_rules('issue_date', 'Issue Date', 'required');
            $this->form_validation->set_rules('expiry_date', 'Expiry Date', 'required');
            $this->form_validation->set_rules('sum_insured', 'Sum Insured', 'required|numeric');
            $this->form_validation->set_rules('premium_amount', 'Premium Amount', 'required|numeric');

            if ($this->form_validation->run() == TRUE) {
                $issue_date = $this->input->post('issue_date');
                $expiry_date = $this->input->post('expiry_date');
                $sum_insured = $this->input->post('sum_insured');
                $premium_amount = $this->input->post('premium_amount');
                $currency_id = $this->input->post('currency_id') ?: 1;
                $vat_rate = $this->input->post('vat_rate') ?: 5;

                // Calculate VAT
                $vat_amount = ($premium_amount * $vat_rate) / 100;
                $total_premium = $premium_amount + $vat_amount;

                $policy_data = [
                    'policy_no' => $this->Policy_model->generate_policy_number($this->input->post('policy_type_id')),
                    'customer_id' => $this->input->post('customer_id'),
                    'policy_type_id' => $this->input->post('policy_type_id'),
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    'sum_insured' => $sum_insured,
                    'premium_amount' => $premium_amount,
                    'vat_rate' => $vat_rate,
                    'vat_amount' => $vat_amount,
                    'total_premium' => $total_premium,
                    'currency_id' => $currency_id,
                    'exchange_rate' => $this->input->post('exchange_rate') ?: 1.000000,
                    'payment_mode' => $this->input->post('payment_mode') ?: 'yearly',
                    'payment_frequency' => $this->input->post('payment_frequency') ?: 'annual',
                    'agent_id' => $this->input->post('agent_id') ?: NULL,
                    'broker_id' => $this->input->post('broker_id') ?: NULL,
                    'commission_rate' => $this->input->post('commission_rate') ?: 0,
                    'commission_amount' => 0,
                    'vehicle_make' => $this->input->post('vehicle_make'),
                    'vehicle_model' => $this->input->post('vehicle_model'),
                    'vehicle_year' => $this->input->post('vehicle_year'),
                    'vehicle_plate_no' => $this->input->post('vehicle_plate_no'),
                    'vehicle_chassis_no' => $this->input->post('vehicle_chassis_no'),
                    'remarks' => $this->input->post('remarks'),
                    'status' => 'active',
                    'created_by' => $this->session->userdata('user_id') ?: 1
                ];

                // Calculate commission
                if ($policy_data['commission_rate'] > 0) {
                    $policy_data['commission_amount'] = ($premium_amount * $policy_data['commission_rate']) / 100;
                }

                $policy_id = $this->Policy_model->insert_policy($policy_data);

                if ($policy_id) {
                    // Create premium schedule based on payment frequency
                    $this->_create_premium_schedule($policy_id, $issue_date, $expiry_date, $total_premium, $policy_data['payment_frequency']);

                    // Log activity
                    $this->Policy_model->log_activity($policy_id, 'Policy issued');

                    $this->session->set_flashdata('success', 'Policy issued successfully');
                    redirect('policies/view/' . $policy_id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to issue policy');
                }
            }
        }

        // Get customer_id from query string if provided
        $data['selected_customer_id'] = $this->input->get('customer_id');

        $data['page_title'] = 'Issue New Policy';
        $data['breadcrumbs'] = [
            ['title' => 'Policies', 'url' => base_url('policies')],
            ['title' => 'Issue New']
        ];
        $data['customers'] = $this->Customer_model->get_all_customers();
        $data['policy_types'] = $this->Policy_model->get_policy_types();
        $data['currencies'] = $this->Policy_model->get_currencies();
        $data['agents'] = $this->Policy_model->get_agents();
        $data['brokers'] = $this->Policy_model->get_brokers();
        $data['main_content'] = 'policies/form';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Edit policy
     */
    public function edit($id) {
        $policy = $this->Policy_model->get_policy($id);

        if (!$policy) {
            $this->session->set_flashdata('error', 'Policy not found');
            redirect('policies');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('expiry_date', 'Expiry Date', 'required');
            $this->form_validation->set_rules('sum_insured', 'Sum Insured', 'required|numeric');

            if ($this->form_validation->run() == TRUE) {
                $policy_data = [
                    'expiry_date' => $this->input->post('expiry_date'),
                    'sum_insured' => $this->input->post('sum_insured'),
                    'vehicle_make' => $this->input->post('vehicle_make'),
                    'vehicle_model' => $this->input->post('vehicle_model'),
                    'vehicle_year' => $this->input->post('vehicle_year'),
                    'vehicle_plate_no' => $this->input->post('vehicle_plate_no'),
                    'vehicle_chassis_no' => $this->input->post('vehicle_chassis_no'),
                    'remarks' => $this->input->post('remarks'),
                    'updated_by' => $this->session->userdata('user_id') ?: 1
                ];

                if ($this->Policy_model->update_policy($id, $policy_data)) {
                    $this->Policy_model->log_activity($id, 'Policy updated');
                    $this->session->set_flashdata('success', 'Policy updated successfully');
                    redirect('policies/view/' . $id);
                } else {
                    $this->session->set_flashdata('error', 'Failed to update policy');
                }
            }
        }

        $data['policy'] = $policy;
        $data['page_title'] = 'Edit Policy - ' . $policy->policy_no;
        $data['breadcrumbs'] = [
            ['title' => 'Policies', 'url' => base_url('policies')],
            ['title' => $policy->policy_no, 'url' => base_url('policies/view/' . $id)],
            ['title' => 'Edit']
        ];
        $data['main_content'] = 'policies/edit';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Create endorsement
     */
    public function endorse($policy_id) {
        $policy = $this->Policy_model->get_policy($policy_id);

        if (!$policy) {
            echo json_encode(['success' => false, 'message' => 'Policy not found']);
            return;
        }

        if ($this->input->post()) {
            $endorsement_data = [
                'policy_id' => $policy_id,
                'endorsement_no' => $this->Policy_model->generate_endorsement_number($policy_id),
                'endorsement_date' => $this->input->post('endorsement_date'),
                'endorsement_type' => $this->input->post('endorsement_type'),
                'description' => $this->input->post('description'),
                'premium_adjustment' => $this->input->post('premium_adjustment') ?: 0,
                'sum_insured_adjustment' => $this->input->post('sum_insured_adjustment') ?: 0,
                'effective_date' => $this->input->post('effective_date'),
                'created_by' => $this->session->userdata('user_id') ?: 1
            ];

            $endorsement_id = $this->Policy_model->insert_endorsement($endorsement_data);

            if ($endorsement_id) {
                // Update policy if adjustments
                if ($endorsement_data['premium_adjustment'] != 0 || $endorsement_data['sum_insured_adjustment'] != 0) {
                    $updates = [];
                    if ($endorsement_data['premium_adjustment'] != 0) {
                        $updates['premium_amount'] = $policy->premium_amount + $endorsement_data['premium_adjustment'];
                    }
                    if ($endorsement_data['sum_insured_adjustment'] != 0) {
                        $updates['sum_insured'] = $policy->sum_insured + $endorsement_data['sum_insured_adjustment'];
                    }
                    if (!empty($updates)) {
                        $this->Policy_model->update_policy($policy_id, $updates);
                    }
                }

                $this->Policy_model->log_activity($policy_id, 'Endorsement created: ' . $endorsement_data['endorsement_no']);
                echo json_encode(['success' => true, 'message' => 'Endorsement created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create endorsement']);
            }
        }
    }

    /**
     * Renew policy
     */
    public function renew($policy_id) {
        $old_policy = $this->Policy_model->get_policy($policy_id);

        if (!$old_policy) {
            $this->session->set_flashdata('error', 'Policy not found');
            redirect('policies');
        }

        if ($this->input->post()) {
            $issue_date = $this->input->post('issue_date');
            $expiry_date = $this->input->post('expiry_date');
            $premium_amount = $this->input->post('premium_amount');
            $sum_insured = $this->input->post('sum_insured');
            $vat_rate = $this->input->post('vat_rate') ?: 5;

            // Calculate VAT
            $vat_amount = ($premium_amount * $vat_rate) / 100;
            $total_premium = $premium_amount + $vat_amount;

            // Create new policy
            $new_policy_data = [
                'policy_no' => $this->Policy_model->generate_policy_number($old_policy->policy_type_id),
                'customer_id' => $old_policy->customer_id,
                'policy_type_id' => $old_policy->policy_type_id,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                'sum_insured' => $sum_insured,
                'premium_amount' => $premium_amount,
                'vat_rate' => $vat_rate,
                'vat_amount' => $vat_amount,
                'total_premium' => $total_premium,
                'currency_id' => $old_policy->currency_id,
                'exchange_rate' => $old_policy->exchange_rate,
                'payment_mode' => $old_policy->payment_mode,
                'payment_frequency' => $old_policy->payment_frequency,
                'agent_id' => $old_policy->agent_id,
                'broker_id' => $old_policy->broker_id,
                'commission_rate' => $old_policy->commission_rate,
                'commission_amount' => ($premium_amount * $old_policy->commission_rate) / 100,
                'vehicle_make' => $old_policy->vehicle_make,
                'vehicle_model' => $old_policy->vehicle_model,
                'vehicle_year' => $old_policy->vehicle_year,
                'vehicle_plate_no' => $this->input->post('vehicle_plate_no') ?: $old_policy->vehicle_plate_no,
                'vehicle_chassis_no' => $old_policy->vehicle_chassis_no,
                'renewed_from_policy_id' => $policy_id,
                'remarks' => 'Renewed from policy: ' . $old_policy->policy_no,
                'status' => 'active',
                'created_by' => $this->session->userdata('user_id') ?: 1
            ];

            $new_policy_id = $this->Policy_model->insert_policy($new_policy_data);

            if ($new_policy_id) {
                // Create premium schedule
                $this->_create_premium_schedule($new_policy_id, $issue_date, $expiry_date, $total_premium, $new_policy_data['payment_frequency']);

                // Update old policy status
                $this->Policy_model->update_policy($policy_id, [
                    'status' => 'renewed',
                    'renewed_to_policy_id' => $new_policy_id
                ]);

                $this->Policy_model->log_activity($policy_id, 'Policy renewed to: ' . $new_policy_data['policy_no']);
                $this->Policy_model->log_activity($new_policy_id, 'Policy renewed from: ' . $old_policy->policy_no);

                $this->session->set_flashdata('success', 'Policy renewed successfully');
                redirect('policies/view/' . $new_policy_id);
            } else {
                $this->session->set_flashdata('error', 'Failed to renew policy');
            }
        }

        $data['policy'] = $old_policy;
        $data['page_title'] = 'Renew Policy - ' . $old_policy->policy_no;
        $data['breadcrumbs'] = [
            ['title' => 'Policies', 'url' => base_url('policies')],
            ['title' => $old_policy->policy_no, 'url' => base_url('policies/view/' . $policy_id)],
            ['title' => 'Renew']
        ];
        $data['main_content'] = 'policies/renew';

        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Cancel policy
     */
    public function cancel($policy_id) {
        if (!$this->input->post()) {
            show_404();
        }

        $policy = $this->Policy_model->get_policy($policy_id);
        if (!$policy) {
            echo json_encode(['success' => false, 'message' => 'Policy not found']);
            return;
        }

        $cancellation_data = [
            'policy_id' => $policy_id,
            'cancellation_date' => $this->input->post('cancellation_date'),
            'cancellation_reason' => $this->input->post('cancellation_reason'),
            'refund_amount' => $this->input->post('refund_amount') ?: 0,
            'cancelled_by' => $this->session->userdata('user_id') ?: 1
        ];

        if ($this->Policy_model->cancel_policy($cancellation_data)) {
            // Update policy status
            $this->Policy_model->update_policy($policy_id, ['status' => 'cancelled']);

            $this->Policy_model->log_activity($policy_id, 'Policy cancelled: ' . $cancellation_data['cancellation_reason']);
            echo json_encode(['success' => true, 'message' => 'Policy cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel policy']);
        }
    }

    /**
     * Calculate premium (AJAX)
     */
    public function calculate_premium() {
        $policy_type_id = $this->input->post('policy_type_id');
        $sum_insured = $this->input->post('sum_insured');
        $vehicle_year = $this->input->post('vehicle_year');
        $customer_age = $this->input->post('customer_age');

        // Simple premium calculation (you can make this more complex based on requirements)
        $base_rate = 0.03; // 3% of sum insured

        // Adjust rate based on policy type
        $policy_type = $this->Policy_model->get_policy_type($policy_type_id);
        if ($policy_type) {
            $base_rate = $policy_type->base_rate ?: 0.03;
        }

        // Vehicle age factor
        $current_year = date('Y');
        if ($vehicle_year && $vehicle_year < $current_year - 10) {
            $base_rate += 0.01; // Add 1% for old vehicles
        }

        // Customer age factor (for health/life insurance)
        if ($customer_age && $customer_age > 50) {
            $base_rate += 0.005; // Add 0.5% for older customers
        }

        $premium_amount = $sum_insured * $base_rate;
        $vat_rate = 5;
        $vat_amount = ($premium_amount * $vat_rate) / 100;
        $total_premium = $premium_amount + $vat_amount;

        echo json_encode([
            'success' => true,
            'premium_amount' => round($premium_amount, 2),
            'vat_amount' => round($vat_amount, 2),
            'total_premium' => round($total_premium, 2)
        ]);
    }

    /**
     * Export policies to CSV
     */
    public function export() {
        $policies = $this->Policy_model->get_all_policies();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="policies_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Policy No', 'Customer', 'Type', 'Issue Date', 'Expiry Date', 'Sum Insured', 'Premium', 'Status']);

        foreach ($policies as $policy) {
            fputcsv($output, [
                $policy->policy_no,
                $policy->customer_name,
                $policy->policy_type_name,
                date('d/m/Y', strtotime($policy->issue_date)),
                date('d/m/Y', strtotime($policy->expiry_date)),
                $policy->sum_insured,
                $policy->total_premium,
                ucfirst($policy->status)
            ]);
        }

        fclose($output);
    }

    /**
     * Private: Create premium schedule
     */
    private function _create_premium_schedule($policy_id, $issue_date, $expiry_date, $total_premium, $frequency) {
        $schedules = [];

        switch ($frequency) {
            case 'annual':
                $schedules[] = [
                    'policy_id' => $policy_id,
                    'installment_no' => 1,
                    'due_date' => $issue_date,
                    'amount' => $total_premium,
                    'status' => 'pending'
                ];
                break;

            case 'semi-annual':
                $installment_amount = $total_premium / 2;
                for ($i = 0; $i < 2; $i++) {
                    $due_date = date('Y-m-d', strtotime($issue_date . ' +' . ($i * 6) . ' months'));
                    $schedules[] = [
                        'policy_id' => $policy_id,
                        'installment_no' => $i + 1,
                        'due_date' => $due_date,
                        'amount' => $installment_amount,
                        'status' => 'pending'
                    ];
                }
                break;

            case 'quarterly':
                $installment_amount = $total_premium / 4;
                for ($i = 0; $i < 4; $i++) {
                    $due_date = date('Y-m-d', strtotime($issue_date . ' +' . ($i * 3) . ' months'));
                    $schedules[] = [
                        'policy_id' => $policy_id,
                        'installment_no' => $i + 1,
                        'due_date' => $due_date,
                        'amount' => $installment_amount,
                        'status' => 'pending'
                    ];
                }
                break;

            case 'monthly':
                $months = $this->_calculate_months($issue_date, $expiry_date);
                $installment_amount = $total_premium / $months;
                for ($i = 0; $i < $months; $i++) {
                    $due_date = date('Y-m-d', strtotime($issue_date . ' +' . $i . ' months'));
                    $schedules[] = [
                        'policy_id' => $policy_id,
                        'installment_no' => $i + 1,
                        'due_date' => $due_date,
                        'amount' => $installment_amount,
                        'status' => 'pending'
                    ];
                }
                break;
        }

        foreach ($schedules as $schedule) {
            $this->Policy_model->insert_premium_schedule($schedule);
        }
    }

    /**
     * Private: Calculate months between two dates
     */
    private function _calculate_months($start_date, $end_date) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        return $interval->y * 12 + $interval->m + ($interval->d > 0 ? 1 : 0);
    }
}
