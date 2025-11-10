<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#------------------------------------    
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------    

class Receipt extends MX_Controller
{
    
    public function __construct() {
        parent::__construct();
        
        $this->load->model(array(
            'receipt_model','account/Accounts_model'
        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
        
    }

    public function customer_autocomplete(){
        $customer_id    = $this->input->post('customer_id',TRUE);
        $customer_info  = $this->receipt_model->customer_search($customer_id);

        $list[''] = '';
        foreach ($customer_info as $value) {
            $json_customer[] = array('label'=>$value['customer_name'],'value'=>$value['customer_id']);
        } 
        echo json_encode($json_customer);
    }
    
    public function cloudsubset_broker_receipt() {
        $data['broker'] = $this->receipt_model->broker_list();
        $data['title']  = 'Broker Receipt';
        $data['module'] = "receipt";
        $data['page']   = "broker_form";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_broker_receipt_search() {
        if($this->input->post('broker_id')!= '' || $this->session->userdata('search') != ''){
            
            $data['brokers'] = $this->receipt_model->broker_list();
            $broker_id = trim($this->input->post('broker_id', TRUE));
            if ($broker_id == '') $broker_id = $this->session->userdata('search');
            else $this->session->set_userdata('search',$broker_id);
            $broker      = $this->db->select('*')->from('broker_information')->where('broker_id', $broker_id)->get()->row();
            
            $config["base_url"]         = base_url('broker_receipt_search/');
            $config["total_rows"]       = $this->receipt_model->broker_receipt_list_count($broker_id);
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
            $page                = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
            $data["links"]       = $this->pagination->create_links();
            
            
            $data['broker_id'] = $broker_id;
            $data['broker'] = $broker;
            $data['broker_receipts'] = $this->receipt_model->broker_receipt_list($broker_id,$config["per_page"], $page);
            $data['module'] = "receipt";
            $data['page'] = "broker_receipt_list";
            echo Modules::run('template/layout', $data);
        } else {
            $data['brokers'] = $this->receipt_model->broker_list();
            $data['title']  = 'Broker Receipt';
            $data['module'] = "receipt";
            $data['page']   = "broker_receipt_list";
            echo Modules::run('template/layout', $data);
        }
    }
    
    public function cloudsubset_broker_receipt_edit($id) {
        $data['broker_receipt'] = $this->receipt_model->broker_receipt_details($id);
        $data['broker_receipt_dates'] = $this->receipt_model->broker_receipt_date_details($data['broker_receipt']->invoice_id);
        $data['title']  = 'Salesman receipt';
        $data['module'] = "receipt";
        $data['page']   = "broker_receipt_edit";
        echo Modules::run('template/layout', $data);
    }

    public function cloudsubset_update_broker_receipt($id){
        $this->form_validation->set_rules('broker_pr_id', display('broker_pr_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount') ,'required');
        $this->form_validation->set_rules('due_amount_new',display('due_amount'),'required');
        $this->form_validation->set_rules('paid_amount_new',display('paid_amount'),'required');
        
       
        if ($this->form_validation->run() === true && $this->input->post('paid_amount')>0) { 
            $update = $this->receipt_model->broker_receipt_update($id);
            
            if(!empty($update)){
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['broker_receipt'] = $this->receipt_model->broker_receipt_details($id);
                
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
        redirect("broker_receipt_edit/".$id);
    }

    public function cloudsubset_broker_receipt_clear(){
        $this->form_validation->set_rules('broker_pr_id[]', 'Id' ,'required');
        
        
       
        if ($this->form_validation->run() === true) { 
            $update = $this->receipt_model->broker_receipt_clear();
            // echo $update;exit;
            if(!empty($update)){
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                // $data['broker_receipt'] = $this->receipt_model->broker_receipt_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
                redirect('broker_receipt_search');
            }else{
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
                redirect('broker_receipt_search');
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
    }

    public function cloudsubset_broker_receipt_payment(){
        $this->form_validation->set_rules('broker_pr_id[]', 'Id' ,'required');
        $this->form_validation->set_rules('broker_id', display('broker_id') ,'required');
       
        if ($this->form_validation->run() === true) {
            
            $data['broker_receipts'] = $this->receipt_model->broker_receipt_details_product();
            $data["broker_pr_id"] = $this->input->post('broker_pr_id');
            $data["broker_id"] = $this->input->post('broker_id');
            $data["title"] = display("debit_voucher");
            $data["acc"] = $this->receipt_model->getTransationHead();
            $data["voucher_no"] = $this->receipt_model->voNO();
            $data["crcc"] = $this->receipt_model->getCashbankHead();
            // $data['brokers'] = $this->input->post('broker_pr_id');
            $data['title']  = 'Broker Receipt';
            $data['module'] = "receipt";
            $data['page']   = "broker_receipt_payment_list";
            echo Modules::run('template/layout', $data);
            // $update = $this->receipt_model->broker_receipt_update($id);
            
            // if(!empty($update)){
            //     // $data['status'] = true;
            //     // $data['message'] = display('update_successfully');
            //     $data['broker_receipt'] = $this->receipt_model->broker_receipt_details($id);
                
            //     $this->session->set_flashdata('message', display('update_successfully'));
            // }else{
            //     // $data['status'] = false;
            //     // $data['exception'] = 'Please Try Again';
            //     $this->session->set_flashdata('exception', display('please_try_again'));
            // }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
    }
    
    public function cloudsubset_broker_receipt_detail($id,$broker_id) {
        
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
        $this->db->where('b.inv_id', $id);
        $this->db->where('b.broker_id', $broker_id);
        $query = $this->db->get();
        $broker_receipt= $query->row();
        
        $code = '<input class="form-control" type="hidden" size="100" name="broker_pr_id[]" required readonly="readonly" value="' .  html_escape($broker_receipt->broker_pr_id) .'" tabindex="4" />';
        $code .= '<input class="form-control" type="hidden" size="100" name="broker_id[]" required readonly="readonly" value="' .  html_escape($broker_receipt->broker_id) .'" tabindex="4" /></td>';
        $code .= '<td><input class="form-control invoice_id" type="text" size="100" name="invoice_id" required readonly="readonly" value="' . html_escape($broker_receipt->inv_id) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control" type="text" size="100" name="product_name" required readonly="readonly" value="' . html_escape($broker_receipt->product_name) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control broker_price" type="text" size="100" name="broker_price"  required readonly="readonly" value="' . html_escape($broker_receipt->broker_price) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control paid_amount_old" type="text" size="100" name="paid_amount_old" required readonly="readonly" value="' . html_escape($broker_receipt->paid_amount) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control due_amount_old" type="text" size="100" name="due_amount_old" required readonly="readonly" value="' . html_escape($broker_receipt->due_amount) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control paid_amount" type="text" size="100" name="paid_amount" required value="' . html_escape($broker_receipt->due_amount) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control paid_amount_new" type="text" size="100" name="paid_amount_new" required readonly="readonly" value="' . html_escape($broker_receipt->paid_amount + $broker_receipt->due_amount) . '" tabindex="4" /></td>';
        $code .= '<td><input class="form-control due_amount_new" type="text" size="100" name="due_amount_new" required readonly="readonly" value="0.00" tabindex="4" /></td>';
        $code .= '<td><button class="btn btn-danger red text-right" type="button" value="Delete" onclick="deleteRowReceipt(this)"><i class="fa fa-trash-o"></i></button></td>';
        echo json_encode($code);
    }

    public function cloudsubset_broker_receipt_payments_update(){
        $this->form_validation->set_rules('broker_pr_id[]', display('broker_pr_id') ,'required');
        $this->form_validation->set_rules('paid_amount[]', display('paid_amount') ,'required');
        $this->form_validation->set_rules('due_amount_new[]',display('due_amount'),'required');
        $this->form_validation->set_rules('paid_amount_new[]',display('paid_amount'),'required');
        
       
        // if ($this->form_validation->run() === true && $this->input->post('paid_amount')>0) { 
        //     $update = $this->receipt_model->broker_receipt_update($id);
       
        if ($this->form_validation->run() === true) { 
            
            for($i=0;$i<count($this->input->post('broker_pr_id'));$i++){
            // var_dump($this->input->post());exit;
                $update = $this->receipt_model->broker_receipt_updates($this->input->post('broker_pr_id')[$i],$this->input->post('paid_amount_new')[$i],$this->input->post('due_amount_new')[$i],$this->input->post('paid_amount')[$i]);
            }
            if(!empty($update)){
                $this->session->set_flashdata('message', display('update_successfully'));
                redirect('broker_receipt_search');
            }else{
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
                redirect('broker_receipt_search');
            }
        
        }else{
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
    }
    
    // public function cloudsubset_customer_payment() {
    //     $data['customer'] = $this->receipt_model->customer_list();
    //     $data['title']  = 'Salesman Commission';
    //     $data['module'] = "receipt";
    //     $data['page']   = "customer_form";
    //     echo Modules::run('template/layout', $data);
    // }
    
    // public function cloudsubset_customer_payment_search() {
    //     if($this->input->post('customer_id')!= ''){
    //         $customer_id = trim($this->input->post('customer_id', TRUE));
    //         $customer      = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
            
    //         $config["base_url"]         = base_url('customer_payment_search/');
    //         $config["total_rows"]       = $this->receipt_model->customer_payment_list_count($customer_id);
    //         $config["per_page"]         = 10;
    //         $config["uri_segment"]      = 2;
    //         $config["num_links"]        = 5;
    //         $config['full_tag_open']    = "<ul class='pagination'>";
    //         $config['full_tag_close']   = "</ul>";
    //         $config['num_tag_open']     = '<li>';
    //         $config['num_tag_close']    = '</li>';
    //         $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
    //         $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
    //         $config['next_tag_open']    = "<li>";
    //         $config['next_tag_close']   = "</li>";
    //         $config['prev_tag_open']    = "<li>";
    //         $config['prev_tagl_close']  = "</li>";
    //         $config['first_tag_open']   = "<li>";
    //         $config['first_tagl_close'] = "</li>";
    //         $config['last_tag_open']    = "<li>";
    //         $config['last_tagl_close']  = "</li>";
    //         $this->pagination->initialize($config);
    //         $page                = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
    //         $data["links"]       = $this->pagination->create_links();
            
            
    //         $data['customer_id'] = $customer_id;
    //         $data['customer'] = $customer;
    //         $data['customer_payments'] = $this->receipt_model->customer_payment_list($customer_id,$config["per_page"], $page);
    //         $data['module'] = "receipt";
    //         $data['page'] = "customer_payment_list";
    //         echo Modules::run('template/layout', $data);
    //     } else {
    //         redirect('customer_payment_search');
    //     }
    // }
    
    // public function cloudsubset_customer_payment_edit($id) {
    //     $data['customer_payment'] = $this->receipt_model->customer_payment_details($id);
    //     $data['customer_payment_dates'] = $this->receipt_model->customer_payment_date_details($data['customer_payment']->invoice_id);
    //     $data['title']  = 'Salesman Commission';
    //     $data['module'] = "receipt";
    //     $data['page']   = "customer_payment_edit";
    //     echo Modules::run('template/layout', $data);
    // }

    // public function cloudsubset_update_customer_payment($id){
    //     $this->form_validation->set_rules('customer_pr_id', display('customer_pr_id') ,'required');
    //     // $this->form_validation->set_rules('customer_id', display('customer_id') ,'required');
    //     $this->form_validation->set_rules('paid_amount', display('paid_amount') ,'required');
    //     $this->form_validation->set_rules('due_amount_new',display('due_amount'),'required');
    //     $this->form_validation->set_rules('paid_amount_new',display('paid_amount'),'required');
        
       
    //     if ($this->form_validation->run() === true) { 
    //         $update = $this->receipt_model->customer_payment_update($id);
            
    //         if(!empty($update)){
    //             // $data['status'] = true;
    //             // $data['message'] = display('update_successfully');
    //             $data['customer_payment'] = $this->receipt_model->customer_payment_details($id);
                
    //             $this->session->set_flashdata('message', display('update_successfully'));
    //         }else{
    //             // $data['status'] = false;
    //             // $data['exception'] = 'Please Try Again';
    //             $this->session->set_flashdata('exception', display('please_try_again'));
    //         }
        
    //     }else{
    //         // $data['status'] = false;
    //         // $data['exception'] = validation_errors();  
    //         $this->session->set_flashdata('exception', validation_errors());
    //     }
    //     redirect("customer_payment_edit/".$id);
    // }
    
    // public function payment_invoice() {
    //     $finyear = $this->input->post('finyear', true);
        
    //     if ($finyear <= 0) {
    //         $this->session->set_flashdata('exception', 'Please Create Financial Year First');
    //         redirect("payment_form");
    //     } else {
            
    //         $invoice_id = $this->receipt_model->payment_invoice_entry();
    //         if($invoice_id){
    //             $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
    //             if ($setting_data[0]['is_autoapprove_v'] == 1) {
                    
    //                 $new = $this->autoapprove($invoice_id);
    //             }
                
                
    //             $this->session->set_flashdata(array(
    //                 'message' => display('successfully_added')
    //             ));
                
    //             redirect("invoice_payment_details/" . $invoice_id);
    //         }
    //     }
    // }
    
    public function autoapprove($invoice_id) {
        
        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $invoice_id)->where('status', 0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
        
    }
}