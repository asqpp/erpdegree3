<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * HR Controller
 * Basic HR management for employees, departments, and leave
 */
class HR extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('HR_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
    }

    // Employee List
    public function employees() {
        $data['employees'] = $this->HR_model->get_employees();
        $data['page_title'] = 'Employees';
        $data['breadcrumbs'] = [['title' => 'HR'], ['title' => 'Employees']];
        $data['main_content'] = 'hr/employees';
        $this->load->view('templates/modern_layout', $data);
    }

    // Add Employee
    public function add_employee() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('department_id', 'Department', 'required');

            if ($this->form_validation->run() == TRUE) {
                $employee_data = [
                    'employee_no' => $this->HR_model->generate_employee_number(),
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department_id' => $this->input->post('department_id'),
                    'designation' => $this->input->post('designation'),
                    'join_date' => $this->input->post('join_date'),
                    'basic_salary' => $this->input->post('basic_salary'),
                    'status' => 'active',
                    'created_by' => $this->session->userdata('user_id') ?: 1
                ];

                $employee_id = $this->HR_model->insert_employee($employee_data);
                if ($employee_id) {
                    $this->session->set_flashdata('success', 'Employee added successfully');
                    redirect('hr/employees');
                }
            }
        }

        $data['departments'] = $this->HR_model->get_departments();
        $data['page_title'] = 'Add Employee';
        $data['breadcrumbs'] = [['title' => 'HR'], ['title' => 'Employees'], ['title' => 'Add']];
        $data['main_content'] = 'hr/employee_form';
        $this->load->view('templates/modern_layout', $data);
    }

    // Departments
    public function departments() {
        $data['departments'] = $this->HR_model->get_departments();
        $data['page_title'] = 'Departments';
        $data['breadcrumbs'] = [['title' => 'HR'], ['title' => 'Departments']];
        $data['main_content'] = 'hr/departments';
        $this->load->view('templates/modern_layout', $data);
    }

    // Leave Management
    public function leaves() {
        $data['leaves'] = $this->HR_model->get_leaves();
        $data['page_title'] = 'Leave Management';
        $data['breadcrumbs'] = [['title' => 'HR'], ['title' => 'Leaves']];
        $data['main_content'] = 'hr/leaves';
        $this->load->view('templates/modern_layout', $data);
    }

    // Payroll
    public function payroll() {
        $month = $this->input->get('month') ?: date('Y-m');

        $data['month'] = $month;
        $data['payroll'] = $this->HR_model->get_payroll($month);
        $data['page_title'] = 'Payroll Management';
        $data['breadcrumbs'] = [['title' => 'HR'], ['title' => 'Payroll']];
        $data['main_content'] = 'hr/payroll';
        $this->load->view('templates/modern_layout', $data);
    }
}
