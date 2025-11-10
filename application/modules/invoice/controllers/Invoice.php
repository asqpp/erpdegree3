<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    #------------------------------------    
    # Author: Cloudsubset
    # Author link: https://www.cloudsubset.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Invoice extends MX_Controller {

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'libraries/pdfparser-master/alt_autoload.php-dist';
        $timezone = $this->db->select('timezone')->from('web_setting')->get()->row();
        date_default_timezone_set($timezone->timezone);
        $this->load->model(array(
            'invoice_model','customer/customer_model','account/Accounts_model')); 
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
          
    }

    function cloudsubset_invoice_form() {
        $data['supplier'] = $this->invoice_model->supplier_list();
        $data['broker'] = $this->invoice_model->broker_list();
        $data['salesman'] = $this->invoice_model->salesman_list();
        $walking_customer      = $this->invoice_model->pos_customer_setup();
        $data['all_pmethod']   = $this->invoice_model->pmethod_dropdown();
        
        $data['customer_name'] = $walking_customer[0]['customer_name'];
        $data['customer_id']   = $walking_customer[0]['customer_id'];
        $data['invoice_no']    = $this->number_generator();
        $data['title']         = display('add_invoice');
        $data['taxes']         = $this->invoice_model->tax_fileds();
        $data['module']        = "invoice";
        $vatortax              = $this->invoice_model->vat_tax_setting();
        if($vatortax->fixed_tax == 1){
            $data['page']          = "add_invoice_form"; 
        }
        if($vatortax->dynamic_tax == 1){
            $data['page']          = "add_invoice_form_dynamic"; 
        }
        echo modules::run('template/layout', $data);
    }

    public function cloudsubset_invoice_list(){
        $data['title']        = display('manage_invoice');
        $data['total_invoice']= $this->invoice_model->count_invoice();
        $data['module']       = "invoice";
        $data['page']         = "invoice"; 
        echo modules::run('template/layout', $data);
    }

    public function CheckInvoiceList(){
        $postData = $this->input->post();
        $data     = $this->invoice_model->getInvoiceList($postData);
        echo json_encode($data);
    } 

    public function delivery_note(){

        $data['invoice_no']    =  $this->input->post('invoice_no',TRUE);
        $data['delivery_note'] = $this->db->select('delivery_note')
        ->from('invoice')->where('invoice_id',$data['invoice_no'])->get()->row()->delivery_note;

        $this->load->view('invoice/delivery_note',$data); 
      }

    public function save_delivery_note($invoice_id){

        $delivery_note = $this->input->post('note',true);
        $data =  array('delivery_note' => $delivery_note);
        
        if ($this->db->where('invoice_id', $invoice_id)->update('invoice', $data)) {
            #set success message
            $this->session->set_flashdata('message', display('save_successfully'));
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        
        redirect("invoice_list");
        
    }
    
    public function cloudsubset_invoice_details($invoice_id = null){
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount  = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                  if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
                if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                    
                }
                if(!empty($invoice_detail[$k]['discount'])){
                    $is_dis_val = $is_dis_val+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt_per'])){
                    $vat_amnt_per = $vat_amnt_per+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt'])){
                    $vat_amnt = $vat_amnt+1;
                    
                }
   
            }
        }


        $totalbal      = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword = $totalbal;
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'             => display('invoice_details'),
        'invoice_id'        => $invoice_detail[0]['invoice_id'],
        'invoice_no'        => $invoice_detail[0]['invoice'],
        'customer_name'     => $invoice_detail[0]['customer_name'],
        'customer_address'  => $invoice_detail[0]['customer_address'],
        'customer_telephone'   => $invoice_detail[0]['customer_telephone'],
        'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
        'customer_email'    => $invoice_detail[0]['customer_email'],
        'final_date'        => $invoice_detail[0]['final_date'],
        'email_address'     => $invoice_detail[0]['email_address'],
        'contact'           => $invoice_detail[0]['contact'],
        'invoice_details'   => $invoice_detail[0]['invoice_details'],
        'total_amount'      => number_format($invoice_detail[0]['total_amount'], 2, '.', ','),
        'subTotal_quantity' => $subTotal_quantity,
        'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_discount_cal'=> $invoice_detail[0]['total_discount'],
        'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
        'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
        'subTotal_amount_cal'=> $subTotal_ammount,
        'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'  => $invoice_detail,
        'am_inword'         => $amount_inword,
        'is_discount'       => $invoice_detail[0]['total_discount']-$invoice_detail[0]['invoice_discount'],
        'users_name'        => $users->first_name.' '.$users->last_name,
        'tax_regno'         => $txregname,
        'is_desc'           => $descript,
        'is_dis_val'        => $is_dis_val,
        'vat_amnt_per'      => $vat_amnt_per,
        'vat_amnt'          => $vat_amnt,
        'is_discount'       => $is_discount,
        'is_serial'         => $isserial,
        'is_unit'           => $isunit,
        );
        $data['module']     = "invoice";
        $data['page']       = "invoice_html"; 
        echo modules::run('template/layout', $data);
    }

    public function cloudsubset_delivery_invoice_details($invoice_id = null){
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount  = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                  if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
                if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                    
                }
                if(!empty($invoice_detail[$k]['discount'])){
                    $is_dis_val = $is_dis_val+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt_per'])){
                    $vat_amnt_per = $vat_amnt_per+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt'])){
                    $vat_amnt = $vat_amnt+1;
                    
                }
   
            }
        }


        $totalbal      = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword = $totalbal;
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'             => display('invoice_details'),
        'invoice_id'        => $invoice_detail[0]['invoice_id'],
        'invoice_no'        => $invoice_detail[0]['invoice'],
        'customer_name'     => $invoice_detail[0]['customer_name'],
        'customer_address'  => $invoice_detail[0]['customer_address'],
        'customer_telephone'=> $invoice_detail[0]['customer_telephone'],
        'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
        'customer_email'    => $invoice_detail[0]['customer_email'],
        'final_date'        => $invoice_detail[0]['final_date'],
        'email_address'     => $invoice_detail[0]['email_address'],
        'contact'           => $invoice_detail[0]['contact'],
        'invoice_details'   => $invoice_detail[0]['invoice_details'],
        'total_amount'      => number_format($invoice_detail[0]['total_amount'], 2, '.', ','),
        'subTotal_quantity' => $subTotal_quantity,
        'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_discount_cal'=> $invoice_detail[0]['total_discount'],
        'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
        'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
        'subTotal_amount_cal'=> $subTotal_ammount,
        'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'  => $invoice_detail,
        'am_inword'         => $amount_inword,
        'is_discount'       => $invoice_detail[0]['total_discount']-$invoice_detail[0]['invoice_discount'],
        'users_name'        => $users->first_name.' '.$users->last_name,
        'tax_regno'         => $txregname,
        'is_desc'           => $descript,
        'is_dis_val'        => $is_dis_val,
        'vat_amnt_per'      => $vat_amnt_per,
        'vat_amnt'          => $vat_amnt,
        'is_discount'       => $is_discount,
        'is_serial'         => $isserial,
        'is_unit'           => $isunit,
        );
        $data['module']     = "invoice";
        $data['page']       = "delivery_invoice_html"; 
        echo modules::run('template/layout', $data);
    }
    
    public function cloudsubset_invoice_pad_print($invoice_id){
           $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
         $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                 if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
                if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                    
                }
                if(!empty($invoice_detail[$k]['discount'])){
                    $is_dis_val = $is_dis_val+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt_per'])){
                    $vat_amnt_per = $vat_amnt_per+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt'])){
                    $vat_amnt = $vat_amnt+1;
                    
                }
            }
        }

        $totalbal      = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword = $this->numbertowords->convert_number($totalbal);
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'            => display('pad_print'),
        'invoice_id'       => $invoice_detail[0]['invoice_id'],
        'invoice_no'       => $invoice_detail[0]['invoice'],
        'customer_name'    => $invoice_detail[0]['customer_name'],
        'customer_address' => $invoice_detail[0]['customer_address'],
        'customer_telephone'=> $invoice_detail[0]['customer_telephone'],
        'customer_mobile'  => $invoice_detail[0]['customer_mobile'],
        'customer_email'   => $invoice_detail[0]['customer_email'],
        'final_date'       => $invoice_detail[0]['final_date'],
        'print_setting'    => $this->invoice_model->cloudsubset_print_settingdata(),
        'invoice_details'  => $invoice_detail[0]['invoice_details'],
        'total_amount'     => number_format($totalbal, 2, '.', ','),
        'subTotal_cartoon' => $subTotal_cartoon,
        'subTotal_quantity'=> $subTotal_quantity,
        'total_vat'        => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
        'invoice_discount' => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
        'total_discount'   => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_tax'        => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'      => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'       => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
         'shipping_cost'   => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data' => $invoice_detail,
        'previous'         => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'am_inword'        => $amount_inword,
        'is_discount'      => $invoice_detail[0]['total_discount']-$invoice_detail[0]['invoice_discount'],
        'is_dis_val'       => $is_dis_val,
        'vat_amnt_per'     => $vat_amnt_per,
        'vat_amnt'         => $vat_amnt,
        'is_discount'      => $is_discount,
        'users_name'       => $users->first_name.' '.$users->last_name,
        'tax_regno'        => $txregname,
        'is_desc'          => $descript,
        'is_serial'        => $isserial,
        'is_unit'          => $isunit,
        );

        $data['module']     = "invoice";
        $data['page']       = "pad_print"; 
        echo modules::run('template/layout', $data);
    }
    
    public function cloudsubset_invoice_pos_print($invoice_id = null){
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }  
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $is_dis_val        = 0;
        $vat_amnt_per      = 0;
        $vat_amnt          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                 if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
                    if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                    
                }
                    if(!empty($invoice_detail[$k]['discount'])){
                    $is_dis_val = $is_dis_val+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt_per'])){
                    $vat_amnt_per = $vat_amnt_per+1;
                    
                }
                    if(!empty($invoice_detail[$k]['vat_amnt'])){
                    $vat_amnt = $vat_amnt+1;
                    
                }
            }
        }

        $payment_method_list = $this->invoice_model->invoice_method_wise_balance($invoice_id);
        $terms_list = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result(); 
        $totalbal = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $user_id  = $invoice_detail[0]['sales_by'];
        $users    = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'                => display('pos_print'),
        'invoice_id'           => $invoice_detail[0]['invoice_id'],
        'invoice_no'           => $invoice_detail[0]['invoice'],
        'customer_name'        => $invoice_detail[0]['customer_name'],
        'customer_address'     => $invoice_detail[0]['customer_address'],
        'customer_telephone'   => $invoice_detail[0]['customer_telephone'],
        'customer_mobile'      => $invoice_detail[0]['customer_mobile'],
        'customer_email'       => $invoice_detail[0]['customer_email'],
        'final_date'           => $invoice_detail[0]['final_date'],
        'invoice_details'      => $invoice_detail[0]['invoice_details'],
        'grand_total'          => $invoice_detail[0]['total_amount'],
        'total_amount'         => number_format($totalbal, 2, '.', ','),
        'subTotal_cartoon'     => $subTotal_cartoon,
        'subTotal_quantity'    => $subTotal_quantity,
        'invoice_discount'     => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
        'total_discount'       => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_vat'            => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
        'total_tax'            => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'     => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'          => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'           => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'shipping_cost'        => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        
        'invoice_all_data'     => $invoice_detail,
        'previous'             => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'is_discount'          => $is_discount,
        'is_dis_val'           => $is_dis_val,
        'vat_amnt_per'         => $vat_amnt_per,
        'vat_amnt'             => $vat_amnt,
        'users_name'           => $users->first_name.' '.$users->last_name,
        'tax_regno'            => $txregname,
        'is_desc'              => $descript,
        'is_serial'            => $isserial,
        'is_unit'              => $isunit,
        'all_discount'         => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'p_method_list'        => $payment_method_list,
        'terms_list'           => $terms_list,

        );

        $data['module']     = "invoice";
        $data['page']       = "pos_print"; 
        echo modules::run('template/layout', $data);

    }
    
    public function cloudsubset_pos_print_direct(){
        $invoice_id = $this->input->post('invoice_id',true);
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }  
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $is_discount       = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                 if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
                    if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                    
                }
            }
        }

 
        $totalbal = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $user_id  = $invoice_detail[0]['sales_by'];
        $users    = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'                => display('pos_print'),
        'invoice_id'           => $invoice_detail[0]['invoice_id'],
        'invoice_no'           => $invoice_detail[0]['invoice'],
        'customer_name'        => $invoice_detail[0]['customer_name'],
        'customer_address'     => $invoice_detail[0]['customer_address'],
        'customer_mobile'      => $invoice_detail[0]['customer_mobile'],
        'customer_telephone'   => $invoice_detail[0]['customer_telephone'],
        'customer_email'       => $invoice_detail[0]['customer_email'],
        'final_date'           => $invoice_detail[0]['final_date'],
        'invoice_details'      => $invoice_detail[0]['invoice_details'],
        'total_amount'         => number_format($totalbal, 2, '.', ','),
        'subTotal_cartoon'     => $subTotal_cartoon,
        'subTotal_quantity'    => $subTotal_quantity,
        'invoice_discount'     => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
        'total_discount'       => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_tax'            => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'     => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'          => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'           => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'shipping_cost'        => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'     => $invoice_detail,
        'previous'             => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
         'is_discount'         => $is_discount,
        'users_name'           => $users->first_name.' '.$users->last_name,
        'tax_regno'            => $txregname,
        'is_desc'              => $descript,
        'is_serial'            => $isserial,
        'is_unit'              => $isunit,
        'url'                  => $this->input->post('url',TRUE),

        );

        $data['module']     = "invoice";
        $data['page']       = "pos_invoice_html_direct"; 
        echo modules::run('template/layout', $data);

    }
    
    public function cloudsubset_download_invoice($invoice_id = null){
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $isunit            = 0;
        $is_discount       = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price']; 
            }
            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                }
                 if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                }
                if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                }
            }
        }

        $currency_details = $this->invoice_model->retrieve_setting_editdata();
        $company_info     = $this->invoice_model->retrieve_company();
        $totalbal         = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword    = $this->numbertowords->convert_number($totalbal);
        $user_id          = $invoice_detail[0]['sales_by'];
        $users            = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'             => display('invoice_details'),
        'invoice_id'        => $invoice_detail[0]['invoice_id'],
        'customer_info'     => $invoice_detail,
        'invoice_no'        => $invoice_detail[0]['invoice'],
        'customer_name'     => $invoice_detail[0]['customer_name'],
        'customer_address'  => $invoice_detail[0]['customer_address'],
        'customer_telephone'=> $invoice_detail[0]['customer_telephone'],
        'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
        'customer_email'    => $invoice_detail[0]['customer_email'],
        'final_date'        => $invoice_detail[0]['final_date'],
        'invoice_details'   => $invoice_detail[0]['invoice_details'],
        'total_amount'      => number_format($invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'], 2, '.', ','),
        'subTotal_quantity' => $subTotal_quantity,
        'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
        'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'  => $invoice_detail,
        'company_info'      => $company_info,
        'currency'          => $currency_details[0]['currency'],
        'position'          => $currency_details[0]['currency_position'],
        'discount_type'     => $currency_details[0]['discount_type'],
        'currency_details'  => $currency_details,
        'am_inword'         => $amount_inword,
        'is_discount'       => $is_discount,
        'users_name'        => $users->first_name.' '.$users->last_name,
        'tax_regno'         => $txregname,
        'is_desc'           => $descript,
        'is_serial'         => $isserial,
        'is_unit'           => $isunit,
        );



        $this->load->library('pdfgenerator');
        $dompdf = new DOMPDF();
        $page = $this->load->view('invoice/invoice_download', $data, true);
        $file_name = time();
        $dompdf->load_html($page,'UTF-8');
        $dompdf->render();
        $output = $dompdf->output();
        @exec("sudo chmod " . "$file_name 777");
        file_put_contents("assets/data/pdf/invoice/$file_name.pdf", $output);
        $filename = $file_name . '.pdf';
        $file_path = base_url() . 'assets/data/pdf/invoice/' . $filename;

        $this->load->helper('download');
        force_download('./assets/data/pdf/invoice/' . $filename, NULL);
        redirect("invoice_list");
        
    }

    public function cloudsubset_manual_sales_insert(){
        $this->form_validation->set_rules('customer_id', display('customer_name') ,'required|max_length[15]');
        $this->form_validation->set_rules('product_id',display('product'),'required|max_length[20]');
        $this->form_validation->set_rules('multipaytype[]',display('payment_type'),'required');
        $this->form_validation->set_rules('total_premium_amount','Total Premium Amount','required|max_length[20]');
        $sup_price = $this->input->post('broker_commission_amount', true);
        $s_id = $this->input->post('supplier_id', true);
        $sal_price = $this->input->post('salesman_commission_amount', true);
        $sa_id = $this->input->post('salesman_id', true);
        
        $normal = $this->input->post('is_normal');

        $finyear = $this->input->post('finyear',true);
        
        if($finyear<=0){
            $data['status'] = false;
            $data['exception'] = 'Please Create Financial Year First';
        }else {
            if ($this->form_validation->run() === true) {
                $incremented_id = $this->number_generator();
                $invoice_id     = $this->invoice_model->invoice_entry($incremented_id);
                if(!empty($invoice_id)){
                    $product_id = (!empty($this->input->post('product_id', true)) ? $this->input->post('product_id', true) : $this->generator(8));
                    // if($this->input->post('invoice_type') == 1 || $this->input->post('invoice_type') == 2) {
                    //     for ($i = 0, $n = count($s_id);$i < $n;$i++)
                    //     {
    
                    //         $supp_id = $s_id[$i];
    
                    //         $supp_prd = array(
                    //             'product_id' => $product_id,
                    //             'supplier_id' => $supp_id,
                    //             'supplier_price' => $sup_price,
                    //         );
    
                    //         $this->db->insert('supplier_product', $supp_prd);
    
                    //         $sall_id = $sa_id[$i];
    
                    //         $supp_prd = array(
                    //             'product_id' => $product_id,
                    //             'salesman_id' => $sall_id,
                    //             'salesman_price' => $sal_price,
                    //             'products_model' => $product_model,
                    //         );
    
                    //         $this->db->insert('salesman_product', $supp_prd);
                    //     }
                    // }
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                    if ($setting_data[0]['is_autoapprove_v'] == 1) {
                        
                        $new = $this->autoapprove($invoice_id);
                    }

                    $data['status']     = true;
                    $data['invoice_id'] = $invoice_id;
                    $data['message']    = display('save_successfully');
                    $mailsetting        = $this->db->select('*')->from('email_config')->get()->result_array();

                    if($mailsetting[0]['isinvoice']==1){
                        $mail  = $this->invoice_pdf_generate($invoice_id);
                        if($mail == 0){
                            $data['exception'] = $this->session->set_userdata(array('error_message' => display('please_config_your_mail_setting')));
                        }
                    }
                    if($normal == 1){
                        $printdata       = $this->invoice_model->cloudsubset_invoice_pos_print_direct($invoice_id);
                        $data['details'] = $this->load->view('invoice/invoice_html_manual', $printdata, true);
                    }else{
                        $printdata       = $this->invoice_model->cloudsubset_invoice_pos_print_direct($invoice_id);
                        $data['details'] = $this->load->view('invoice/pos_print', $printdata, true);
                    }
            
                }else{
                    $data['status']    = false;
                    $data['exception'] = 'Please Try Again';
                }
            
            }else{
                $data['status']    = false;
                $data['exception'] = validation_errors();  
            }
        }
        echo json_encode($data);
    }

    public function autoapprove($invoice_id){

        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo',$invoice_id)->where('status',0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
        
    }

    public function cloudsubset_showpaymentmodal(){
        $is_credit =  $this->input->post('is_credit_edit',TRUE);
        $data['is_credit'] = $is_credit;
        if ($is_credit == 1) {
            # code...
            $data['all_pmethod'] = $this->invoice_model->pmethod_dropdown();
        }else{

            $data['all_pmethod'] = $this->invoice_model->pmethod_dropdown_new();
        }
        $this->load->view('invoice/newpaymentveiw',$data); 
    }
    
    public function cloudsubset_edit_invoice($invoice_id = null){
        $invoice_detail = $this->invoice_model->retrieve_invoice_editdata($invoice_id);
        $vat_tax_info   = $this->invoice_model->vat_tax_setting();
        if ($invoice_detail[0]['is_dynamic'] ==1) {
            if ($invoice_detail[0]['is_dynamic'] != $vat_tax_info->dynamic_tax) {

                $this->session->set_flashdata('exception', 'VAT and TAX are set globally, which is not the same as VAT and TAX on this invoice. (which was configured when the invoice was created). It is not editable.');
                redirect("invoice_list");
            }
            
        }
        elseif ($invoice_detail[0]['is_fixed'] ==1) {
            if ($invoice_detail[0]['is_fixed'] != $vat_tax_info->fixed_tax) {

                $this->session->set_flashdata('exception', 'VAT and TAX are set globally, which is not the same as VAT and TAX on this invoice. (which was configured when the invoice was created). It is not editable.');
                redirect("invoice_list");
            }
        }
     
        $taxinfo        = $this->invoice_model->invoice_taxinfo($invoice_id);
        $taxfield       = $this->db->select('tax_name,default_value')
                                ->from('tax_settings')
                                ->get()
                                ->result_array();
        // $i = 0;
        // if (!empty($invoice_detail)) {
        //     foreach ($invoice_detail as $k => $v) {
        //         $i++;
        //         $invoice_detail[$k]['sl'] = $i;
        //         $stock = $this->invoice_model->stock_qty_check($invoice_detail[$k]['product_id']);
        //         $invoice_detail[$k]['stock_qty'] = $stock + $invoice_detail[$k]['quantity'];
        //     }
        // }

        $currency_details = $this->invoice_model->retrieve_setting_editdata();

        $multi_pay_data = $this->db->select('COAID, Debit')
                        ->from('acc_vaucher')
                        ->where('referenceNo',$invoice_detail[0]['invoice'])
                        ->where('Vtype','CV')
                        ->get()->result();
        
        $data = array(
            'title'           => display('invoice_edit'),
            'invoice_type'     => $invoice_detail[0]['invoice_type'],
            'dbinv_id'        => $invoice_detail[0]['dbinv_id'],
            'invoice_id'      => $invoice_detail[0]['inv_id'],
            'customer_id'     => $invoice_detail[0]['customer_id'],
            'customer_name'   => $invoice_detail[0]['customer_name'],
            'salesman_id'     => $invoice_detail[0]['salesman_id'],
            'broker_id'     => $invoice_detail[0]['broker_id'],
            'supplier_id'     => $invoice_detail[0]['supplier_id'],
            'document_date'     => $invoice_detail[0]['document_date'],
            'policy_type'     => $invoice_detail[0]['policy_type'],
            'debit_note_no'     => $invoice_detail[0]['debit_note_no'],
            // 'credit_note_no'     => $invoice_detail[0]['credit_note_no'],
            'commission_credit_note_no'     => $invoice_detail[0]['commission_credit_note_no'],
            'incentive_credit_note_no'     => $invoice_detail[0]['incentive_credit_note_no'],
            'policy_no'     => $invoice_detail[0]['policy_no'],
            'endorsement_no'     => $invoice_detail[0]['endorsement_no'],
            'policy_from'     => $invoice_detail[0]['policy_from'],
            'policy_to'     => $invoice_detail[0]['policy_to'],
            'sum_insured'     => $invoice_detail[0]['sum_insured'],
            'interest'     => $invoice_detail[0]['interest'],
            'narration'     => $invoice_detail[0]['narration'],
            'attachment'     => $invoice_detail[0]['attachment'],
            
            
            'date'            => $invoice_detail[0]['date'],
            'invoice_details' => $invoice_detail[0]['invoice_details'],
            'invoice'         => $invoice_detail[0]['invoice'],
            'total_amount'    => $invoice_detail[0]['total_amount'],
            'paid_amount'     => $invoice_detail[0]['paid_amount'],
            'due_amount'      => $invoice_detail[0]['due_amount'],
            'invoice_discount'=> $invoice_detail[0]['invoice_discount'],
            'total_discount'  => $invoice_detail[0]['total_discount'],
            'total_vat_amnt'  => $invoice_detail[0]['total_vat_amnt'],
            'unit'            => $invoice_detail[0]['unit'],
            'tax'             => $invoice_detail[0]['tax'],
            'taxes'           => $taxfield,
            'invoice_all_data'=> $invoice_detail,
            'taxvalu'         => $taxinfo,
            'discount_type'   => $currency_details[0]['discount_type'],
            'bank_id'         => $invoice_detail[0]['bank_id'],
            'multi_paytype'   => $multi_pay_data,
            'is_credit'       => $invoice_detail[0]['is_credit'],
        );
        // var_dump($invoice_detail);exit;
        $data['supplier'] = $this->invoice_model->supplier_list();
        $data['broker'] = $this->invoice_model->broker_list();
        $data['salesman'] = $this->invoice_model->salesman_list();
        $data['all_pmethod'] = $this->invoice_model->pmethod_dropdown_new();
        $data['all_pmethodwith_cr'] = $this->invoice_model->pmethod_dropdown();
        $data['module']     = "invoice";
        $vatortax              = $this->invoice_model->vat_tax_setting();
        if($vatortax->fixed_tax == 1){
            
            $data['page']       = "edit_invoice_form"; 
        }
        if($vatortax->dynamic_tax == 1){
            $data['page']          = "edit_invoice_form_dynamic"; 
        }
        echo modules::run('template/layout', $data);
    }

    public function cloudsubset_update_invoice(){
        $this->form_validation->set_rules('invoice_no', display('invoice_no') ,'required|max_length[20]');
        $this->form_validation->set_rules('customer_id', display('customer_name') ,'required|max_length[15]');
        $this->form_validation->set_rules('product_id',display('product'),'required|max_length[20]');
        // $this->form_validation->set_rules('multipaytype[]',display('payment_type'),'required');
        $this->form_validation->set_rules('total_premium_amount','Total Premium Amount','required|max_length[20]');
        
        $sup_price = $this->input->post('broker_commission_amount', true);
        $s_id = $this->input->post('supplier_id', true);
        $sal_price = $this->input->post('salesman_commission_amount', true);
        $sa_id = $this->input->post('salesman_id', true);
        
        $multipaytype = $this->input->post('multipaytype',TRUE);
        $finyear = $this->input->post('finyear',true);
        if($finyear<=0){
            $data['status'] = false;
            $data['exception'] = 'Please Create Financial Year First';
        }else {
       
            if ($this->form_validation->run() === true) { 
                $invoice_id = $this->invoice_model->update_invoice();
                if(!empty($invoice_id)){
                    $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                    if ($setting_data[0]['is_autoapprove_v'] == 1) {
                        
                        $new = $this->autoapprove($invoice_id);
                    }
                    $data['status'] = true;
                    $data['invoice_id'] = $invoice_id;
                    $data['message'] = display('update_successfully');
                    $mailsetting = $this->db->select('*')->from('email_config')->get()->result_array();
                    if($mailsetting[0]['isinvoice']==1){
                        $mail = $this->invoice_pdf_generate($invoice_id);
                        if($mail == 0){
                            $data['exception'] = $this->session->set_userdata(array('error_message' => display('please_config_your_mail_setting')));
                        }
                    }
                    $data['details'] = $this->load->view('invoice/invoice_html', $data, true);
                }else{
                $data['status'] = false;
                $data['exception'] = 'Please Try Again';
                }
            
            }else{
                $data['status'] = false;
            $data['exception'] = validation_errors();  
            }
        }
        echo json_encode($data);
    }

    public function invoice_pdf_generate($invoice_id = null) {
        $id = $invoice_id; 
        $invoice_detail = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon = 0;
        $subTotal_discount = 0;
        $subTotal_ammount = 0;
        $descript = 0;
        $isserial = 0;
        $isunit = 0;
        $is_discount = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
               
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['discount_per'])){
                    $is_discount = $is_discount+1;
                    
                }

                if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
   
            }
        }

        $currency_details = $this->invoice_model->retrieve_setting_editdata();
        $company_info = $this->invoice_model->retrieve_company();
        $totalbal = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword = $this->numbertowords->convert_number($totalbal);
        $user_id = $invoice_detail[0]['sales_by'];
        
        $name    = $invoice_detail[0]['customer_name'];
        $email   = $invoice_detail[0]['customer_email'];
        $data = array(
        'title'             => display('invoice_details'),
        'invoice_id'        => $invoice_detail[0]['invoice_id'],
        'customer_info'     => $invoice_detail,
        'invoice_no'        => $invoice_detail[0]['invoice'],
        'customer_name'     => $invoice_detail[0]['customer_name'],
        'customer_address'  => $invoice_detail[0]['customer_address'],
        'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
        'customer_telephone'=> $invoice_detail[0]['customer_telephone'],
        'customer_email'    => $invoice_detail[0]['customer_email'],
        'final_date'        => $invoice_detail[0]['final_date'],
        'invoice_details'   => $invoice_detail[0]['invoice_details'],
        'total_amount'      => number_format($invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'], 2, '.', ','),
        'subTotal_quantity' => $subTotal_quantity,
        'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_vat'         => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),
        'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'  => $invoice_detail,
        'company_info'      => $company_info,
        'currency'          => $currency_details[0]['currency'],
        'position'          => $currency_details[0]['currency_position'],
        'discount_type'     => $currency_details[0]['discount_type'],
        'currency_details'  => $currency_details,
        'am_inword'         => $amount_inword,
        'is_discount'       => $is_discount,
        
        'tax_regno'         => $txregname,
        'is_desc'           => $descript,
        'is_serial'         => $isserial,
        'is_unit'           => $isunit,
        );

        $this->load->library('pdfgenerator');
        $html = $this->load->view('invoice/invoice_download', $data, true);
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents('assets/data/pdf/invoice/' . $id . '.pdf', $output);
        $file_path = getcwd() . '/assets/data/pdf/invoice/' . $id . '.pdf';
        $send_email = '';
        if (!empty($email)) {
            $send_email = $this->setmail($email, $file_path, $id, $name);
            
            if($send_email){
           return 1;
            }else{
               return 0;
               
            }
           
        }
      return 0; 
       
    }
    
    public function setmail($email, $file_path, $id = null, $name = null) {
        $setting_detail = $this->db->select('*')->from('email_config')->get()->row();
        $subject = 'Product Purchase Information';
        $message = strtoupper($name) . '-' . $id;

        $config = Array(
            'protocol'  => $setting_detail->protocol,
            'smtp_host' => $setting_detail->smtp_host,
            'smtp_port' => $setting_detail->smtp_port,
            'smtp_user' => $setting_detail->smtp_user,
            'smtp_pass' => $setting_detail->smtp_pass,
            'mailtype'  => 'html', 
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        );
       
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->from($setting_detail->smtp_user);
        $this->email->to($email);

        $config = Array(
        'protocol'  => $setting_detail->protocol,
        'smtp_host' => $setting_detail->smtp_host,
        'smtp_port' => $setting_detail->smtp_port,
        'smtp_user' => $setting_detail->smtp_user,
        'smtp_pass' => $setting_detail->smtp_pass,
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'wordwrap'  => TRUE
        );
        
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->from($setting_detail->smtp_user);
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->attach($file_path);
        $check_email = $this->test_input($email);
        if (filter_var($check_email, FILTER_VALIDATE_EMAIL)) {
            if ($this->email->send()) {
                return true;
            } else {
                $this->session->set_flashdata(array('exception' => display('please_configure_your_mail.')));
                return false;
            }
        } else {
           
            return false;
        }
    }
    
    //Email testing for email
    public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    function cloudsubset_pos_invoice() {
        $taxfield = $this->db->select('tax_name,default_value')
                ->from('tax_settings')
                ->get()
                ->result_array();
                $tablecolumn   = $this->db->list_fields('tax_collection');
                $num_column    = count($tablecolumn)-4;
        $walking_customer      = $this->invoice_model->pos_customer_setup();
        $data['customer_name'] = $walking_customer[0]['customer_name'];
        $data['customer_id']   = $walking_customer[0]['customer_id'];
        $data['invoice_no']    = $this->number_generator();
        $data['title']         = display('pos_invoice');
        $data['taxes']         = $this->invoice_model->tax_fileds();
        $data['taxnumber']     = $num_column;
        $data['module']        = "invoice";
        $data['page']          = "add_pos_invoice_form"; 
        echo modules::run('template/layout', $data);
    }
    
    public function cloudsubset_gui_pos(){
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
            $tablecolumn       = $this->db->list_fields('tax_collection');
            $num_column        = count($tablecolumn)-4;
        $data['title']         = display('gui_pos');
        $saveid                = $this->session->userdata('id');
        $walking_customer      = $this->invoice_model->walking_customer();
        $data['customer_id']   = $walking_customer[0]['customer_id'];
        $data['customer_name'] = $walking_customer[0]['customer_name'];
        $data['categorylist']  = $this->invoice_model->category_list();
        $customer_details      = $this->invoice_model->pos_customer_setup();
        $data['customerlist']  = $this->invoice_model->customer_dropdown();
        $data['customer_name'] = $customer_details[0]['customer_name'];
        $data['customer_id']   = $customer_details[0]['customer_id'];
        $data['itemlist']      = $this->invoice_model->allproduct();
        $data['product_list']  = $this->invoice_model->product_list();
        $data['taxes']         = $taxfield;
        $data['taxnumber']     = $num_column;
        $data['invoice_no']    = $this->number_generator();
        $data['todays_invoice']= $this->invoice_model->todays_invoice();
        $data['all_pmethod']   = $this->invoice_model->pmethod_dropdown();
        $data['module']        = "invoice";
        $vatortax              = $this->invoice_model->vat_tax_setting();
        if($vatortax->fixed_tax == 1){
            $data['page']      = "gui_pos_invoice"; 
            $data['tax_type']  = "fixed"; 

        }
        if($vatortax->dynamic_tax == 1){
            $data['page']      = "gui_pos_invoice_dynamic"; 
            $data['tax_type']  = "dynamic";
        }
        echo modules::run('template/layout', $data); 
    }
    
    public function getitemlist(){
        $catid       = $this->input->post('category_id',TRUE);
        $category_id = (!empty($catid)?$catid:'');
        $getproduct  = $this->invoice_model->searchprod($category_id);
        if(!empty($getproduct)){
        $data['itemlist']=$getproduct;
        $this->load->view('invoice/getproductlist', $data);  
        }else{
        $title['title'] = 'Product Not found';
        $this->load->view('invoice/productnot_found', $title);
        }
    }
    
    public function getitemlist_byname(){
            $product_name     = $this->input->post('product_name',TRUE);
            $getproduct       = $this->invoice_model->searchprod_byname($product_name);
            if(!empty($getproduct)){
            $data['itemlist'] = $getproduct;
            $this->load->view('invoice/getproductlist', $data);  
            }else{
            $title['title']   = 'Product Not found';
            $this->load->view('invoice/productnot_found', $title);
            }
     }
    
    public function getitemlist_byproductname(){
            $prod       = $this->input->post('product_name',TRUE);
            $catid      = $this->input->post('category_id',TRUE);
            $getproduct = $this->invoice_model->searchprod_byname($catid,$prod);
            if(!empty($getproduct)){
            $data['itemlist']=$getproduct;
            $this->load->view('invoice/getproductlist', $data);  
            }
            else{
                $title['title'] = 'Product Not found';
                $this->load->view('invoice/productnot_found', $title);
                }
    }

    public function gui_pos_invoice() {
        $product_id = $this->input->post('product_id',TRUE);
        $pro_id = $this->input->post('product_id',TRUE);

        $product_details = $this->invoice_model->pos_invoice_setup($product_id);
        $taxfield       = $this->db->select('tax_name,default_value')
                ->from('tax_settings')
                ->get()
                ->result_array();
           $prinfo = $this->db->select('*')->from('product_information')->where('product_id',$product_id)->get()->result_array();

        $tr = " ";
        if (!empty($product_details)) {
            $product_id = $this->generator(5);
            $serialdata =explode(',', $product_details->serial_no);
            if($product_details->total_product > 0){
              $qty = 1;
            }else{
                $qty = 0;
            }

            $this->db->select('SUM(quantity) as purchase_qty,batch_id,product_id');
            $this->db->from('product_purchase_details');
            $this->db->where('product_id', $product_details->product_id);
            $this->db->group_by('batch_id');
            $pur_product_batch = $this->db->get()->result();


        $html = "";
        if (empty($pur_product_batch)) {
            $html .="No Serial Found !";
        }else{
            // Select option created for product
            $html .="<select name=\"serial_no[]\"   class=\"serial_no_1 form-control\" required onchange=\"invoice_product_batch('" . $product_details->product_id . "')\" id=\"serial_no_".$product_details->product_id."\">";
                $html .= "<option value=''>".display('select_one')."</option>";
                foreach ($pur_product_batch as $p_batch) {


                    $sellt_prod_batch = $this->db->select('SUM(quantity) as sale_qty,batch_id, product_id')->from('invoice_details')->where('product_id', $p_batch->product_id)->where('batch_id', $p_batch->batch_id)->get()->row();
                    $pur_prod = (empty($sellt_prod_batch->sale_qty)?0:$sellt_prod_batch->sale_qty);
                    $available_prod = $p_batch->purchase_qty - $pur_prod;
                    if ($available_prod > 0) {
                        $html .="<option value=".$p_batch->batch_id.">".$p_batch->batch_id."</option>";
                    }
                }   
            $html .="</select>";
        }
            
            $tr .= "<tr id=\"row_" . $product_details->product_id . "\">
                        <td class=\"\" style=\"width:220px\">
                            
                            <input type=\"text\" name=\"product_name\" onkeypress=\"invoice_productList('" . $product_details->product_id . "');\" class=\"form-control productSelection \" value='" . $product_details->product_name . "- (" . $product_details->product_model . ")" . "' placeholder='" . display('product_name') . "' required=\"\"  tabindex=\"\" readonly>

                            <input type=\"hidden\" class=\"form-control autocomplete_hidden_value product_id_" . $product_details->product_id . "\" name=\"product_id[]\" id=\"SchoolHiddenId_" . $product_details->product_id . "\" value = \"$product_details->product_id\"/>
                        </td>
                        <td>".$html."</td>
                        <td>
                            <input type=\"text\" name=\"available_quantity[]\" class=\"form-control text-right available_quantity_" . $product_details->product_id . "\" value='' readonly=\"\" id=\"available_quantity_" . $product_details->product_id . "\"/>
                        </td>
                        <td>
                            <input type=\"text\" name=\"product_quantity[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" class=\"total_qntt_" . $product_details->product_id . " form-control text-right\" id=\"total_qntt_" . $product_details->product_id . "\" placeholder=\"0.00\" min=\"0\" value='" . $qty . "' required=\"required\"/>
                        </td>
                        <td style=\"width:85px\">
                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" value='" . $product_details->price . "' id=\"price_item_" . $product_details->product_id . "\" class=\"price_item1 form-control text-right\" required placeholder=\"0.00\" min=\"0\"/>
                        </td>

                        <td class=\"\">
                            <input type=\"text\" name=\"discount[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"discount_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                          
                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"discountvalue[]\"  id=\"discount_value_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\" readonly/>
                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"vatpercent[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"vat_percent_" . $product_details->product_id . "\" value='" . $product_details->product_vat . "' class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"vatvalue[]\"  id=\"vat_value_" . $product_details->product_id . "\" class=\"form-control text-right total_vatamnt\" placeholder=\"0.00\" min=\"0\" readonly/>
                        </td>
                        <td class=\"text-right\" style=\"width:100px\">
                            <input class=\"total_price form-control text-right\" type=\"text\" name=\"total_price[]\" id=\"total_price_" . $product_details->product_id . "\" value='" . $product_details->price . "' tabindex=\"-1\" readonly=\"readonly\"/>
                        </td>

                        <td>";
                     
                            $sl=0;

                           $tr.="<input type=\"hidden\" id=\"total_discount_" . $product_details->product_id . "\" />
                            <input type=\"hidden\" id=\"all_discount_" . $product_details->product_id . "\" class=\"total_discount dppr\"/>
                            <a style=\"text-align: right;\" class=\"btn btn-danger btn-xs\" href=\"#\"  onclick=\"deleteRow(this,'".$product_details->product_id."')\">" . '<i class="fa fa-close"></i>' . "</a>
                             <a style=\"text-align: right;\" class=\"btn btn-success btn-xs\" href=\"#\"  onclick=\"detailsmodal('".$product_details->product_name."','".$product_details->total_product."','".$product_details->product_model."','".$product_details->unit."','".$product_details->price."','".$product_details->image."')\">" . '<i class="fa fa-eye"></i>' . "</a>
                        </td>
                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }
    
    public function gui_pos_invoice_dynamic() {
        $product_id = $this->input->post('product_id',TRUE);
        $pro_id = $this->input->post('product_id',TRUE);

        $product_details = $this->invoice_model->pos_invoice_setup($product_id);
        $taxfield       = $this->db->select('tax_name,default_value')
                ->from('tax_settings')
                ->get()
                ->result_array();
           $prinfo = $this->db->select('*')->from('product_information')->where('product_id',$product_id)->get()->result_array();

        $tr = " ";
        if (!empty($product_details)) {
            $product_id = $this->generator(5);
            $serialdata =explode(',', $product_details->serial_no);
            if($product_details->total_product > 0){
              $qty = 1;
            }else{
                $qty = 0;
            }

            $this->db->select('SUM(quantity) as purchase_qty,batch_id,product_id');
            $this->db->from('product_purchase_details');
            $this->db->where('product_id', $product_details->product_id);
            $this->db->group_by('batch_id');
            $pur_product_batch = $this->db->get()->result();


        $html = "";
        if (empty($pur_product_batch)) {
            $html .="No Serial Found !";
        }else{
            // Select option created for product
            $html .="<select name=\"serial_no[]\"   class=\"serial_no_1 form-control\" required onchange=\"invoice_product_batch('" . $product_details->product_id . "')\" id=\"serial_no_".$product_details->product_id."\">";
                $html .= "<option value=''>".display('select_one')."</option>";
                foreach ($pur_product_batch as $p_batch) {


                    $sellt_prod_batch = $this->db->select('SUM(quantity) as sale_qty,batch_id, product_id')->from('invoice_details')->where('product_id', $p_batch->product_id)->where('batch_id', $p_batch->batch_id)->get()->row();
                    $pur_prod = (empty($sellt_prod_batch->sale_qty)?0:$sellt_prod_batch->sale_qty);
                    $available_prod = $p_batch->purchase_qty - $pur_prod;
                    if ($available_prod > 0) {
                        # code...
                        $html .="<option value=".$p_batch->batch_id.">".$p_batch->batch_id."</option>";
                    }
                }   
            $html .="</select>";
        }
            
            $tr .= "<tr id=\"row_" . $product_details->product_id . "\">
                        <td class=\"\" style=\"width:220px\">
                            
                            <input type=\"text\" name=\"product_name\" onkeypress=\"invoice_productList('" . $product_details->product_id . "');\" class=\"form-control productSelection \" value='" . $product_details->product_name . "- (" . $product_details->product_model . ")" . "' placeholder='" . display('product_name') . "' required=\"\"  tabindex=\"\" readonly>

                            <input type=\"hidden\" class=\"form-control autocomplete_hidden_value product_id_" . $product_details->product_id . "\" name=\"product_id[]\" id=\"SchoolHiddenId_" . $product_details->product_id . "\" value = \"$product_details->product_id\"/>
                        </td>
                        <td>".$html."</td>
                        <td>
                            <input type=\"text\" name=\"available_quantity[]\" class=\"form-control text-right available_quantity_" . $product_details->product_id . "\" value='' readonly=\"\" id=\"available_quantity_" . $product_details->product_id . "\"/>
                        </td>
                        <td>
                            <input type=\"text\" name=\"product_quantity[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" class=\"total_qntt_" . $product_details->product_id . " form-control text-right\" id=\"total_qntt_" . $product_details->product_id . "\" placeholder=\"0.00\" min=\"0\" value='" . $qty . "' required=\"required\"/>
                        </td>
                        <td style=\"width:85px\">
                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" value='" . $product_details->price . "' id=\"price_item_" . $product_details->product_id . "\" class=\"price_item1 form-control text-right\" required placeholder=\"0.00\" min=\"0\"/>
                        </td>

                        <td class=\"\">
                            <input type=\"text\" name=\"discount[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"discount_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                          
                        </td>
                        <td class=\"\">
                            <input type=\"text\" name=\"discountvalue[]\"  id=\"discount_value_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\" readonly/>
                        </td>
                        
                        <td class=\"text-right\" style=\"width:100px\">
                            <input class=\"total_price form-control text-right\" type=\"text\" name=\"total_price[]\" id=\"total_price_" . $product_details->product_id . "\" value='" . $product_details->price . "' tabindex=\"-1\" readonly=\"readonly\"/>
                        </td>

                        <td>";
                     
                            $sl=0;
                        foreach ($taxfield as $taxes) {
                            $txs = 'tax'.$sl;
                           $tr .= "<input type=\"hidden\" id=\"total_tax".$sl."_" . $product_details->product_id . "\" class=\"total_tax".$sl."_" . $product_details->product_id . "\" value='" . $prinfo[0][$txs] . "'/>
                            <input type=\"hidden\" id=\"all_tax".$sl."_" . $product_details->product_id . "\" class=\" total_tax".$sl."\" value='" . $prinfo[0][$txs]*$product_details->price . "' name=\"tax[]\"/>";  
                       $sl++; }

                           $tr.="<input type=\"hidden\" id=\"total_discount_" . $product_details->product_id . "\" />
                            <input type=\"hidden\" id=\"all_discount_" . $product_details->product_id . "\" class=\"total_discount dppr\"/>
                            <a style=\"text-align: right;\" class=\"btn btn-danger btn-xs\" href=\"#\"  onclick=\"deleteRow(this,'".$product_details->product_id."')\">" . '<i class="fa fa-close"></i>' . "</a>
                             <a style=\"text-align: right;\" class=\"btn btn-success btn-xs\" href=\"#\"  onclick=\"detailsmodal('".$product_details->product_name."','".$product_details->total_product."','".$product_details->product_model."','".$product_details->unit."','".$product_details->price."','".$product_details->image."')\">" . '<i class="fa fa-eye"></i>' . "</a>
                        </td>
                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }
    
    //Insert pos invoice
    public function insert_pos_invoice() {
        $product_id      = $this->input->post('product_id',TRUE);
        $product_details = $this->invoice_model->pos_invoice_setup($product_id);
        $taxfield = $this->db->select('tax_name,default_value')
                ->from('tax_settings')
                ->get()
                ->result_array();
           $prinfo = $this->db->select('*')->from('product_information')->where('product_id',$product_id)->get()->result_array();
        $tr = " ";
        if (!empty($product_details)) {
            $product_id = $this->generator(5);
            $serialdata =explode(',', $product_details->serial_no);
            if($product_details->total_product > 0){
              $qty = 1;
            }else{
                $qty = 1;
            }

        $html = "";
        if (empty($serialdata)) {
            $html .="No Serial Found !";
        }else{
            // Select option created for product
            $html .="<select name=\"serial_no[]\"   class=\"serial_no_1 form-control\" id=\"serial_no_" . $product_details->product_id . "\">";
                $html .= "<option value=''>".display('select_one')."</option>";
                foreach ($serialdata as $serial) {
                    $html .="<option value=".$serial.">".$serial."</option>";
                }   
            $html .="</select>";
        }
            
            $tr .= "<tr id=\"row_" . $product_details->product_id . "\">
                        <td class=\"\" style=\"width:220px\">
                            
                            <input type=\"text\" name=\"product_name\" onkeypress=\"invoice_productList('" . $product_details->product_id . "');\" class=\"form-control productSelection \" value='" . $product_details->product_name . "- (" . $product_details->product_model . ")" . "' placeholder='" . display('product_name') . "' required=\"\" id=\"product_name_" . $product_details->product_id . "\" tabindex=\"\" readonly>

                            <input type=\"hidden\" class=\"form-control autocomplete_hidden_value product_id_" . $product_details->product_id . "\" name=\"product_id[]\" id=\"SchoolHiddenId_" . $product_details->product_id . "\" value = \"$product_details->product_id\"/>
                            
                        </td>
                         <td>
                             <input type=\"text\" name=\"desc[]\" class=\"form-control text-right \"  />
                                        </td>
                                        <td>".$html."</td>
                        <td>
                            <input type=\"text\" name=\"available_quantity[]\" class=\"form-control text-right available_quantity_" . $product_details->product_id . "\" value='" . $product_details->total_product . "' readonly=\"\" id=\"available_quantity_" . $product_details->product_id . "\"/>
                        </td>

                        <td>
                            <input class=\"form-control text-right unit_'" . $product_details->product_id . "' valid\" value=\"$product_details->unit\" readonly=\"\" aria-invalid=\"false\" type=\"text\">
                        </td>
                    
                        <td>
                            <input type=\"text\" name=\"product_quantity[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" class=\"total_qntt_" . $product_details->product_id . " form-control text-right\" id=\"total_qntt_" . $product_details->product_id . "\" placeholder=\"0.00\" min=\"0\" value='" . $qty . "'/>
                        </td>

                        <td style=\"width:85px\">
                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" value='" . $product_details->price . "' id=\"price_item_" . $product_details->product_id . "\" class=\"price_item1 form-control text-right\" required placeholder=\"0.00\" min=\"0\"/>
                        </td>

                        <td class=\"\">
                            <input type=\"text\" name=\"discount[]\" onkeyup=\"quantity_calculate('" . $product_details->product_id . "');\" onchange=\"quantity_calculate('" . $product_details->product_id . "');\" id=\"discount_" . $product_details->product_id . "\" class=\"form-control text-right\" placeholder=\"0.00\" min=\"0\"/>

                           
                        </td>

                        <td class=\"text-right\" style=\"width:100px\">
                            <input class=\"total_price form-control text-right\" type=\"text\" name=\"total_price[]\" id=\"total_price_" . $product_details->product_id . "\" value='" . $product_details->price . "' tabindex=\"-1\" readonly=\"readonly\"/>
                        </td>

                        <td>";
                        $sl=0;
                        foreach ($taxfield as $taxes) {
                            $txs = 'tax'.$sl;
                           $tr .= "<input type=\"hidden\" id=\"total_tax".$sl."_" . $product_details->product_id . "\" class=\"total_tax".$sl."_" . $product_details->product_id . "\" value='" . $prinfo[0][$txs] . "'/>
                            <input type=\"hidden\" id=\"all_tax".$sl."_" . $product_details->product_id . "\" class=\" total_tax".$sl."\" value='" . $prinfo[0][$txs]*$product_details->price . "' name=\"tax[]\"/>";  
                       $sl++; }
                        
                             $tr .= "<input type=\"hidden\" id=\"total_discount_" . $product_details->product_id . "\" />
                            <input type=\"hidden\" id=\"all_discount_" . $product_details->product_id . "\" class=\"total_discount dppr\"/>
                            <button  class=\"btn btn-danger btn-xs text-center\" type=\"button\"  onclick=\"deleteRow(this)\">" . '<i class="fa fa-close"></i>' . "</button>
                        </td>
                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }

    public function invoice_inserted_data_manual(){
        $data['title']      = display('invoice_print');
        $invoice_id         = $this->input->post('invoice_id',TRUE);
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                  if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
   
            }
        }


        $payment_method_list = $this->invoice_model->invoice_method_wise_balance($invoice_id);
        $terms_list = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result(); 
        $totalbal      = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword = $totalbal;
        $user_id       = $invoice_detail[0]['sales_by'];
        $users         = $this->invoice_model->user_invoice_data($user_id);
        $data = array(
        'title'             => display('invoice_details'),
        'invoice_id'        => $invoice_detail[0]['invoice_id'],
        'invoice_no'        => $invoice_detail[0]['invoice'],
        'customer_name'     => $invoice_detail[0]['customer_name'],
        'customer_address'  => $invoice_detail[0]['customer_address'],
        'customer_telephone'=> $invoice_detail[0]['customer_telephone'],
        'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
        'customer_email'    => $invoice_detail[0]['customer_email'],
        'final_date'        => $invoice_detail[0]['final_date'],
        'invoice_details'   => $invoice_detail[0]['invoice_details'],
        'total_amount'      => number_format($invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'], 2, '.', ','),
        'grand_total'       => $invoice_detail[0]['total_amount'],
        'subTotal_quantity' => $subTotal_quantity,
        'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'  => $invoice_detail,
        'am_inword'         => $amount_inword,
        'is_discount'       => $invoice_detail[0]['total_discount']-$invoice_detail[0]['invoice_discount'],
        'users_name'        => $users->first_name.' '.$users->last_name,
        'tax_regno'         => $txregname,
        'is_desc'           => $descript,
        'is_serial'         => $isserial,
        'is_unit'           => $isunit,
        'all_discount'         => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'p_method_list'        => $payment_method_list,
        'terms_list'           => $terms_list,
        'total_vat'            => number_format($invoice_detail[0]['total_vat_amnt'] + $invoice_detail[0]['total_tax'], 2, '.', ','),
        );
        $data['module']     = "invoice";
        $data['page']       = "invoice_html_manual"; 
        echo modules::run('template/layout', $data);
    }
    
    /*invoice no generator*/
    public function number_generator() {
        $this->db->select_max('invoice', 'invoice_no');
        $query      = $this->db->get('invoice');
        $result     = $query->result_array();
        $invoice_no = $result[0]['invoice_no'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1000;
        }
        return $invoice_no;
    }

    public function cloudsubset_customer_autocomplete(){
        $customer_id    = $this->input->post('customer_id',TRUE);
        $customer_info  = $this->invoice_model->customer_search($customer_id);

        $list[''] = '';
        foreach ($customer_info as $value) {
            $json_customer[] = array('label'=>$value['customer_name'],'value'=>$value['customer_id']);
        } 
        echo json_encode($json_customer);
    }

    /*product autocomple search*/
    public function cloudsubset_autocomplete_product(){
        $product_name   = $this->input->post('product_name',TRUE);
        $product_info   = $this->invoice_model->autocompletproductdata($product_name);
       if(!empty($product_info)){
        $list[''] = '';
        foreach ($product_info as $value) {
            $json_product[] = array('label'=>$value['product_name'],'value'=>$value['product_id']);
        } 
    }else{
        $json_product[] = 'No Product Found';
        }
        echo json_encode($json_product);
    
    }
     
    /*after selecting product retrieve product info*/
    public function get_product_info() {
        $product_id   = $this->input->post('product_id',TRUE);
        $product_info = $this->invoice_model->get_product_info($product_id);
        echo json_encode($product_info);
    }
    
    public function retrieve_product_data_inv() {
        $product_id   = $this->input->post('product_id',TRUE);
        $product_info = $this->invoice_model->get_total_product_invoic($product_id);
        echo json_encode($product_info);
    }
    
    public function cloudsubset_batchwise_productprice() {
        $product_id   = $this->input->post('prod_id',TRUE);
        $batch_no   = $this->input->post('batch_no',TRUE);

        $this->db->select('sum(quantity) as purchase_qty,batch_id,product_id');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('batch_id', $batch_no);
        $pur_product_batch = $this->db->get()->row();

        $sellt_prod_batch = $this->db->select('sum(quantity) as sale_qty,batch_id, product_id')
        ->from('invoice_details')->where('product_id', $product_id)
        ->where('batch_id', $batch_no)
        ->get()
        ->row();
        

        $batch_wise_stock =  (!empty($pur_product_batch->purchase_qty)?$pur_product_batch->purchase_qty:0)-(!empty($sellt_prod_batch->sale_qty)?$sellt_prod_batch->sale_qty:0);
        echo sprintf('%0.2f',$batch_wise_stock);
        
    }
    
    /*after select customer retrieve customer previous balance*/
    public function previous() {
        $customer_id = $this->input->post('customer_id',TRUE);
        $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
         $this->db->from('customer_information a');
         $this->db->join('acc_coa b','a.customer_id = b.customer_id','left');
         $this->db->where('a.customer_id',$customer_id);
        $result = $this->db->get()->result_array();
       $balance = $result[0]['balance'];   
       $b = (!empty($balance)?$balance:0);                            
        if ($b){
           echo  $b;
        } else {
           echo  $b;
        }
    }
    
    public function instant_customer(){
     
        $data = array(
            'customer_name'    => $this->input->post('customer_name',TRUE),
            'customer_address' => $this->input->post('address',TRUE),
            'customer_mobile'  => $this->input->post('mobile',TRUE),
            'customer_telephone'=> $this->input->post('telephone',TRUE),
            'customer_email'   => $this->input->post('email',TRUE),
            'status'           => 1
        );

        $result = $this->db->insert('customer_information',$data);
        if ($result) {

        $customer_id = $this->db->insert_id();
       
        //Customer  basic information adding.
        $coa = $this->customer_model->headcode();
            if($coa->HeadCode!=NULL){
                $headcode=$coa->HeadCode+1;
            }else{
                $headcode="102030001";
            }
        $c_acc      = $customer_id.'-'.$this->input->post('customer_name',TRUE);
        $createby   = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');

        $customer_coa = [
            'HeadCode'         => $headcode,
            'HeadName'         => $c_acc,
            'PHeadName'        => 'Customer Receivable',
            'HeadLevel'        => '4',
            'IsActive'         => '1',
            'IsTransaction'    => '1',
            'IsGL'             => '0',
            'HeadType'         => 'A',
            'IsBudget'         => '0',
            'IsDepreciation'   => '0',
            'customer_id'      => $customer_id,
            'DepreciationRate' => '0',
            'CreateBy'         => $createby,
            'CreateDate'       => $createdate,
        ];
        //Previous balance adding -> Sending to customer model to adjust the data.
        // $this->db->insert('acc_coa',$customer_coa);

        $sub_acc = [
            'subTypeId'   => 3,
            'name'        => $data['customer_name'],
            'referenceNo' => $customer_id,
            'status'      => 1,
            'created_date'=> date("Y-m-d"),
            
        ];
        $this->db->insert('acc_subcode',$sub_acc);
           
            
          
            $data['status']        = true;
            $data['message']       = display('save_successfully');
            $data['customer_id']   = $customer_id;
            $data['customer_name'] = $data['customer_name'];
        } else {
            $data['status'] = false;
            $data['exception'] = display('please_try_again');
        }
        echo json_encode($data);
    }
    
    public function cloudsubset_invoice_details_directprint($invoice_id = null){
        $invoice_detail     = $this->invoice_model->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
                ->from('tax_settings')
                ->where('is_show',1)
                ->get()
                ->result_array();
        $txregname ='';
        foreach($taxfield as $txrgname){
        $regname = $txrgname['tax_name'].' Reg No  - '.$txrgname['reg_no'].', ';
        $txregname .= $regname;
        }       
        $subTotal_quantity = 0;
        $subTotal_cartoon  = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;
        $descript          = 0;
        $isserial          = 0;
        $isunit            = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $invoice_detail[$k]['date'];
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                  if(!empty($invoice_detail[$k]['description'])){
                    $descript = $descript+1;
                    
                }
                 if(!empty($invoice_detail[$k]['serial_no'])){
                    $isserial = $isserial+1;
                    
                }
                 if(!empty($invoice_detail[$k]['unit'])){
                    $isunit = $isunit+1;
                    
                }
   
            }
        }


        $totalbal = $invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'];
        $amount_inword     = $totalbal;
        $user_id           = $invoice_detail[0]['sales_by'];
        $users             = $this->invoice_model->user_invoice_data($user_id);
        $company_info      = $this->invoice_model->retrieve_company();
        $currency_details  = $this->invoice_model->retrieve_setting_editdata();
        $data = array(
        'title'             => display('invoice_details'),
        'invoice_id'        => $invoice_detail[0]['invoice_id'],
        'invoice_no'        => $invoice_detail[0]['invoice'],
        'customer_name'     => $invoice_detail[0]['customer_name'],
        'customer_address'  => $invoice_detail[0]['customer_address'],
        'customer_telephone'=> $invoice_detail[0]['customer_telephone'],
        'customer_mobile'   => $invoice_detail[0]['customer_mobile'],
        'customer_email'    => $invoice_detail[0]['customer_email'],
        'final_date'        => $invoice_detail[0]['final_date'],
        'invoice_details'   => $invoice_detail[0]['invoice_details'],
        'total_amount'      => number_format($invoice_detail[0]['total_amount']+$invoice_detail[0]['prevous_due'], 2, '.', ','),
        'subTotal_quantity' => $subTotal_quantity,
        'total_discount'    => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
        'total_tax'         => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
        'subTotal_ammount'  => number_format($subTotal_ammount, 2, '.', ','),
        'paid_amount'       => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
        'due_amount'        => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
        'previous'          => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
        'shipping_cost'     => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
        'invoice_all_data'  => $invoice_detail,
        'am_inword'         => $amount_inword,
        'is_discount'       => $invoice_detail[0]['total_discount']-$invoice_detail[0]['invoice_discount'],
        'users_name'        => $users->first_name.' '.$users->last_name,
        'tax_regno'         => $txregname,
        'is_desc'           => $descript,
        'is_serial'         => $isserial,
        'is_unit'           => $isunit,
        'discount_type'     => $currency_details[0]['discount_type'],
        'company_info'      => $company_info,
        'logo'              => $currency_details[0]['invoice_logo'],
        'position'          => $currency_details[0]['currency_position'],
        'currency'          => $currency_details[0]['currency'],
        );
       return $data;
    }
    
    public function generator($lenth) {
        $number=array("A","B","C","D","E","F","G","H","I","J","K","L","N","M","O","P","Q","R","S","U","V","T","W","X","Y","Z","1","2","3","4","5","6","7","8","9","0");
    
        for($i=0; $i<$lenth; $i++)
        {
            $rand_value=rand(0,34);
            $rand_number=$number["$rand_value"];
        
            if(empty($con))
            { 
            $con=$rand_number;
            }
            else
            {
            $con="$con"."$rand_number";}
        }
        return $con;
    }
    
    /*invoice no generator*/
    public function number_generator_ajax() {
        $this->db->select_max('invoice', 'invoice_no');
        $query      = $this->db->get('invoice');
        $result     = $query->result_array();
        $invoice_no = $result[0]['invoice_no'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1000;
        }
        echo  $invoice_no;
    }

    // category part
    function cloudsubset_terms_list() {
        $data['title']      = display('terms_list');
        $data['module']     = "invoice";
        $data['page']       = "terms_list"; 
        $data["allterms_list"] = $this->invoice_model->allterms_list();
        echo modules::run('template/layout', $data);
    }
    
    public function cloudsubset_terms_form($id = null) {
        $data['title'] = display('terms_add');
        #-------------------------------#
        $this->form_validation->set_rules('term_condi',display('term_condi'),'required');
        $this->form_validation->set_rules('status', display('status') ,'max_length[2]');
        #-------------------------------#
        $data['single_terms'] = (object)$postData = [
            'id'          =>$id,
            'description' => $this->input->post('term_condi',true),
            'status'      => $this->input->post('status',true),
        ]; 
        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->invoice_model->create_terms($postData)) {
                    #set success message
                   $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                 $this->session->set_flashdata('exception', display('please_try_again'));
                }
                
                redirect("terms_list");
           
            } else {
                if ($this->invoice_model->update_terms($postData)) {
                   $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                  $this->session->set_flashdata('exception', display('please_try_again'));
                } 
                redirect("terms_list");
            
            }
            } else { 
                if(!empty($id)){
                $data['title']    = display('terms_update');
                $data['single_terms'] = $this->invoice_model->single_terms_data($id);  
                }
                
                $data['module']   = "invoice";  
                $data['page']     = "terms_form";
                echo Modules::run('template/layout', $data); 
           
            } 
    }
    
    public function cloudsubset_terms($id = null) {
        if ($this->invoice_model->delete_terms($id)) {
      $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
       $this->session->set_flashdata('exception', display('please_try_again'));
        }

        redirect("terms_list");
    }
    
    public function upload_invoice() {
        // var_dump($data['supplier']);exit;
        if (isset($_POST['upload_invoice_submit']) && isset($_POST['supplier_id'])) {
            $data['invoice_no']    = $this->number_generator();
            $PDFContent = '';
            $PDFContent1 = '';
            $PDFContent2 = '';
            if (isset($_POST['upload_invoice_submit'])) {
                $invoicetext = '';
                $policytext = '';
                $commissiontext = '';
                if (!empty($_FILES["invoice"]["name"])) {
                    $PDFfileName = basename($_FILES["invoice"]["name"]);
                    $PDFfileType = pathinfo($PDFfileName, PATHINFO_EXTENSION);
                    $allowTypes  = array(
                        'pdf'
                    );
                    if (in_array($PDFfileType, $allowTypes)) {
                        $parser = new \Smalot\PdfParser\Parser();
                        $invoicepdf    = $parser->parseFile($_FILES["invoice"]["tmp_name"]);
                        
                        $invoicetext = $invoicepdf->getText();
                    
                    } else {
                        echo '<p>Invoice File : Only PDF file is allowed to upload.</p>';
                    }
                } 
                // else {
                //     echo '<p>Please select a invoice file.</p>';
                // }
                if (!empty($_FILES["pdf"]["name"])) {
                    $PDFfileName = basename($_FILES["pdf"]["name"]);
                    $PDFfileType = pathinfo($PDFfileName, PATHINFO_EXTENSION);
                    $allowTypes  = array(
                        'pdf'
                    );
                    if (in_array($PDFfileType, $allowTypes)) {
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf    = $parser->parseFile($_FILES["pdf"]["tmp_name"]);
                        
                        $policytext = $pdf->getText();
                    
                    } else {
                        echo '<p>Policy File :Only PDF file is allowed to upload.</p>';
                    }
                } 
                // else {
                //     echo '<p>Please select a policy file.</p>';
                // }
                if (!empty($_FILES["commission"]["name"])) {
                    $commissionfileName = basename($_FILES["commission"]["name"]);
                    $commissionfileType = pathinfo($commissionfileName, PATHINFO_EXTENSION);
                    $allowTypes  = array(
                        'pdf'
                    );
                    if (in_array($commissionfileType, $allowTypes)) {
                        $parser = new \Smalot\PdfParser\Parser();
                        $commissionpdf    = $parser->parseFile($_FILES["commission"]["tmp_name"]);
                        
                        $commissiontext = $commissionpdf->getText();
                    
                    } else {
                        echo '<p>Commission File: Only PDF file is allowed to upload.</p>';
                    }
                } 
                // else {
                //     echo '<p>Please select a commission file.</p>';
                // }
                if (!empty($_FILES["incentive"]["name"])) {
                    $incentivefileName = basename($_FILES["incentive"]["name"]);
                    $incentivefileType = pathinfo($incentivefileName, PATHINFO_EXTENSION);
                    $allowTypes  = array(
                        'pdf'
                    );
                    if (in_array($incentivefileType, $allowTypes)) {
                        $parser = new \Smalot\PdfParser\Parser();
                        $incentivepdf    = $parser->parseFile($_FILES["incentive"]["tmp_name"]);
                        
                        $incentivetext = $incentivepdf->getText();
                    
                    } else {
                        echo '<p>Incentive File : Only PDF file is allowed to upload.</p>';
                    }
                } 
                // else {
                //     echo '<p>Please select a incentive file.</p>';
                // }
                $invoicetext = str_replace(' ', '##', $invoicetext);
                $commissiontext = str_replace(' ', '##', $commissiontext);
                $incentivetext = str_replace(' ', '##', $incentivetext);
                $policytext = str_replace(' ', '##', $policytext);
                // echo $policytext;
                // echo $invoicetext;exit;
                // echo $commissiontext;exit;
                $data['supplier_id']=$this->input->post('supplier_id');
                if ($this->input->post('supplier_id') == 5 && $invoicetext != '') {
                    
                    $invoicetext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $invoicetext);
                    $commissiontext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $commissiontext);
                    // echo $invoicetext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $policytext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $commissiontext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // exit;
                    
                    $data['document_date']= date("Y-m-d", strtotime(str_replace('##', '-', $this->between ('Policy##Issuance##Date', 'Insurance##Period', $policytext))));
                    
                    $data['customer_name']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('Insured##Name', 'Date##of##Birth', $policytext)));
                    $data['customer_name'] = str_replace('####', '', $data['customer_name']);
                    
                    $data['policy_no'] = preg_replace('/[^A-Za-z0-9,. ]/', '####', $this->between ('POLICY##NO##:##', '##', $invoicetext));
                    $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                    
                    $data['debit_note_no'] = $this->between ('Policy##Number&&', '&', $invoicetext);
                    
                    $data['commission_credit_note_no'] = $this->between ('Billing##Date&&', '&', $commissiontext);
                    
                    $data['policy_from']= date("Y-m-d", strtotime(str_replace('##', '-', $this->between ('Insurance##Period', '####', $policytext))));
                    
                    $data['policy_to']= date("Y-m-d", strtotime(str_replace('##', '-', $this->between ('-####', '####', $policytext))));
                    
                    $data['sum_insured']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('Sum##Insured', 'Financed##by', $policytext)));
                    $data['sum_insured'] = str_replace('####', '', $data['sum_insured']);
                    
                    $data['interest'] = preg_replace('/[^A-Za-z0-9,. ]/', '',str_replace('##', '', $this->between ('Make##&##Model', 'Plate##', $policytext))) . ',Year ' . preg_replace('/[^A-Za-z0-9,.]/', '',$this->between ('Model##Year', 'Place##of##Registration', $policytext)) . ',Plate No ' . preg_replace('/[^A-Za-z0-9,.]/', '',$this->between ('Plate##Number', 'Purpose##of##', $policytext));
                    
                    
                    $resp = $this->between ('Total##Amount&&', '&Total##Amount', $invoicetext);
                    $totals = explode('&',$resp);
                    // var_dump($totals);exit;
                    $data['premium_amount'] = str_replace(',', '', $totals[1]);
                    $data['premium_vat'] = str_replace(',', '', $totals[2]);
                    $data['total_premium_amount'] = str_replace(',', '', $totals[3]);
                    $data['grand_total_price'] = str_replace(',', '', $totals[3]);
                    
                    
                    $resp = $this->between ('Qty&&&&', '&&&&Unit##price', $commissiontext);
                    $totals = explode('&&',$resp);
                    
                    $data['gross_commission_amount'] = $totals[1];
                    $data['gross_commission_vat'] = $totals[2];
                    $data['total_gross_commission_amount'] = $totals[3];
                    
                    // exit;
                    $data['supplier'] = $this->invoice_model->supplier_list();
                    $data['broker'] = $this->invoice_model->broker_list();
                    $data['salesman'] = $this->invoice_model->salesman_list();
                    
                    $data['module']        = "invoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 8 && $invoicetext != '') {
                    $invoicetext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $invoicetext);
                    $policytext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $policytext);
                    $commissiontext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $commissiontext);
                    // echo $invoicetext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $policytext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $commissiontext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // exit;
                    $data['document_date']= date("Y-m-d", strtotime($this->between ('Tax##Invoice##No##', '&Tax##Invoice', $invoicetext)));
                    
                    $data['customer_name']= str_replace('##', ' ', $this->between ('Date&To&', '&', $invoicetext));
                    
                    $data['policy_no'] = $this->between ('Policy##No.##&', '####', $invoicetext);
                    
                    $data['debit_note_no']= $this->between ('Division##', '&Tax##', $invoicetext);
                    
                    $data['commission_credit_note_no']= $this->between ('Division##', '&Tax##Invoice##', $commissiontext);
                    
                    $resp= $this->between ($data['policy_no'], '##to##', $invoicetext);
                    $data['policy_from']= date("Y-m-d", strtotime($this->between ($data['policy_no'], '##to##', $invoicetext)));
                    
                    $data['policy_to']= date("Y-m-d", strtotime($this->between ('##to##', '&Policy##', $invoicetext)));
                    
                    
                    $data['sum_insured']= str_replace('##', ' ', $this->between ('Total##Agreed##Premium##', '##/-', $policytext));
                    
                    $data['interest']= $this->between ('Account##No&', '&Registration##', $invoicetext) . ','. str_replace('##', ' ', $this->between ('Vehicle##Make##', '&', $invoicetext));
                    
                    $texts= $this->between ($data['policy_no'].'####', '####(Sub', $policytext);
                    $texts =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $texts);
                    
                    $data['premium_amount']= str_replace(',', '', $this->between ($data['policy_no'].'####', '&##', $invoicetext));
                    
                    $data['premium_vat']= str_replace(',', '', $this->between ('&####', '&Qty&&', $invoicetext));
                    
                    $data['total_premium_amount']= str_replace(',', '', $this->between ('&Qty&&##', '&##', $invoicetext));
                    
                    $data['gross_commission']= str_replace(',', '', $this->between ('Being##', '##%', $commissiontext));
                    
                    $text= $this->between ($data['policy_no'].'####', '%####', $commissiontext);
                    $tfg = explode('##',$text);
                    
                    $data['gross_commission_amount']= str_replace(',', '', $tfg[1]);
                    
                    $data['gross_commission_vat']= str_replace(',', '', $this->between ('%####', '&Qty&&##', $commissiontext));
                    
                    $data['total_gross_commission_amount']= str_replace(',', '', $this->between ($data['policy_no'].'####', '&##', $commissiontext));
                    
                    $data['grand_total_price']= $data['total_premium_amount'];
                    
                    $data['supplier'] = $this->invoice_model->supplier_list();
                    $data['broker'] = $this->invoice_model->broker_list();
                    $data['salesman'] = $this->invoice_model->salesman_list();
                    $data['module']        = "invoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 14 && $invoicetext != '') {
                    
                    $invoicetext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $invoicetext);
                    $policytext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $policytext);
                    $commissiontext =preg_replace('/[^A-Za-z0-9,.\/#+-=()%]/', '&', $commissiontext);
                    // echo $invoicetext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $policytext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $commissiontext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // exit;
                    // $data['policy_type']= preg_replace('/[^A-Za-z##]/', ' ', str_replace('##', ' ', $this->between ('POLICY##SCHEDULE', 'www.adamjeeinsurance.com', $policytext)));
                    // $data['customer_name'] = str_replace('####', '', $data['customer_name']);
                    // echo $data['policy_type'];exit;
                    
                    $data['customer_name']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('NAME##OF##INSURED', 'ADDRESS:', $policytext)));
                    $data['customer_name'] = str_replace('####', '', $data['customer_name']);
                    // var_dump($data['customer_name']);echo '<br>';
                    
                    $data['policy_no'] = preg_replace('/[^A-Za-z0-9,.-]/', '####', $this->between ('POLICY##NO:', 'CERTIFICATE##NO', $policytext));
                    $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                    // var_dump($data['policy_no']);echo '<br>';
                    
                    
                    $resp= $this->between ('DATE##OF##ISSUE:', '##', $policytext);
                    $da = explode('/',$resp);
                    $data['document_date']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    // var_dump($data['document_date']);echo '<br>';
                    
                    $data['premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Commission##&##all##allowance):AED##', 'PERIOD##OF##INSURANCE', $policytext));
                    // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                    // var_dump($data['premium_amount']);echo '<br>';
                    
                    
                    $data['premium_vat'] = preg_replace('/[^A-Za-z0-9,.]/', '####', str_replace('####', '', $this->between ('VAT@5%####', 'Premium##Payable', $policytext)));
                    $data['premium_vat'] = str_replace('####', '', $data['premium_vat']);
                    // var_dump($data['premium_vat']);echo '<br>';
                    
                    $data['total_premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '', $this->between ('Premium##Payable##################', '##:', $policytext));
                    
                    $data['grand_total_price'] =  $data['total_premium_amount'];
                    // var_dump($data['total_premium_amount']);echo '<br>';
                    
                    if($invoicetext != '') {
                    
                        $resp= $this->between ('PERIOD##OF##INSURANCE##:##FROM########', '##', $invoicetext);
                        $da = explode('/',$resp);
                        $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        // var_dump($data['policy_from']);echo '<br>';
                        
                        $resp= $this->between ('########To######', '##', $invoicetext);
                        $da = explode('/',$resp);
                        $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        // var_dump($data['policy_to']);echo '<br>';
                    
                        $data['debit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $invoicetext));
                        $data['debit_note_no'] = str_replace('####', '', $data['debit_note_no']);
                        // var_dump($data['debit_note_no']);echo '<br>';
                    
                        $data['policy_type'] = preg_replace('/[^A-Za-z0-9,.#]/', '####', $this->between ('POLICY##TYPE', 'CERTIFICATE', $invoicetext));
                        $data['policy_type'] = str_replace('##', ' ', $data['policy_type']);
                        // var_dump($data['policy_type']);echo '<br>';
                    
                        $data['debit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $invoicetext));
                        $data['debit_note_no'] = str_replace('####', '', $data['debit_note_no']);
                        // var_dump($data['debit_note_no']);echo '<br>';
                        
                        $resp= $this->between ('##and##expires##at##00:00##on####', '#', $policytext);
                        $da = explode('/',$resp);
                        $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        
                        $data['interest']= str_replace('##', ' ', preg_replace('/[^A-Za-z#]/', '', $this->between ('لكيهلا##مقر', 'Vehicle##value##', $policytext)));
                        
                    }
                    
                    if($commissiontext != '') {
                    
                        $data['commission_credit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $commissiontext));
                        $data['commission_credit_note_no'] = str_replace('####', '', $data['commission_credit_note_no']);
                        // var_dump($data['commission_credit_note_no']);echo '<br>';
                        
                        $data['gross_commission_amount'] = $this->between ('Commission##on##'.$data['policy_no'].'####', '##END', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        // var_dump($data['gross_commission_amount']);echo '<br>';
                        
                        $data['gross_commission'] = $this->between ('Being##your##', '##%####', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        // var_dump($data['gross_commission']);echo '<br>';
                        
                        $data['gross_commission_vat'] = $this->after ('VAT@##5%######', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        // var_dump($data['gross_commission_vat']);echo '<br>';
                        
                        $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Qty', 'Unit##price', $commissiontext));
                        $resp = str_replace('####', '', $resp);
                        $totals = explode('##',$resp);
                        
                        $data['total_gross_commission_amount'] = $this->between ('##DUE##', 'Amount##', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        // var_dump($data['total_gross_commission_amount']);echo '<br>';
                        
                    }
                    // exit;
                    
                    $data['supplier'] = $this->invoice_model->supplier_list();
                    $data['broker'] = $this->invoice_model->broker_list();
                    $data['salesman'] = $this->invoice_model->salesman_list();
                    $data['module']        = "invoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 12 && $invoicetext != '') {
                    $invoicetext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '&', $invoicetext);
                    $commissiontext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '&', $commissiontext);
                    
                    $resp= $this->between ('##on##', '##and##', $policytext);
                    $da = explode('/',$resp);
                    $data['document_date']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    
                    $sg=preg_replace('/[^A-Za-z#]/', '&&', $this->between ('the##Company', 'Insured##details', $policytext));
                    $data['customer_name']= str_replace('##', ' ', $this->between('##&&&&&&&&&&&&&&&&&&&&&&&&##&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&','&&&&&&&&&&&&&&&&&&&&&&',$sg));
                    
                    $data['policy_no'] = $this->getBetween($invoicetext,'Premium##due##for##our####Policy####(##', '##)####');
                    
                    $data['policy_type']= str_replace('##', ' ', $this->between ('Policy##Type&&', '&&', $invoicetext));
                    
                    $data['debit_note_no']= 'DN-'.$this->between ('&DN-', '&:&RAK', $invoicetext);
                    
                    $data['commission_credit_note_no']= 'CN-' . $this->between ('&CN-', '&:&RAK', $commissiontext);
                    
                    $resp= $this->between ('begins##at##00:00##on##', '##and##expires', $policytext);
                    $da = explode('/',$resp);
                    $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    
                    $resp= $this->between ('##and##expires##at##00:00##on####', '#', $policytext);
                    $da = explode('/',$resp);
                    $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    
                    $data['interest']= str_replace('##', ' ', preg_replace('/[^A-Za-z#]/', '', $this->between ('لكيهلا##مقر', 'Vehicle##value##', $policytext)));
                    
                    $data['premium_amount']= $this->between ('AED&##', '&Currency##', $invoicetext);
                    
                    $data['premium_vat']= $this->between ($data['premium_amount'].'&##', '&VAT##', $invoicetext);
                    
                    $data['total_premium_amount']= $this->between ('Gross##Amount##', '&&&&', $invoicetext);
                    
                    $data['grand_total_price']= $data['total_premium_amount'];
                    
                    $data['supplier'] = $this->invoice_model->supplier_list();
                    $data['broker'] = $this->invoice_model->broker_list();
                    $data['salesman'] = $this->invoice_model->salesman_list();
                    $data['module']        = "invoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 10 && $invoicetext != '') {
                    
                    $policytext2 =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '*', $policytext);
                    $invoicetext2 =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '*', $invoicetext);
                    $invoicetext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $invoicetext);
                    $commissiontext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $commissiontext);
                    $policytext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $policytext);
                    
                    
                    // echo $invoicetext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $policytext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $commissiontext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // exit;
                    
                    $resp= preg_replace('/[^A-Za-z0-9\/]/', '', $this->between ('Invoice##Date##', '####', $invoicetext));
                    $da = explode('/',$resp);
                    $data['document_date']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    // var_dump($data['document_date']);echo '<br>';
                    
                    $sg=preg_replace('/[^A-Za-z#]/', '&&', $this->between ('Ins.Insured##Name############', 'Policy##Type##', $invoicetext));
                    $sg= str_replace('##', ' ', $sg);
                    $data['customer_name']= str_replace('&&', ' ', $sg);
                    // var_dump($data['customer_name']);echo '<br>';
                    
                    $data['policy_no']=$this->between ('Policy##No.##', '############', $invoicetext);
                    
                    $data['policy_type']= str_replace('##', ' ', $this->between ('Policy##Type##', '########', $invoicetext));
                    // var_dump($data['policy_type']);echo '<br>';
                    
                    $data['debit_note_no']= $this->between ('Invoice##Number', '##Invoice##Date##', $invoicetext);
                    // var_dump($data['debit_note_no']);echo '<br>';
                    
                    $data['commission_credit_note_no']= $this->between ('Invoice##Number', '##Invoice##Date##', $commissiontext);
                    // var_dump($data['commission_credit_note_no']);echo '<br>';
                    
                    $resp= preg_replace('/[^A-Za-z0-9\/]/', '', $this->between ($data['policy_no'], '##to##', $invoicetext));
                    $da = explode('/',$resp);
                    $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    // var_dump($data['policy_from']);echo '<br>';
                    
                    $resp= preg_replace('/[^A-Za-z0-9\/]/', '', $this->between ('##to##', 'Period##of##', $invoicetext));
                    $da = explode('/',$resp);
                    $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    // var_dump($data['policy_to']);echo '<br>';
                    
                    $data['sum_insured']= $this->between ('Cylinders##:::::', 'AED##', $policytext);
                    // var_dump($data['sum_insured']);echo '<br>';
                    
                    $data['interest']= str_replace('##', ' ', $this->between ('Description####:*:*:*:*:*:*:*:*:*:*:*:##', '*', $policytext2));
                    // var_dump($data['interest']);echo '<br>';
                    
                    $jh = $this->between ('SubTotal##', '.', $invoicetext);
                    $data['total_premium_amount']= str_replace(',', '', $this->between ('SubTotal##', '.', $invoicetext));
                    // var_dump($data['total_premium_amount']);echo '<br>';
                    
                    // $data['premium_amount']= str_replace(',', '', $this->between ('*##', '*##'.$jh, $invoicetext2));
                    // var_dump($data['premium_amount']);echo '<br>';
                    
                    // $data['premium_vat']= $this->between ('VAT####', '####=##', $texts);
                    // var_dump($data['premium_vat']);echo '<br>';
                    
                    
                    $data['grand_total_price']= $data['total_premium_amount'];
                    // var_dump($data['grand_total_price']);echo '<br>';
                    
                    $data['supplier'] = $this->invoice_model->supplier_list();
                    $data['broker'] = $this->invoice_model->broker_list();
                    $data['salesman'] = $this->invoice_model->salesman_list();
                    $data['module']        = "invoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 16 && $invoicetext != '') {
                    
                    $policytext2 =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '*', $policytext);
                    $invoicetext2 =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '*', $invoicetext);
                    $invoicetext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $invoicetext);
                    $commissiontext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $commissiontext);
                    $policytext =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $policytext);
                    
                    
                    echo $invoicetext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    echo $policytext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    echo $commissiontext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    
                    $resp= $this->between ('DATEINVOICE#####', 'TAX##', $invoicetext);
                    $resp=substr($resp, 0, 10);
                    $da = explode('.',$resp);
                    $data['document_date']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    echo $data['document_date'];
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    exit;
                    // var_dump($data['document_date']);echo '<br>';
                    
                    $sg=preg_replace('/[^A-Za-z#]/', '&&', $this->between ('Ins.Insured##Name############', 'Policy##Type##', $invoicetext));
                    $sg= str_replace('##', ' ', $sg);
                    $data['customer_name']= str_replace('&&', ' ', $sg);
                    // var_dump($data['customer_name']);echo '<br>';
                    
                    $data['policy_no']=$this->between ('Policy##No.##', '############', $invoicetext);
                    
                    $data['policy_type']= str_replace('##', ' ', $this->between ('Policy##Type##', '########', $invoicetext));
                    // var_dump($data['policy_type']);echo '<br>';
                    
                    $data['debit_note_no']= $this->between ('Invoice##Number', '##Invoice##Date##', $invoicetext);
                    // var_dump($data['debit_note_no']);echo '<br>';
                    
                    $data['commission_credit_note_no']= $this->between ('Invoice##Number', '##Invoice##Date##', $commissiontext);
                    // var_dump($data['commission_credit_note_no']);echo '<br>';
                    
                    $resp= preg_replace('/[^A-Za-z0-9\/]/', '', $this->between ($data['policy_no'], '##to##', $invoicetext));
                    $da = explode('/',$resp);
                    $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    // var_dump($data['policy_from']);echo '<br>';
                    
                    $resp= preg_replace('/[^A-Za-z0-9\/]/', '', $this->between ('##to##', 'Period##of##', $invoicetext));
                    $da = explode('/',$resp);
                    $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    // var_dump($data['policy_to']);echo '<br>';
                    
                    $data['sum_insured']= $this->between ('Cylinders##:::::', 'AED##', $policytext);
                    // var_dump($data['sum_insured']);echo '<br>';
                    
                    $data['interest']= str_replace('##', ' ', $this->between ('Description####:*:*:*:*:*:*:*:*:*:*:*:##', '*', $policytext2));
                    // var_dump($data['interest']);echo '<br>';
                    
                    $jh = $this->between ('SubTotal##', '.', $invoicetext);
                    $data['total_premium_amount']= str_replace(',', '', $this->between ('SubTotal##', '.', $invoicetext));
                    // var_dump($data['total_premium_amount']);echo '<br>';
                    
                    // $data['premium_amount']= str_replace(',', '', $this->between ('*##', '*##'.$jh, $invoicetext2));
                    // var_dump($data['premium_amount']);echo '<br>';
                    
                    // $data['premium_vat']= $this->between ('VAT####', '####=##', $texts);
                    // var_dump($data['premium_vat']);echo '<br>';
                    
                    
                    $data['grand_total_price']= $data['total_premium_amount'];
                    // var_dump($data['grand_total_price']);echo '<br>';
                    
                    $data['supplier'] = $this->invoice_model->supplier_list();
                    $data['broker'] = $this->invoice_model->broker_list();
                    $data['salesman'] = $this->invoice_model->salesman_list();
                    $data['module']        = "invoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } else {
                    echo $policytext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    echo $invoicetext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    echo $commissiontext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    exit;
                }
                // Display content
                // echo $PDFContent;
                exit;
            }
        } else {
            
            $data['supplier'] = $this->invoice_model->supplier_list();
            $data['broker'] = $this->invoice_model->broker_list();
            $data['salesman'] = $this->invoice_model->salesman_list();
            $data['title']  = 'Invoice Parser';
            $data['module'] = "invoice";
            $data['page']   = "invoice_upload";
            echo Modules::run('template/layout', $data);
        }
    }
    
    function after ($first, $second) {
        if (!is_bool(strpos($second, $first)))
        return substr($second, strpos($second,$first)+strlen($first));
    }

    function after_last ($first, $second) {
        if (!is_bool(strrevpos($second, $first)))
        return substr($second, strrevpos($second, $first)+strlen($first));
    }

    function before ($first, $second) {
        return substr($second, 0, strpos($second, $first));
    }
    
    function between ($first, $that, $second) {
        return $this->before ($that, $this->after($first, $second));
    }
    
    function getBetween($string, $start = "", $end = ""){
        if (strpos($string, $start)) { // required if $start not exist in $string
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }
    
}