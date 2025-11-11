<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Cloudsubset
    # Author link: https://www.cloudsubset.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Broker_model extends CI_Model {

     
   public function create($data = array())
	{
		$add_broker =  $this->db->insert('broker_information', $data);

		 $broker_id = $this->db->insert_id();
        $coa = $this->headcode();
           if($coa->HeadCode!=NULL){
                $headcode=$coa->HeadCode+1;
           }else{
                $headcode="21110000001";
            }
    $c_acc=$broker_id.'-'.$data['broker_name'];
    $createby=$this->session->userdata('id');
    $createdate=date('Y-m-d H:i:s');
       

    $broker_coa = [
             'HeadCode'        => $headcode,
            'HeadName'         => $c_acc,
            'PHeadName'        => 'Brokers',
            'HeadLevel'        => '4',
            'IsActive'         => '1',
            'IsTransaction'    => '1',
            'IsGL'             => '0',
            'HeadType'         => 'L',
            'IsBudget'         => '0',
            'broker_id'      => $broker_id,
            'IsDepreciation'   => '0',
            'DepreciationRate' => '0',
            'CreateBy'         => $createby,
            'CreateDate'       => $createdate,
        ];

        $sub_acc = [
            'subTypeId'   => 7,
            'name'        => $data['broker_name'],
            'referenceNo' => $broker_id,
            'status'      => 1,
            'created_date'=> date("Y-m-d"),
            
       ];

        if($add_broker){
           
            $this->db->insert('acc_subcode',$sub_acc);
        }
        if(!empty($this->input->post('previous_balance'))){
        
          }
        return true;
	}

	public function broker_dropdown()
	{
		$data =  $this->db->select("*")
			->from('broker_information')
			->order_by('broker_name', 'asc')
			->get()
			->result();

      $list[''] = display('select_option');
    if (!empty($data)) {
      foreach($data as $value)
        $list[$value->broker_id] = $value->broker_name;
      return $list;
    } else {
      return false; 
    }
	}





	public function broker_list($offset=null, $limit=null)
    {
  

        return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
			->from('broker_information a')
			->join('acc_coa b','a.broker_id = b.broker_id','left')
			->group_by('a.broker_id')
			->order_by('a.broker_name', 'asc')
			->limit($offset, $limit)
			->get()
			->result();

         
    }


      public function getbrokerList($postData=null){

         $response = array();
         $broker_id =  $this->input->post('broker_id');
         $custom_data = $this->input->post('customfiled');
         if(!empty($custom_data)){
         $cus_data = [''];
         foreach ($custom_data as $cusd) {
           $cus_data[] = $cusd;
         }
       }
    
         ## Read value
         $draw = $postData['draw'];
         $start = $postData['start'];
         $rowperpage = $postData['length']; // Rows display per page
         $columnIndex = $postData['order'][0]['column']; // Column index
         $columnName = $postData['columns'][$columnIndex]['data']; // Column name
         $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
         $searchValue = $postData['search']['value']; // Search value

         ## Search 
         $searchQuery = "";
         if($searchValue != ''){
            $searchQuery = " (a.broker_name like '%".$searchValue."%' or a.mobile like '%".$searchValue."%' or a.emailnumber like '%".$searchValue."%'or a.phone like '%".$searchValue."%' or a.address like '%".$searchValue."%' or a.country like '%".$searchValue."%' or a.state like '%".$searchValue."%' or a.zip like '%".$searchValue."%' or a.city like '%".$searchValue."%') ";
         }

         ## Total number of records without filtering
         $this->db->select('count(*) as allcount');
         $this->db->from('broker_information a');
         $this->db->join('acc_coa b','a.broker_id = b.broker_id','left');
         
         if(!empty($broker_id)){
             $this->db->where('a.broker_id',$broker_id);
         }
         if(!empty($custom_data)){
             $this->db->where_in('a.broker_id',$cus_data);
         }
          if($searchValue != '')
         $this->db->where($searchQuery);
         $this->db->group_by('a.broker_id');
         $totalRecords =$this->db->get()->num_rows();

         ## Total number of record with filtering
         $this->db->select('count(*) as allcount');
         $this->db->from('broker_information a');
         $this->db->join('acc_coa b','a.broker_id = b.broker_id','left');
         if(!empty($broker_id)){
             $this->db->where('a.broker_id',$broker_id);
         }
          if(!empty($custom_data)){
             $this->db->where_in('a.broker_id',$cus_data);
         }
         if($searchValue != '')
            $this->db->where($searchQuery);
           $this->db->group_by('a.broker_id');
         $totalRecordwithFilter = $this->db->get()->num_rows();

         ## Fetch records
         $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
         $this->db->from('broker_information a');
         $this->db->join('acc_coa b','a.broker_id = b.broker_id','left');
         $this->db->group_by('a.broker_id');
          if(!empty($broker_id)){
             $this->db->where('a.broker_id',$broker_id);
         }
          if(!empty($custom_data)){
             $this->db->where_in('a.broker_id',$cus_data);
         }
         if($searchValue != '')
         $this->db->where($searchQuery);
         $this->db->order_by($columnName, $columnSortOrder);
         $this->db->limit($rowperpage, $start);
         $records = $this->db->get()->result();
         $data = array();
         $sl =1;
  
         foreach($records as $record ){
          $button = '';
          $base_url = base_url();
 
          if($this->permission1->method('manage_broker','update')->access()){
              $button .=' <a href="'.$base_url.'edit_broker/'.$record->broker_id.'" class="btn btn-info btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>';
            }
        if($this->permission1->method('manage_broker','delete')->access()){
            $button .=' <a onclick="brokerdelete('.$record->broker_id.')" href="javascript:void(0)"  class="btn btn-danger btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>';
        }


        
               
            $data[] = array( 
                'sl'               =>$sl,
                'broker_name'    =>$record->broker_name,
                'address'          =>$record->address,
                'address2'         =>$record->address2,
                'mobile'           =>$record->mobile,
                'phone'            =>$record->phone,
                'email'            =>$record->emailnumber,
                'email_address'    =>$record->email_address,
                'contact'          =>$record->contact,
                'fax'              =>$record->fax,
                'city'             =>$record->city,
                'state'            =>$record->state,
                'zip'              =>$record->zip,
                'country'          =>$record->country,
                'balance'          =>(!empty($record->balance)?$record->balance:0),
                'button'           =>$button,
                
            ); 
            $sl++;
         }

         ## Response
         $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
         );

         return $response; 
    }



        
    
    public function individual_info($id){
      return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('broker_information a')
      ->join('acc_coa b','a.broker_id = b.broker_id','left')
      ->where('a.broker_id',$id)
      ->group_by('a.broker_id')
      ->order_by('a.broker_name', 'asc')
      ->get()
      ->result();
    }






	public function singledata($id = null)
	{
		return $this->db->select('*')
			->from('broker_information')
			->where('broker_id', $id)
			->get()
			->row();
	}

  public function allbroker()
  {
    return $this->db->select('*')
      ->from('broker_information')
      ->get()
      ->result();
  }




	public function update($data = array())
	{
		$updatebroker =  $this->db->where('broker_id', $data["broker_id"])
			->update("broker_information", $data);

		$broker_id = $data["broker_id"];
        $old_headnam = $broker_id.'-'.$this->input->post("old_name");
        $c_acc=$broker_id.'-'.$data["broker_name"];
         $broker_coa = [
             'HeadName'         => $c_acc
        ];
 

        $sub_acc = [
            'name'        => $data['broker_name'],
          ];

        $this->db->where('referenceNo', $broker_id)
                 ->where('subTypeId', 4)
                 ->update('acc_subcode',$sub_acc);
    
    return true;
	}

	public function delete($id = null)
	{

        $this->db->where('referenceNo', $id)
                 ->where('subTypeId', 4)
                 ->delete('acc_subcode');

		return $this->db->where('broker_id', $id)
			->delete("broker_information");
	}


	   public function headcode(){
         $query=$this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '21110%'");
        return $query->row();

    }


      public function previous_balance_add($balance, $broker_id) {
    $cusifo = $this->db->select('*')->from('broker_information')->where('broker_id',$broker_id)->get()->row();
    $headn = $broker_id.'-'.$cusifo->broker_name;
    $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName',$headn)->get()->row();
    $broker_headcode = $coainfo->HeadCode;
        $transaction_id = $this->generator(10);
       

// broker debit for previous balance
      $cosdr = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  $broker_headcode,
      'Narration'      =>  'broker debit For '.$cusifo->broker_name,
      'Debit'          =>  $balance,
      'Credit'         =>  0,
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );
       $inventory = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  1141,
      'Narration'      =>  'Inventory credit For Old sale For'.$cusifo->broker_name,
      'Debit'          =>  0,
      'Credit'         =>  $balance,//purchase price asbe
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    ); 

       
        if(!empty($balance)){
           $this->db->insert('acc_transaction', $cosdr); 
           $this->db->insert('acc_transaction', $inventory); 
        }
    }



