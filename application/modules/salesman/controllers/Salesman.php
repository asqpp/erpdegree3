<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Cloudsubset
    # Author link: https://www.cloudsubset.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Salesman extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
  
        $this->load->model(array(
            'salesman_model')); 
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
          
    }
    
    function index() {
        $data['title']      = display('salesman_list');
        $data['module']     = "salesman";
        $data['page']       = "salesman_list"; 
        $data["salesman_dropdown"] = $this->salesman_model->salesman_dropdown();
        $data['all_salesman'] = $this->salesman_model->allsalesman(); 
        echo modules::run('template/layout', $data);
    }


    public function cloudsubset_ChecksalesmanList(){
        $postData = $this->input->post();
        $data     = $this->salesman_model->getsalesmanList($postData);
        echo json_encode($data);
    }



  public function cloudsubset_form($id = null)
    {
        $data['title'] = display('add_salesman');
        #-------------------------------#
        $this->form_validation->set_rules('salesman_name',display('salesman_name'),'required|max_length[200]');
        $this->form_validation->set_rules('salesman_mobile', display('salesman_mobile') ,'max_length[20]');
        if(empty($id)){
        $this->form_validation->set_rules('salesman_email',display('email'),'max_length[100]|valid_email|is_unique[salesman_information.email_address]');
    }else{
        $this->form_validation->set_rules('salesman_email',display('email'),'max_length[100]|valid_email');
    }
        $this->form_validation->set_rules('contact',display('contact'),'max_length[200]');
        $this->form_validation->set_rules('phone',display('phone'),'max_length[20]');
        $this->form_validation->set_rules('city',display('city'),'max_length[100]'); 
        $this->form_validation->set_rules('state',display('state'),'max_length[100]');
        $this->form_validation->set_rules('zip',display('zip'),'max_length[30]');
        $this->form_validation->set_rules('country',display('country'),'max_length[100]');  
        $this->form_validation->set_rules('salesman_address',display('salesman_address'),'max_length[255]');
        $this->form_validation->set_rules('address2',display('address2'),'max_length[255]'); 
        
      
        #-------------------------------#

        $data['salesman'] = (object)$postData = [
            'salesman_id'      => $this->input->post('salesman_id',true),
            'salesman_name'    => $this->input->post('salesman_name',true),
            'mobile'           => $this->input->post('salesman_mobile', true),
            'emailnumber'      => $this->input->post('salesman_email', true),
            'email_address'    => $this->input->post('email_address', true),
            'contact'          => $this->input->post('contact', true),
            'phone'            => $this->input->post('phone', true),
            'fax'              => $this->input->post('fax', true), 
            'city'             => $this->input->post('city', true) ,
            'state'            => $this->input->post('state', true) ,
            'zip'              => $this->input->post('zip', true) ,
            'country'          => $this->input->post('country', true) ,
            'address'          => $this->input->post('salesman_address', true) ,
            'address2'         => $this->input->post('address2', true) ,
            'status'           => 1,
            
        ]; 
        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($postData['salesman_id'])) {
                if ($this->salesman_model->create($postData)) {
                    #set success message
                    
                        $info['msg']    = display('save_successfully');
                        $info['status'] = 1;
                } else {
                    #set exception message
                    
                        $info['msg']    = display('please_try_again');
                        $info['status'] = 0;
                }
            } else {
                if ($this->salesman_model->update($postData)) {
                    #set success message
                    $info['msg']    = display('update_successfully');
                    $info['status'] = 1;
                } else {
                    #set exception message
                    $info['msg']    = display('please_try_again');
                    $info['status'] = 0;
                } 
            }
 
            echo json_encode($info);

        } else { 
            if(empty($this->input->post('salesman_name',true))){
            if(!empty($id)){
            $data['title']    = display('edit_salesman');
            $data['salesman'] = $this->salesman_model->singledata($id);  
            }
            $data['module']   = "salesman";  
            $data['page']     = "form";  
            echo Modules::run('template/layout', $data); 
        }else{

          $info['msg']    = validation_errors();
          $info['status'] = 0;
           echo json_encode($info);
        }
        } 
    }



    public function cloudsubset_delete($id) {
        if ($this->salesman_model->delete($id)) {
            echo display('delete_successfully');
        } else {
            display('please_try_again');
        }
    }

    public function salesman_search($id){
       $data["salesmans"] = $this->salesman_model->individual_info($id);
        $this->load->view('salesman_search', $data);
    }

    public function cloudsubset_salesman_ledger() {
   $data['title']    = display('salesman_ledger'); 
        #-------------------------------#       
        #
        #pagination starts
        #
    $config["base_url"]    = base_url('salesman_ledger');
    $config["total_rows"]  = $this->salesman_model->count_salesman_ledger();
    $config["per_page"]    = 10;
    $config["uri_segment"] = 2;
    $config["last_link"]   = "Last"; 
    $config["first_link"]  = "First"; 
    $config['next_link']   = 'Next';
    $config['prev_link']   = 'Prev';  
    $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
    $config['full_tag_close'] = "</ul>";
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
    $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
    $config['next_tag_open'] = "<li>";
    $config['next_tag_close'] = "</li>";
    $config['prev_tag_open'] = "<li>";
    $config['prev_tagl_close'] = "</li>";
    $config['first_tag_open'] = "<li>";
    $config['first_tagl_close'] = "</li>";
    $config['last_tag_open'] = "<li>";
    $config['last_tagl_close'] = "</li>";
    /* ends of bootstrap */
    $this->pagination->initialize($config);
    $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
    $data["ledgers"]  = $this->salesman_model->salesman_ledgerdata($config["per_page"], $page);
    $data["links"]    = $this->pagination->create_links();
    $data['salesman'] = $this->salesman_model->salesman_list_ledger();
    $data['salesman_name'] = '';
    $data['salesman_id'] = '';
    $data['address']  ='';
    $data['module']   = "salesman";
    $data['page']     = "salesman_ledger";   
    echo Modules::run('template/layout', $data); 
    }

    public function cloudsubset_salesman_ledgerData() {
    $start           = $this->input->post('from_date',true);
    $end             = $this->input->post('to_date',true);
    $salesman_id     = $this->input->post('salesman_id',true);
    $pre_balance = $this->salesman_model->get_opening_balance(
        $salesman_id,
        $start,
        $end
    );
    
        $data["start"] = $start;
        $data["end"] = $end;
    $salesman_detail = $this->salesman_model->salesman_personal_data($salesman_id);
    $data['title']   = display('salesman_ledger');
    $data['salesman']= $this->salesman_model->salesman_list_ledger();
    $data["ledgers"] = $this->salesman_model->salesmanledger_searchdata($salesman_id, $start, $end);
    $data['salesman_name'] = $salesman_detail[0]['salesman_name'];
    $data['salesman_id'] = $salesman_id;
    $data['address']  = $salesman_detail[0]['address'];
    $data['module']   = "salesman";
    $data["links"]    = '';
    $data['page']     = "salesman_ledger";   
    $data['pre_balance'] = $pre_balance;
    echo Modules::run('template/layout', $data); 
    }


    public function cloudsubset_salesman_advance() {
    $data['title'] = display('salesman_advance');    
    $data['salesman_list']= $this->salesman_model->salesman_list_advance();
    $data['module']= "salesman";
    $data['page']  = "salesman_advance";   
    echo Modules::run('template/layout', $data); 
    }

      public function insert_salesman_advance(){
        $advance_type = $this->input->post('type',TRUE);
        if($advance_type ==1){
            $dr = $this->input->post('amount',TRUE);
            $tp = 'd';
        }else{
            $cr = $this->input->post('amount',TRUE);
            $tp = 'c';
        }
            $createby=$this->session->userdata('id');
            $createdate=date('Y-m-d H:i:s');
            $transaction_id=$this->salesman_model->generator(10);
            $salesman_id = $this->input->post('salesman_id',TRUE);
            $salesmaninfo = $this->db->select('*')->from('salesman_information')->where('salesman_id',$salesman_id)->get()->row();
    $headn = $salesman_id.'-'.$salesmaninfo->salesman_name;
    $coainfo = $this->db->select('*')->from('acc_coa')->where('salesman_id',$salesman_id)->get()->row();
    $salesman_headcode = $coainfo->HeadCode;
              
                   $salesman_accledger = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'Advance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  $salesman_headcode,
      'Narration'      =>  'salesman Advance For  '.$salesmaninfo->salesman_name,
      'Debit'          =>  (!empty($dr)?$dr:0),
      'Credit'         =>  (!empty($cr)?$cr:0),
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );
                         $cc = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'Advance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  111000001,
      'Narration'      =>  'Cash in Hand  For '.$salesmaninfo->salesman_name.' Advance',
      'Debit'          =>  (!empty($dr)?$dr:0),
      'Credit'         =>  (!empty($cr)?$cr:0),
      'IsPosted'       =>  1,
      'CreateBy'       =>  $this->session->userdata('id'),
      'CreateDate'     =>  date('Y-m-d H:i:s'),
      'IsAppove'       =>  1
    ); 
                  
       $this->db->insert('acc_transaction',$salesman_accledger);
       $this->db->insert('acc_transaction',$cc);
       redirect(base_url('salesman_advance_receipt/'.$transaction_id.'/'.$salesman_id));

  }

  //salesman_advance_receipt
   public function salesman_advancercpt($receiptid=null,$salesman_id=null) {
    $data['title']         = display('advance_receipt'); 
    $salesman_id           = $this->uri->segment(3);
    $receiptdata           = $this->salesman_model->advance_details($receiptid,$salesman_id);
    $salesman_details      = $this->salesman_model->salesman_personal_data($salesman_id);
    $data['details']       = $receiptdata;
    $data['salesman_name'] = $salesman_details[0]['salesman_name'];
    $data['receipt_no']    = $receiptdata[0]['VNo'];
    $data['address']       = $salesman_details[0]['address'];
    $data['mobile']        = $salesman_details[0]['mobile'];
    $data['module']        = "salesman";
    $data['page']          = "salesman_advance_receipt";   
    echo Modules::run('template/layout', $data); 
    }

        public function cloudsubset_salesman_ledgerinfo($salesman_id) {
        $salesman_details = $this->salesman_model->salesman_personal_data($salesman_id);
        $salesman         = $this->salesman_model->salesman_list_advance();
        $ledgers          = $this->salesman_model->salesman_product_sale_info($salesman_id);

        $data = array(
            'title'           => display('salesman_ledger'),
            'ledgers'         => $ledgers,
            'salesman_id'     => $salesman_id,
            'salesman_name'   => $salesman_details[0]['salesman_name'],
            'address'         => $salesman_details[0]['address'],
            'salesman'        => $salesman,
            'links'           => '',
        );

    $data['module']    = "salesman";
    $data['page']      = "salesman_ledger";   
    echo Modules::run('template/layout', $data);
    }

}

