<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#------------------------------------    
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------    

class Payment extends MX_Controller
{
    
    public function __construct() {
        parent::__construct();
        
        $this->load->model(array(
            'payment_model'
        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
        
    }

    public function customer_autocomplete(){
        $customer_id    = $this->input->post('customer_id',TRUE);
        $customer_info  = $this->payment_model->customer_search($customer_id);

        $list[''] = '';
        foreach ($customer_info as $value) {
            $json_customer[] = array('label'=>$value['customer_name'],'value'=>$value['customer_id']);
        } 
        echo json_encode($json_customer);
    }
    
    // public function cloudsubset_salesman_commission() {
    //     $data['salesman'] = $this->payment_model->salesman_list();
    //     $data['title']  = 'Salesman Commission';
    //     $data['module'] = "payment";
    //     $data['page']   = "salesman_form";
    //     echo Modules::run('template/layout', $data);
    // }
    
    public function cloudsubset_salesman_commission_search($salesman_id = '') {
        $data['salesmans'] = $this->payment_model->salesman_list();
        if($this->input->post('salesman_id')!= '' || $salesman_id !=''){
            if($this->input->post('salesman_id'))
                $salesman_id = trim($this->input->post('salesman_id', TRUE));
            if ($salesman_id == '') $salesman_id = $this->session->userdata('search');
            else $this->session->set_userdata('search',$salesman_id);
            
            $salesman      = $this->db->select('*')->from('salesman_information')->where('salesman_id', $salesman_id)->get()->row();
            
            $config["base_url"]         = base_url('salesman_commission_search/'.$salesman_id.'/');
            $config["total_rows"]       = $this->payment_model->salesman_commission_list_count($salesman_id);
            $config["per_page"]         = 10;
            $config["uri_segment"]      = 3;
            $config["num_links"]        = 5;
            $config['full_tag_open']    = "<ul class='pagination'>";
            $config['full_tag_close']   = "</ul>";
            $config['num_tag_open']     = '<li>';
            $config['num_tag_close']    = '</li>';
            $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
            $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
            $config['next_tag_open']    = "<li>";
            $config['next_tag_close']   = "</li>";
            $config['prev_tag_open']    = "<li>";
            $config['prev_tagl_close']  = "</li>";
            $config['first_tag_open']   = "<li>";
            $config['first_tagl_close'] = "</li>";
            $config['last_tag_open']    = "<li>";
            $config['last_tagl_close']  = "</li>";
            $this->pagination->initialize($config);
            $page                = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data["links"]       = $this->pagination->create_links();
            
            
            $data['salesman_id'] = $salesman_id;
            $data['salesman'] = $salesman;
            
            $data['salesman_commissions'] = $this->payment_model->salesman_commission_list($salesman_id,$config["per_page"], $page);
            $data['module'] = "payment";
            $data['page'] = "salesman_commission_list";
            echo Modules::run('template/layout', $data);
        } else {
            $data['title']  = 'Salesman Commission';
            $data['module'] = "payment";
            $data['page']   = "salesman_commission_list";
            echo Modules::run('template/layout', $data);
        }
    }
    
    public function cloudsubset_salesman_commission_edit($id) {
        $data['salesman_commission'] = $this->payment_model->salesman_commission_details($id);
        $data['salesman_commission_dates'] = $this->payment_model->salesman_commission_date_details($data['salesman_commission']->invoice_id);
        $data['title']  = 'Salesman Commission';
        $data['module'] = "payment";
        $data['page']   = "salesman_commission_edit";
        echo Modules::run('template/layout', $data);
    }

    public function cloudsubset_update_salesman_commission($id){
        $this->form_validation->set_rules('salesman_pr_id', display('salesman_pr_id') ,'required');
        // $this->form_validation->set_rules('salesman_id', display('salesman_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount') ,'required');
        $this->form_validation->set_rules('due_amount_new',display('due_amount'),'required');
        $this->form_validation->set_rules('paid_amount_new',display('paid_amount'),'required');
        
       
        if ($this->form_validation->run() === true) { 
            $update = $this->payment_model->salesman_commission_update($id);
            
            if(!empty($update)){
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['salesman_commission'] = $this->payment_model->salesman_commission_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            }else{
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("salesman_commission_edit/".$id);
    }
    
    public function cloudsubset_salesman_commission_clear(){
        $this->form_validation->set_rules('salesman_pr_id[]', 'Id' ,'required');
        $data['salesman_id'] = trim($this->input->post('salesman_id', TRUE));
        
        if ($this->form_validation->run() === true) { 
            $update = $this->payment_model->salesman_commission_clear();
            if(!empty($update)){
                $this->session->set_flashdata('message', display('update_successfully'));
                redirect('salesman_commission_search');
            }else{
                $this->session->set_flashdata('exception', display('please_try_again'));
                redirect('salesman_commission_search');
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
    }
    
    public function cloudsubset_supplier_payment_search($supplier_id='') {
            
        $data['supplier'] = $this->payment_model->supplier_list();
        if($this->input->post('supplier_id')!= '' || $supplier_id!=''){ 
            $data['supplier'] = $this->payment_model->supplier_list();
            if($this->input->post('supplier_id') || $this->session->userdata('search') != '')
                $supplier_id = trim($this->input->post('supplier_id', TRUE));
            if ($supplier_id == '') $supplier_id = $this->session->userdata('search');
            else $this->session->set_userdata('search',$supplier_id);
                
            $supplier      = $this->db->select('*')->from('supplier_information')->where('supplier_id', $supplier_id)->get()->row();
            
            $config["base_url"]         = base_url('supplier_payment_search/'.$supplier_id.'/');
            $config["total_rows"]       = $this->payment_model->supplier_payment_list_count($supplier_id);
            $config["per_page"]         = 10;
            $config["uri_segment"]      = 3;
            $config["num_links"]        = 5;
            $config['full_tag_open']    = "<ul class='pagination'>";
            $config['full_tag_close']   = "</ul>";
            $config['num_tag_open']     = '<li>';
            $config['num_tag_close']    = '</li>';
            $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
            $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
            $config['next_tag_open']    = "<li>";
            $config['next_tag_close']   = "</li>";
            $config['prev_tag_open']    = "<li>";
            $config['prev_tagl_close']  = "</li>";
            $config['first_tag_open']   = "<li>";
            $config['first_tagl_close'] = "</li>";
            $config['last_tag_open']    = "<li>";
            $config['last_tagl_close']  = "</li>";
            $this->pagination->initialize($config);
            $page                = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data["links"]       = $this->pagination->create_links();
            
            
            $data['supplier_id'] = $supplier_id;
            // $data['supplier'] = $supplier;
            $data['supplier_payments'] = $this->payment_model->supplier_payment_list($supplier_id,$config["per_page"], $page);
            $data['module'] = "payment";
            $data['page'] = "supplier_payment_list";
            echo Modules::run('template/layout', $data);
        } else {
            $data['supplier'] = $this->payment_model->supplier_list();
            $data['title']  = 'Supplier Commission';
            $data['module'] = "payment";
            $data['page']   = "supplier_payment_list";
            echo Modules::run('template/layout', $data);
        }
    }
    
    public function cloudsubset_supplier_payment_edit($id) {
        $data['supplier_payment'] = $this->payment_model->supplier_payment_details($id);
        $data['supplier_payment_dates'] = $this->payment_model->supplier_payment_date_details($data['supplier_payment']->invoice_id);
        $data['title']  = 'Salesman Commission';
        $data['module'] = "payment";
        $data['page']   = "supplier_payment_edit";
        echo Modules::run('template/layout', $data);
    }

    public function cloudsubset_update_supplier_payment($id){
        $this->form_validation->set_rules('supplier_pr_id', display('supplier_pr_id') ,'required');
        // $this->form_validation->set_rules('supplier_id', display('supplier_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount') ,'required');
        $this->form_validation->set_rules('due_amount_new',display('due_amount'),'required');
        $this->form_validation->set_rules('paid_amount_new',display('paid_amount'),'required');
        
       
        if ($this->form_validation->run() === true) { 
            $update = $this->payment_model->supplier_payment_update($id);
            
            if(!empty($update)){
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['supplier_payment'] = $this->payment_model->supplier_payment_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            }else{
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("supplier_payment_edit/".$id);
    }
    
    public function cloudsubset_supplier_payment_clear(){
        // var_dump($this->input->post('supplier_pr_id'));exit;
        $this->form_validation->set_rules('supplier_pr_id[]', 'Id' ,'required');
        
        if ($this->form_validation->run() === true) { 
            $update = $this->receipt_model->supplier_payment_clear();
            // echo $update;exit;
            if(!empty($update)){
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                // $data['supplier_payment'] = $this->receipt_model->supplier_payment_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
                redirect('supplier_payment_search');
            }else{
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
                redirect('supplier_payment_search');
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
    }
    
    public function cloudsubset_customer_payment() {
        $data['customer'] = $this->payment_model->customer_list();
        $data['title']  = 'Salesman Commission';
        $data['module'] = "payment";
        $data['page']   = "customer_form";
        echo Modules::run('template/layout', $data);
    }

    public function cloudsubset_customer_autocomplete(){
        $customer_id    = $this->input->post('customer_id',TRUE);
        $customer_info  = $this->payment_model->customer_search($customer_id);

        $list[''] = '';
        foreach ($customer_info as $value) {
            $json_customer[] = array('label'=>$value['customer_name'],'value'=>$value['customer_id']);
        } 
        echo json_encode($json_customer);
    }
    
    public function cloudsubset_customer_payment_search($customer_id='') {
        if($this->input->post('customer_id')!= '' || $customer_id!='' || $this->session->userdata('search') != ''){
            $customer_id = trim($this->input->post('customer_id', TRUE));
            $customer      = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
            
            $config["base_url"]         = base_url('customer_payment_search/'.$customer_id.'/');
            $config["total_rows"]       = $this->payment_model->customer_payment_list_count($customer_id);
            $config["per_page"]         = 10;
            $config["uri_segment"]      = 2;
            $config["num_links"]        = 5;
            $config['full_tag_open']    = "<ul class='pagination'>";
            $config['full_tag_close']   = "</ul>";
            $config['num_tag_open']     = '<li>';
            $config['num_tag_close']    = '</li>';
            $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
            $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
            $config['next_tag_open']    = "<li>";
            $config['next_tag_close']   = "</li>";
            $config['prev_tag_open']    = "<li>";
            $config['prev_tagl_close']  = "</li>";
            $config['first_tag_open']   = "<li>";
            $config['first_tagl_close'] = "</li>";
            $config['last_tag_open']    = "<li>";
            $config['last_tagl_close']  = "</li>";
            $this->pagination->initialize($config);
            $page                = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data["links"]       = $this->pagination->create_links();
            
            
            $data['customer_id'] = $customer_id;
            $data['customer'] = $customer;
            $data['customer_payments'] = $this->payment_model->customer_payment_list($customer_id,$config["per_page"], $page);
            $data['module'] = "payment";
            $data['page'] = "customer_payment_list";
            echo Modules::run('template/layout', $data);
        } else {
            // redirect('customer_payment_search');
            $data['title']  = 'Customer Commission';
            $data['module'] = "payment";
            $data['page']   = "customer_payment_list";
            echo Modules::run('template/layout', $data);
        }
    }
    
    public function cloudsubset_customer_payment_edit($id) {
        $data['customer_payment'] = $this->payment_model->customer_payment_details($id);
        $data['customer_payment_dates'] = $this->payment_model->customer_payment_date_details($data['customer_payment']->invoice_id);
        $data['title']  = 'Salesman Commission';
        $data['module'] = "payment";
        $data['page']   = "customer_payment_edit";
        echo Modules::run('template/layout', $data);
    }

    public function cloudsubset_update_customer_payment($id){
        $this->form_validation->set_rules('customer_pr_id', display('customer_pr_id') ,'required');
        // $this->form_validation->set_rules('customer_id', display('customer_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount') ,'required');
        $this->form_validation->set_rules('due_amount_new',display('due_amount'),'required');
        $this->form_validation->set_rules('paid_amount_new',display('paid_amount'),'required');
        
       
        if ($this->form_validation->run() === true) { 
            $update = $this->payment_model->customer_payment_update($id);
            
            if(!empty($update)){
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['customer_payment'] = $this->payment_model->customer_payment_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            }else{
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("customer_payment_edit/".$id);
    }
    
    public function payment_invoice() {
        $finyear = $this->input->post('finyear', true);
        
        if ($finyear <= 0) {
            $this->session->set_flashdata('exception', 'Please Create Financial Year First');
            redirect("payment_form");
        } else {
            
            $invoice_id = $this->payment_model->payment_invoice_entry();
            if($invoice_id){
                $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                if ($setting_data[0]['is_autoapprove_v'] == 1) {
                    
                    $new = $this->autoapprove($invoice_id);
                }
                
                
                $this->session->set_flashdata(array(
                    'message' => display('successfully_added')
                ));
                
                redirect("invoice_payment_details/" . $invoice_id);
            }
        }
    }
    
    public function autoapprove($invoice_id) {
        
        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $invoice_id)->where('status', 0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
        
    }
}