public function generator($lenth)
    {
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


          public function broker_ledgerdata($per_page, $page) {
        $this->db->select('a.*,b.HeadName');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        $this->db->where('b.PHeadName','Brokers');
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $this->db->limit($per_page, $page);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
         
            return $query->result_array();
        }
        return false;
    }

        
        public function count_broker_ledger() {
        $this->db->select('a.*,b.HeadName');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        $this->db->where('b.PHeadName','Brokers');
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
  

      public function broker_list_ledger() {
        $this->db->select('*');
        $this->db->from('broker_information');
        $this->db->order_by('broker_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

        public function broker_personal_data($broker_id) {
        $this->db->select('*');
        $this->db->from('broker_information');
        $this->db->where('broker_id', $broker_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
//Get Opening balance
    public function get_opening_balance($broker_id, $start, $end) {

        $fyearStartDate = $this->session->userdata('fyearStartDate');
        $fyearEndDate = $this->session->userdata('fyearEndDate');
        $oldDate = date('Y-m-d',strtotime($start. ' -1 year'));
        $prevDate = date('Y-m-d', strtotime($start .' - 1day'));
        $oldBalance = 0;
        $balance = 0;
        $opening =  $this->get_general_ledger_report($broker_id,$fyearStartDate,$prevDate);
        $pre_balance = $this->broker_model->get_opening_balance(
            $broker_id,
            $start,
            $end
        );
        // echo $start;
        // echo '<br>';
        // echo $end;
        // echo '<br>';
        // echo $fyearStartDate;
        // echo '<br>';
        // echo $prevDate;
        if($opening) {
         foreach($opening as $open) {
            //  if($coaHead->HeadType == 'A' || $coaHead->HeadType == 'E') {
            //      $balance= ($open->debit - $open->credit);
            //   } else {
                 $balance+=($open->Credit - $open->Debit);
            //   }
         }
         
        } else {
        $balance= 0;
        }
        return $newBalance = $oldBalance + $balance ; 
                         
    }
    
   // get general ledger report
    public function get_general_ledger_report($broker_id, $start, $end) {
        
        $this->db->select('a.*,b.name,d.customer_name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_subcode b','a.subCode=b.id');
        $this->db->join('broker_information c','c.broker_id =b.referenceNo');
        $this->db->join('invoice i','i.inv_id =a.referenceNo');
        $this->db->join('customer_information d','d.customer_id =i.customer_id');
        $this->db->where(array('c.broker_id' => $broker_id, 'a.subType' => 7, 'b.subTypeId' => '7', 'a.VDate >=' => $start, 'a.VDate <=' => $end));
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function brokerledger_searchdata($broker_id, $start, $end) {
        
        $this->db->select('a.*,b.name,d.customer_name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_subcode b','a.subCode=b.id');
        $this->db->join('broker_information c','c.broker_id =b.referenceNo');
        $this->db->join('invoice i','i.inv_id =a.referenceNo');
        $this->db->join('customer_information d','d.customer_id =i.customer_id');
        $this->db->where(array('c.broker_id' => $broker_id, 'a.subType' => 7, 'b.subTypeId' => '7', 'a.VDate >=' => $start, 'a.VDate <=' => $end));
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        // $query->result_array();
        // echo $this->db->last_query();exit;
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
        // $this->db->select('a.*,b.HeadName');
        // $this->db->from('acc_transaction a');
        // $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        // $this->db->where(array('b.broker_id' => $broker_id, 'a.VDate >=' => $start, 'a.VDate <=' => $end));
        // $this->db->where('a.IsAppove',1);
        // $this->db->order_by('a.VDate','desc');
        // $query = $this->db->get();
        // if ($query->num_rows() > 0) {
        //     return $query->result_array();
        // }
        // return false;
    }

        public function broker_list_advance(){
        $this->db->select('*');
        $this->db->from('broker_information');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

        public function advance_details($transaction_id,$broker_id){

        $headcode = $this->db->select('HeadCode')->from('acc_coa')->where('broker_id',$broker_id)->get()->row();
        return $data  = $this->db->select('*')
                        ->from('acc_transaction')
                        ->where('VNo',$transaction_id)
                        ->where('COAID',$headcode->HeadCode)
                        ->get()
                        ->result_array();

    }

        public function broker_product_sale_info($broker_id) {
        $this->db->select('a.*,b.HeadName');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        $this->db->where('b.broker_id',$broker_id);
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

}

