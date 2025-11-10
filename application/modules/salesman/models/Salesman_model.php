<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Cloudsubset
    # Author link: https://www.cloudsubset.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Salesman_model extends CI_Model {

     
   public function create($data = array())
	{
		$add_salesman =  $this->db->insert('salesman_information', $data);

		 $salesman_id = $this->db->insert_id();
        $coa = $this->headcode();
           if($coa->HeadCode!=NULL){
                $headcode=$coa->HeadCode+1;
           }else{
                $headcode="21110000001";
            }
    $c_acc=$salesman_id.'-'.$data['salesman_name'];
    $createby=$this->session->userdata('id');
    $createdate=date('Y-m-d H:i:s');
       

    $salesman_coa = [
             'HeadCode'        => $headcode,
            'HeadName'         => $c_acc,
            'PHeadName'        => 'Salesmans',
            'HeadLevel'        => '4',
            'IsActive'         => '1',
            'IsTransaction'    => '1',
            'IsGL'             => '0',
            'HeadType'         => 'L',
            'IsBudget'         => '0',
            'salesman_id'      => $salesman_id,
            'IsDepreciation'   => '0',
            'DepreciationRate' => '0',
            'CreateBy'         => $createby,
            'CreateDate'       => $createdate,
        ];

        $sub_acc = [
            'subTypeId'   => 5,
            'name'        => $data['salesman_name'],
            'referenceNo' => $salesman_id,
            'status'      => 1,
            'created_date'=> date("Y-m-d"),
            
       ];

        if($add_salesman){
           
            $this->db->insert('acc_subcode',$sub_acc);
        }
        if(!empty($this->input->post('previous_balance'))){
        
          }
        return true;
	}

	public function salesman_dropdown()
	{
		$data =  $this->db->select("*")
			->from('salesman_information')
			->order_by('salesman_name', 'asc')
			->get()
			->result();

      $list[''] = display('select_option');
        if (!empty($data)) {
          foreach($data as $value)
            $list[$value->salesman_id] = $value->salesman_name;
          return $list;
        } else {
          return false; 
        }
	}
	
	public function salesman_list($offset=null, $limit=null) {
  

        return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
			->from('salesman_information a')
			->join('acc_coa b','a.salesman_id = b.salesman_id','left')
			->group_by('a.salesman_id')
			->order_by('a.salesman_name', 'asc')
			->limit($offset, $limit)
			->get()
			->result();

         
    }


      public function getsalesmanList($postData=null){

         $response = array();
         $salesman_id =  $this->input->post('salesman_id');
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
            $searchQuery = " (a.salesman_name like '%".$searchValue."%' or a.mobile like '%".$searchValue."%' or a.emailnumber like '%".$searchValue."%'or a.phone like '%".$searchValue."%' or a.address like '%".$searchValue."%' or a.country like '%".$searchValue."%' or a.state like '%".$searchValue."%' or a.zip like '%".$searchValue."%' or a.city like '%".$searchValue."%') ";
         }

         ## Total number of records without filtering
         $this->db->select('count(*) as allcount');
         $this->db->from('salesman_information a');
         $this->db->join('acc_coa b','a.salesman_id = b.salesman_id','left');
         
         if(!empty($salesman_id)){
             $this->db->where('a.salesman_id',$salesman_id);
         }
         if(!empty($custom_data)){
             $this->db->where_in('a.salesman_id',$cus_data);
         }
          if($searchValue != '')
         $this->db->where($searchQuery);
         $this->db->group_by('a.salesman_id');
         $totalRecords =$this->db->get()->num_rows();

         ## Total number of record with filtering
         $this->db->select('count(*) as allcount');
         $this->db->from('salesman_information a');
         $this->db->join('acc_coa b','a.salesman_id = b.salesman_id','left');
         if(!empty($salesman_id)){
             $this->db->where('a.salesman_id',$salesman_id);
         }
          if(!empty($custom_data)){
             $this->db->where_in('a.salesman_id',$cus_data);
         }
         if($searchValue != '')
            $this->db->where($searchQuery);
           $this->db->group_by('a.salesman_id');
         $totalRecordwithFilter = $this->db->get()->num_rows();

         ## Fetch records
         $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
         $this->db->from('salesman_information a');
         $this->db->join('acc_coa b','a.salesman_id = b.salesman_id','left');
         $this->db->group_by('a.salesman_id');
          if(!empty($salesman_id)){
             $this->db->where('a.salesman_id',$salesman_id);
         }
          if(!empty($custom_data)){
             $this->db->where_in('a.salesman_id',$cus_data);
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
 
          if($this->permission1->method('manage_salesman','update')->access()){
              $button .=' <a href="'.$base_url.'edit_salesman/'.$record->salesman_id.'" class="btn btn-info btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>';
            }
        if($this->permission1->method('manage_salesman','delete')->access()){
            $button .=' <a onclick="salesmandelete('.$record->salesman_id.')" href="javascript:void(0)"  class="btn btn-danger btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>';
        }


        
               
            $data[] = array( 
                'sl'               =>$sl,
                'salesman_name'    =>$record->salesman_name,
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
      ->from('salesman_information a')
      ->join('acc_coa b','a.salesman_id = b.salesman_id','left')
      ->where('a.salesman_id',$id)
      ->group_by('a.salesman_id')
      ->order_by('a.salesman_name', 'asc')
      ->get()
      ->result();
    }






	public function singledata($id = null)
	{
		return $this->db->select('*')
			->from('salesman_information')
			->where('salesman_id', $id)
			->get()
			->row();
	}

  public function allsalesman()
  {
    return $this->db->select('*')
      ->from('salesman_information')
      ->get()
      ->result();
  }




	public function update($data = array())
	{
		$updatesalesman =  $this->db->where('salesman_id', $data["salesman_id"])
			->update("salesman_information", $data);

		$salesman_id = $data["salesman_id"];
        $old_headnam = $salesman_id.'-'.$this->input->post("old_name");
        $c_acc=$salesman_id.'-'.$data["salesman_name"];
         $salesman_coa = [
             'HeadName'         => $c_acc
        ];
 

        $sub_acc = [
            'name'        => $data['salesman_name'],
          ];

        $this->db->where('referenceNo', $salesman_id)
                 ->where('subTypeId', 4)
                 ->update('acc_subcode',$sub_acc);
    
    return true;
	}

	public function delete($id = null)
	{

        $this->db->where('referenceNo', $id)
                 ->where('subTypeId', 4)
                 ->delete('acc_subcode');

		return $this->db->where('salesman_id', $id)
			->delete("salesman_information");
	}


	   public function headcode(){
         $query=$this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '21110%'");
        return $query->row();

    }


      public function previous_balance_add($balance, $salesman_id) {
    $cusifo = $this->db->select('*')->from('salesman_information')->where('salesman_id',$salesman_id)->get()->row();
    $headn = $salesman_id.'-'.$cusifo->salesman_name;
    $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName',$headn)->get()->row();
    $salesman_headcode = $coainfo->HeadCode;
        $transaction_id = $this->generator(10);
       

// salesman debit for previous balance
      $cosdr = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  $salesman_headcode,
      'Narration'      =>  'salesman debit For '.$cusifo->salesman_name,
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
      'Narration'      =>  'Inventory credit For Old sale For'.$cusifo->salesman_name,
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


          public function salesman_ledgerdata($per_page, $page) {
        $this->db->select('a.*,b.HeadName');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        $this->db->where('b.PHeadName','Salesmans');
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $this->db->limit($per_page, $page);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
         
            return $query->result_array();
        }
        return false;
    }

        
        public function count_salesman_ledger() {
        $this->db->select('a.*,b.HeadName');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        $this->db->where('b.PHeadName','Salesmans');
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
  

      public function salesman_list_ledger() {
        $this->db->select('*');
        $this->db->from('salesman_information');
        $this->db->order_by('salesman_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

        public function salesman_personal_data($salesman_id) {
        $this->db->select('*');
        $this->db->from('salesman_information');
        $this->db->where('salesman_id', $salesman_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
//Get Opening balance
    public function get_opening_balance($salesman_id, $start, $end) {

        $fyearStartDate = $this->session->userdata('fyearStartDate');
        $fyearEndDate = $this->session->userdata('fyearEndDate');
        $oldDate = date('Y-m-d',strtotime($start. ' -1 year'));
        $prevDate = date('Y-m-d', strtotime($start .' - 1day'));
        $oldBalance = 0;
        $balance = 0;
        $opening =  $this->get_general_ledger_report($salesman_id,$fyearStartDate,$prevDate);
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
    public function get_general_ledger_report($salesman_id, $start, $end) {
        $this->db->select('a.*,b.name, d.customer_name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_subcode b','a.subCode=b.id');
        $this->db->join('salesman_information c','c.salesman_id =b.referenceNo');
        $this->db->join('invoice i','i.inv_id =a.referenceNo');
        $this->db->join('customer_information d','d.customer_id =i.customer_id');
        $this->db->where(array('c.salesman_id' => $salesman_id, 'a.subType' => 5, 'b.subTypeId' => '5', 'a.VDate >=' => $start, 'a.VDate <=' => $end));
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }
    public function salesmanledger_searchdata($salesman_id, $start, $end) {
        $this->db->select('a.*,b.name, d.customer_name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_subcode b','a.subCode=b.id');
        $this->db->join('salesman_information c','c.salesman_id =b.referenceNo');
        $this->db->join('invoice i','i.inv_id =a.referenceNo');
        $this->db->join('customer_information d','d.customer_id =i.customer_id');
        $this->db->where(array('c.salesman_id' => $salesman_id, 'a.subType' => 5, 'b.subTypeId' => '5', 'a.VDate >=' => $start, 'a.VDate <=' => $end));
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
        // $this->db->where(array('b.salesman_id' => $salesman_id, 'a.VDate >=' => $start, 'a.VDate <=' => $end));
        // $this->db->where('a.IsAppove',1);
        // $this->db->order_by('a.VDate','desc');
        // $query = $this->db->get();
        // if ($query->num_rows() > 0) {
        //     return $query->result_array();
        // }
        // return false;
    }

        public function salesman_list_advance(){
        $this->db->select('*');
        $this->db->from('salesman_information');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

        public function advance_details($transaction_id,$salesman_id){

        $headcode = $this->db->select('HeadCode')->from('acc_coa')->where('salesman_id',$salesman_id)->get()->row();
        return $data  = $this->db->select('*')
                        ->from('acc_transaction')
                        ->where('VNo',$transaction_id)
                        ->where('COAID',$headcode->HeadCode)
                        ->get()
                        ->result_array();

    }

        public function salesman_product_sale_info($salesman_id) {
        $this->db->select('a.*,b.HeadName');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b','a.COAID=b.HeadCode');
        $this->db->where('b.salesman_id',$salesman_id);
        $this->db->where('a.IsAppove',1);
        $this->db->order_by('a.VDate','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

}

