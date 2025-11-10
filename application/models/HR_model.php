<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HR_model extends CI_Model {

    // Employees
    public function get_employees() {
        $this->db->select('employees.*, departments.name as department_name');
        $this->db->from('employees');
        $this->db->join('departments', 'departments.id = employees.department_id', 'left');
        $this->db->order_by('employees.name', 'ASC');
        return $this->db->get()->result();
    }

    public function insert_employee($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('employees', $data);
        return $this->db->insert_id();
    }

    public function generate_employee_number() {
        $year = date('Y');
        $this->db->like('employee_no', 'EMP-' . $year, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('employees')->row();

        $new_num = $last ? ((int)substr($last->employee_no, -4)) + 1 : 1;
        return 'EMP-' . $year . '-' . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }

    // Departments
    public function get_departments() {
        $this->db->select('departments.*, COUNT(employees.id) as employee_count');
        $this->db->from('departments');
        $this->db->join('employees', 'employees.department_id = departments.id', 'left');
        $this->db->group_by('departments.id');
        return $this->db->get()->result();
    }

    // Leaves
    public function get_leaves() {
        $this->db->select('employee_leaves.*, employees.name as employee_name, leave_types.name as leave_type_name');
        $this->db->from('employee_leaves');
        $this->db->join('employees', 'employees.id = employee_leaves.employee_id', 'left');
        $this->db->join('leave_types', 'leave_types.id = employee_leaves.leave_type_id', 'left');
        $this->db->order_by('from_date', 'DESC');
        return $this->db->get()->result();
    }

    // Payroll
    public function get_payroll($month) {
        $this->db->select('employees.*, departments.name as department_name');
        $this->db->from('employees');
        $this->db->join('departments', 'departments.id = employees.department_id', 'left');
        $this->db->where('employees.status', 'active');
        return $this->db->get()->result();
    }
}
