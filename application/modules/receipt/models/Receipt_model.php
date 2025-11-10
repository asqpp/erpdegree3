<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#------------------------------------    
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------    

class Receipt_model extends CI_Model {

    public function customer_search($customer_id) {
        $query = $this->db->select('*')->from('customer_information')->group_start()->like('customer_name', $customer_id)->or_like('customer_mobile', $customer_id)->group_end()->limit(30)->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function customer_list() {
        $query = $this->db->select('*')->from('customer_information')->where('status', '1')->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }
    
    public function supplier_list() {
        $this->db->select('*');
        $this->db->from('supplier_information');
        $this->db->order_by('supplier_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function broker_list() {
        $this->db->select('*');
        $this->db->from('broker_information');
        $this->db->order_by('broker_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function salesman_list() {
        $this->db->select('*');
        $this->db->from('salesman_information');
        $this->db->order_by('salesman_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }
    
    // broker receipt list 
    public function broker_receipt_list($broker_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
        $this->db->where('a.broker_id', $broker_id);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit($perpage, $page);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // broker receipt list count
    public function broker_receipt_list_count($broker_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.broker_id', $broker_id);
        // $this->db->limit('500');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
    
    // broker receipt detail 
    public function broker_receipt_details($receipt_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.broker_pr_id', $receipt_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    
    // broker receipt detail 
    public function broker_receipt_details_product() {
        $data=array();
        for($i=0;$i<count($this->input->post('broker_pr_id'));$i++){
            $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name,b.broker_id');
            $this->db->from('broker_product a');
            $this->db->join('product_information d', 'd.product_id = a.product_id');
            $this->db->join('invoice b', 'b.id = a.invoice_id');
            $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
            $this->db->where('a.broker_pr_id', $this->input->post('broker_pr_id')[$i]);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $data[$i]= $query->row();
            }
        }
        return $data;
    }
    
    // broker receipt detail date
    public function broker_receipt_date_details($invoice_id) {
        $this->db->select('*');
        $this->db->from('acc_vaucher');
        $this->db->where('referenceNo', $invoice_id);
        $this->db->where('Narration',"Sales receipt Voucher for Broker");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // broker receipt update 
    // public function broker_receipt_update($receipt_id) {
    //     $data = array(
    //         'paid_amount' => $this->input->post('paid_amount_new', TRUE),
    //         'due_amount' => $this->input->post('due_amount_new', TRUE),
    //     );
    //     $update = $this->db->where('broker_pr_id', $receipt_id)->update('broker_product', $data);
        
    //     $brokerpr  = $this->db->select('*')->from('broker_product')->where('broker_pr_id', $receipt_id)->get()->row();
        
    //     $is_credit = 1;
    //     $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
    //     // for inventory & cost of goods sold start
    //     $dbtid      = $predefine_account->brokerCode;
    //     $Narration          = "Income Voucher for sales";
    //     $Comment            = "Income Voucher for sales";
    //     $reVID              = $predefine_account->bankCode;
    //     $reNarration          = "Bank / Cash in Hand account";
    //     $reComment            = "Bank / Cash in Hand account";
    
    //     $subcode    = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $broker_id)->where('subTypeId', 7)->get()->row()->id;
        
    //     $fyear = financial_year();
    //     $VDate = date('Y-m-d');
    //     $CreateBy = $this->session->userdata('id');
    //     $createdate = date('Y-m-d H:i:s');
    //     // Cash & credit voucher insert
    //     $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'CV', 'VNo');
    //     $vaucherNo = "CV-" . ($maxid + 1);

    //     $creditinsert = array(
    //         'fyear' => $fyear,
    //         'VNo' => $vaucherNo,
    //         'Vtype' => 'CV',
    //         'referenceNo' => $brokerpr->invoice_id,
    //         'VDate' => $VDate,
    //         'COAID' => $dbtid,
    //         'Narration' => $Narration,
    //         'ledgerComment' => $Comment,
    //         'RevCodde' => $reVID,
    //         'subType' => 7,
    //         'subCode' => $subcode,
    //         'isApproved' => 0,
    //         'CreateBy' => $CreateBy,
    //         'CreateDate' => $createdate,
    //         'status' => 0,
    //         'broker_id' => $brokerpr->broker_id,
    //         'reNarration' => $reNarration,
    //         'reledgerComment' => $reComment,
    //         'resubType' => 0,
    //         'resubCode' => 0,
    //     );

        
    //     $creditinsert['Debit'] = 0.00;
    //     $creditinsert['Credit'] = $this->input->post('paid_amount',TRUE);
        
    //     $id = $this->db->insert('acc_vaucher', $creditinsert);
    //     // for inventory & cost of goods sold end
        
        
    //     $setting_data = $this->db->select("is_autoapprove_v")->from("web_setting")->where("setting_id", 1)->get()->result_array();
    //     if ($setting_data[0]["is_autoapprove_v"] == 1) {
    //         $vouchers = $this->db
    //             ->select("referenceNo, VNo")
    //             ->from("acc_vaucher")
    //             ->where("referenceNo", $brokerpr->invoice_id)
    //             ->where("status", 0)
    //             ->get()
    //             ->result();
    //         foreach ($vouchers as $value) {
    //             $data = $this->approved_vaucher($value->VNo, "active");
    //         }
    //     }
    //     $vaucherdata = $this->db->select("*")->from("acc_vaucher")->where("id", $id)->get()->result();
        
    //     $ApprovedBy = $this->session->userdata("id");
    //     $approvedDate = date("Y-m-d H:i:s");
    //     if ($vaucherdata) {
    //         foreach ($vaucherdata as $row) {
    //             $transationinsert = [ "vid" => $row->id, "fyear" => $row->fyear, "VNo" => $row->VNo, "Vtype" => $row->Vtype, "referenceNo" => $row->referenceNo, "VDate" => $row->VDate, "COAID" => $row->COAID, "Narration" => $row->Narration, "chequeNo" => !empty($row->chequeNo) ? $row->chequeNo : "", "chequeDate" => $row->chequeDate, "isHonour" => $row->isHonour, "ledgerComment" => $row->ledgerComment, "Debit" => $row->Debit, "Credit" => $row->Credit, "StoreID" => 0, "IsPosted" => 1, "RevCodde" => $row->RevCodde, "subType" => $row->subType, "subCode" => $row->subCode, "IsAppove" => 1, "CreateBy" => $ApprovedBy, "CreateDate" => $approvedDate, ];
    //             $instran = $this->db->insert("acc_transaction", $transationinsert);
    //             addActivityLog("approved_vaucher_transation", "create", $this->db->insert_id(), "acc_transaction",     1, $transationinsert);
    //             // update Monthly record
    //             if ($instran) {
    //                 $this->store_transation_summery($row->COAID, $row->VDate);
    //                 $revercetransationinsert = ["vid" => $row->id, "fyear" => $row->fyear, "VNo" => $row->VNo, "Vtype" => $row->Vtype, "referenceNo" => $row->referenceNo, "VDate" => $row->VDate, "COAID" => $row->RevCodde, "Narration" => $row->reNarration, "chequeNo" => !empty($row->chequeNo) ? $row->chequeNo : "", "chequeDate" => $row->chequeDate, "isHonour" => $row->isHonour, "ledgerComment" => $row->reledgerComment, "Debit" => $row->Credit, "Credit" => $row->Debit, "StoreID" => 0, "IsPosted" => 1, "RevCodde" => $row->COAID, "subType" => $row->resubType, "subCode" => $row->resubCode, "IsAppove" => 1, "CreateBy" => $ApprovedBy, "CreateDate" => $approvedDate,     ];
    //                 $this->db->insert( "acc_transaction", $revercetransationinsert );
    //                 addActivityLog( "approved_vaucher_reversetransation", "create", $this->db->insert_id(), "acc_transaction", 1, $revercetransationinsert);
    //                 // update Monthly record
    //                 $this->store_transation_summery($row->RevCodde, $row->VDate);
    //             }
    //         }
    //     }
        
    //     return $update;
    // }
    
    // broker receipt update 
    public function broker_receipt_updates($receipt_id,$paid_amount,$due_amount,$paid_amounto) {
        $data = array(
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
        );
        $update = $this->db->where('broker_pr_id', $receipt_id)->update('broker_product', $data);
        
        $brokerpr  = $this->db->select('*')->from('broker_product')->where('broker_pr_id', $receipt_id)->get()->row();
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $dbtid      = $predefine_account->brokerCode;
        $Narration          = "Referral Fees on Policy No. ";
        $Comment            = "Referral Fees from Broker on Policy No. ";
        
        $reVID              = $this->input->post('cmbDebit',true);
        $reNarration          = "Sales account";
        $reComment            = "Sales account for income";
    
        $subcode    = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $broker_id)->where('subTypeId', 7)->get()->row()->id;
        
        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'CV', 'VNo');
        $vaucherNo = "CV-" . ($maxid + 1);

        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'CV',
            'referenceNo' => $brokerpr->invoice_id,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'chequeno' =>$this->input->post('checkno',true),
            'chequeDate' =>$this->input->post('chequeDate',true),
            'isHonour' => $this->input->post('ishonours',true),
            'RevCodde' => $reVID,
            'subType' => 7,
            'subCode' => $subcode,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'broker_id' => $brokerpr->broker_id,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );

        
        $creditinsert['Debit'] = 0.00;
        $creditinsert['Credit'] = $paid_amounto;
        
        $id = $this->db->insert('acc_vaucher', $creditinsert);
        // for inventory & cost of goods sold end
        
        
        $setting_data = $this->db->select("is_autoapprove_v")->from("web_setting")->where("setting_id", 1)->get()->result_array();
        if ($setting_data[0]["is_autoapprove_v"] == 1) {
            $vouchers = $this->db
                ->select("referenceNo, VNo")
                ->from("acc_vaucher")
                ->where("referenceNo", $brokerpr->invoice_id)
                ->where("status", 0)
                ->get()
                ->result();
            foreach ($vouchers as $value) {
                $data = $this->approved_vaucher($value->VNo, "active");
            }
        }
        $vaucherdata = $this->db->select("*")->from("acc_vaucher")->where("id", $id)->get()->result();
        
        $ApprovedBy = $this->session->userdata("id");
        $approvedDate = date("Y-m-d H:i:s");
        if ($vaucherdata) {
            foreach ($vaucherdata as $row) {
                $transationinsert = [ "vid" => $row->id, "fyear" => $row->fyear, "VNo" => $row->VNo, "Vtype" => $row->Vtype, "referenceNo" => $row->referenceNo, "VDate" => $row->VDate, "COAID" => $row->COAID, "Narration" => $row->Narration, "chequeNo" => !empty($row->chequeNo) ? $row->chequeNo : "", "chequeDate" => $row->chequeDate, "isHonour" => $row->isHonour, "ledgerComment" => $row->ledgerComment, "Debit" => $row->Debit, "Credit" => $row->Credit, "StoreID" => 0, "IsPosted" => 1, "RevCodde" => $row->RevCodde, "subType" => $row->subType, "subCode" => $row->subCode, "IsAppove" => 1, "CreateBy" => $ApprovedBy, "CreateDate" => $approvedDate, ];
                $instran = $this->db->insert("acc_transaction", $transationinsert);
                addActivityLog("approved_vaucher_transation", "create", $this->db->insert_id(), "acc_transaction",     1, $transationinsert);
                // update Monthly record
                if ($instran) {
                    $this->store_transation_summery($row->COAID, $row->VDate);
                    $revercetransationinsert = ["vid" => $row->id, "fyear" => $row->fyear, "VNo" => $row->VNo, "Vtype" => $row->Vtype, "referenceNo" => $row->referenceNo, "VDate" => $row->VDate, "COAID" => $row->RevCodde, "Narration" => $row->reNarration, "chequeNo" => !empty($row->chequeNo) ? $row->chequeNo : "", "chequeDate" => $row->chequeDate, "isHonour" => $row->isHonour, "ledgerComment" => $row->reledgerComment, "Debit" => $row->Credit, "Credit" => $row->Debit, "StoreID" => 0, "IsPosted" => 1, "RevCodde" => $row->COAID, "subType" => $row->resubType, "subCode" => $row->resubCode, "IsAppove" => 1, "CreateBy" => $ApprovedBy, "CreateDate" => $approvedDate,     ];
                    $this->db->insert( "acc_transaction", $revercetransationinsert );
                    addActivityLog( "approved_vaucher_reversetransation", "create", $this->db->insert_id(), "acc_transaction", 1, $revercetransationinsert);
                    // update Monthly record
                    $this->store_transation_summery($row->RevCodde, $row->VDate);
                }
            }
        }
        
        return $update;
    }
    
    public function getMaxFieldNumber($field, $table, $where = null, $type = null, $fild2 = null) {

        $this->db->select("$field,$fild2");
        $this->db->from($table);
        if ($where != null)
        {
            $this->db->where($where, $type);
        }
        $this->db->order_by('id', 'desc')->limit(1);
        $record = $this->db->get();
        if ($record->num_rows() > 0)
        {
            if ($fild2 != null)
            {
                $num = $record->row($fild2);
                list($txt, $intval) = explode('-', $num);
                return $intval;
            }
            else
            {
                $num = $record->row($field);
                return $num;
            }
        }
        else
        {
            return 0;
        }
    }
    
    // Approved Vaucher
    public function approved_vaucher($id, $action) {
        $vaucherdata = $this->db->select("*")->from("acc_vaucher")->where("VNo", $id)->get()->result();
        $ApprovedBy = $this->session->userdata("id");
        $approvedDate = date("Y-m-d H:i:s");
        if ($vaucherdata) {
            foreach ($vaucherdata as $row) {
                $transationinsert = [ "vid" => $row->id, "fyear" => $row->fyear, "VNo" => $row->VNo, "Vtype" => $row->Vtype, "referenceNo" => $row->referenceNo, "VDate" => $row->VDate, "COAID" => $row->COAID, "Narration" => $row->Narration, "chequeNo" => !empty($row->chequeNo) ? $row->chequeNo : "", "chequeDate" => $row->chequeDate, "isHonour" => $row->isHonour, "ledgerComment" => $row->ledgerComment, "Debit" => $row->Debit, "Credit" => $row->Credit, "StoreID" => 0, "IsPosted" => 1, "RevCodde" => $row->RevCodde, "subType" => $row->subType, "subCode" => $row->subCode, "IsAppove" => 1, "CreateBy" => $ApprovedBy, "CreateDate" => $approvedDate, ];
                $instran = $this->db->insert("acc_transaction", $transationinsert);
                addActivityLog("approved_vaucher_transation", "create", $this->db->insert_id(), "acc_transaction",     1, $transationinsert);
                // update Monthly record
                if ($instran) {
                    $this->store_transation_summery($row->COAID, $row->VDate);
                    $revercetransationinsert = ["vid" => $row->id, "fyear" => $row->fyear, "VNo" => $row->VNo, "Vtype" => $row->Vtype, "referenceNo" => $row->referenceNo, "VDate" => $row->VDate, "COAID" => $row->RevCodde, "Narration" => $row->reNarration, "chequeNo" => !empty($row->chequeNo) ? $row->chequeNo : "", "chequeDate" => $row->chequeDate, "isHonour" => $row->isHonour, "ledgerComment" => $row->reledgerComment, "Debit" => $row->Credit, "Credit" => $row->Debit, "StoreID" => 0, "IsPosted" => 1, "RevCodde" => $row->COAID, "subType" => $row->resubType, "subCode" => $row->resubCode, "IsAppove" => 1, "CreateBy" => $ApprovedBy, "CreateDate" => $approvedDate,     ];
                    $this->db->insert( "acc_transaction", $revercetransationinsert );
                    addActivityLog( "approved_vaucher_reversetransation", "create", $this->db->insert_id(), "acc_transaction", 1, $revercetransationinsert);
                    // update Monthly record
                    $this->store_transation_summery($row->RevCodde, $row->VDate
                    );
                }
            }
        }
        $action = $action == "active" ? 1 : 0;
        $upData = [
            "VNo" => $id, "isApproved" => $action, "approvedBy" => $ApprovedBy, "approvedDate" => $approvedDate, "status" => $action, ];
        return $this->db->where("VNo", $id)->update("acc_vaucher", $upData);
    }
    
    public function store_transation_summery($coaid, $date) {     
        $curentmonth = date('n',  strtotime($date));
        $fyear = financial_year();
        $summery =  $this->get_clossing_balance($coaid,$date);
        $oldrecord = $this->get_monthly_balance($coaid,$fyear);   
        $data=array(
            'fyear' =>  $fyear,
            'COAID' =>  $coaid,
            'balance'.$curentmonth =>  $summery,       
            'updatedDate' =>  date('Y-m-d h:i:s')        
         );
    
        if(!$oldrecord) {
            $up = $this->db->insert('acc_monthly_balance',$data);
        } else {
             $this->db->where('COAID',$coaid);
             $this->db->where('fyear',$fyear); 
             $up = $this->db->update('acc_monthly_balance',$data);
        }
        if($up) {
            return true;
        } else {
            return false;
        }
    }

    public function get_clossing_balance($hCode,$dtpFromDate,$dtpToDate=null,$subtype=1,$subcode=null,$hType=null) {
           if($dtpToDate!=null) {
            $toDate = $dtpToDate;
           } else {
            $toDate = $dtpFromDate;
           }
           $coaHead = $this->general_led_report_headname($hCode);
           $opening = $this->get_opening_balance($hCode,$dtpFromDate,$toDate);
           $current =  $this->get_general_ledger_report($hCode,$toDate,$toDate,0,0); 
              if($current) {
                 foreach($current as $cur) {
                     if($coaHead->HeadType == 'A' || $coaHead->HeadType == 'E') {
                         $balance= ($cur->debit - $cur->credit);
                      } else {
                         $balance=($cur->credit - $cur->debit);
                      }
                 }
                 
              } else {
                $balance= 0;
              }            
          return  $closingbalance = $opening +  $balance;

    }

    public function general_led_report_headname($cmbGLCode){
        $this->db->select('*');
        $this->db->from('acc_coa');
        $this->db->where('HeadCode',$cmbGLCode);
        $query = $this->db->get()->row();
        return $query;
    }
    
    //Get Opening balance
    public function get_opening_balance($hCode,$dtpFromDate,$dtpToDate){  
        $coaHead = $this->general_led_report_headname($hCode); 

            $fyearStartDate = $this->session->userdata('fyearStartDate');
            $fyearEndDate = $this->session->userdata('fyearEndDate');
            $oldDate = date('Y-m-d',strtotime($dtpFromDate. ' -1 year'));
            $prevDate = date('Y-m-d', strtotime($dtpFromDate .' - 1day'));
            $oldBalance = 0;
      
        if($coaHead->HeadType == 'L' || $coaHead->HeadType =='A') {                 
            if($dtpFromDate >= $fyearStartDate && $dtpFromDate <= $fyearEndDate) { 
                $fyear = $this->db->select('*')->from('financial_year')->where('startDate <=',$oldDate)->where('endDate >=',$oldDate)->get()->row();
               
            } else {                   
                $fyear = $this->db->select('*')->from('financial_year')->where('startDate <=',$oldDate)->where('endDate >=',$oldDate)->get()->row();
            } 
            if ($fyear) {
                
                $oldBalance = $this->get_old_year_closingBalance($hCode,$fyear->id,$coaHead->HeadType,$coaHead->subType);
            }
        } else {
            $oldBalance =0;
       } 

      $opening =  $this->get_general_ledger_report($hCode,$fyearStartDate,$prevDate,0,0);
      if($opening) {
         foreach($opening as $open) {
             if($coaHead->HeadType == 'A' || $coaHead->HeadType == 'E') {
                 $balance= ($open->debit - $open->credit);
              } else {
                 $balance=($open->credit - $open->debit);
              }
         }
         
      } else {
        $balance= 0;
      }             

        return $newBalance = $oldBalance + $balance ; 
                         
    }
    
    //Get old year clossing balance
    public function get_old_year_closingBalance($hCode,$year,$hType=null,$subtype=1,$subcode= null) {      
       $this->db->select('*');
       $this->db->from('acc_opening_balance');
       if($subtype != 1) {
          $this->db->where('subCode',$subcode);
          $this->db->where('subType',$subtype);
       } 
        $this->db->where('COAID',$hCode);      
       
       $this->db->where('fyear',$year);            
      $closing =  $this->db->get();
      if($closing->num_rows() > 0) {
        $closingvalue = $closing->row();
        if($hType == 'A') {
           return ($closingvalue->Debit -  $closingvalue->Credit);
        } else {
          return ($closingvalue->Credit -  $closingvalue->Debit);
        }        
      }
      return false;
    }

    // get general ledger report
    public function get_general_ledger_report($cmbCode,$dtpFromDate,$dtpToDate, $chkIsTransction, $isfyear=0,$subtype=1, $subcod=null){
         if($chkIsTransction == 1) {
            $this->db->select('acc_transaction.VNo,acc_transaction.COAID, acc_transaction.Vtype, acc_transaction.VDate, acc_transaction.Narration, acc_transaction.ledgerComment, acc_transaction.Debit, acc_transaction.Credit, acc_transaction.IsAppove, acc_transaction.COAID,acc_coa.HeadName, acc_coa.PHeadName,acc_coa.pheadcode, acc_coa.HeadType');
            $this->db->from('acc_transaction');
            $this->db->join('acc_coa','acc_transaction.RevCodde = acc_coa.HeadCode', 'left');                
            if($subtype!=1 && $subcod != null ) {
             $this->db->join('acc_subtype st','acc_transaction.subType = st.id', 'left');
             $this->db->join('acc_subcode sc','acc_transaction.subCode = sc.id', 'left');
             $this->db->where('acc_transaction.subType',$subtype);
             $this->db->where('acc_transaction.subCode',$subcod);
            } 
            $this->db->where('acc_transaction.COAID',$cmbCode);                
            $this->db->where('acc_transaction.IsAppove',1);
            $this->db->where('VDate BETWEEN "'.$dtpFromDate. '" and "'.$dtpToDate.'"');               
            if($isfyear!=0) {
              $this->db->where('acc_transaction.fyear',$this->session->userdata('fyear')); 
            }                
            $this->db->order_by('acc_transaction.VDate','Asc');
            $this->db->order_by('acc_transaction.Vtype','Asc');
            $query = $this->db->get();
            return $query->result();
    
         } else {
            $this->db->select('COAID, Vtype, sum(Debit) as debit, sum(Credit) as credit ');
            $this->db->from('acc_transaction');               
            $this->db->where('IsAppove',1);
            $this->db->where('VDate BETWEEN "'.$dtpFromDate. '" and "'.$dtpToDate.'"');
            $this->db->where('COAID',$cmbCode);
            if($isfyear!=0) {
              $this->db->where('acc_transaction.fyear',$this->session->userdata('fyear')); 
            }
            $query = $this->db->get();         
            return $query->result();
         }       
    }
    
    public function get_monthly_balance($coaid, $fyear) {
           $this->db->select('id');
           $this->db->from('acc_monthly_balance');
           $this->db->where('COAID',$coaid);
           $this->db->where('fyear',$fyear); 
           $res =  $this->db->get();
           if($res->num_rows() > 0) {
              return $res->row();
           } else {
            return false;
           }

    }

    // Accounts list
    public function getTransationHead() {
      return  $data = $this->db->select("*")
            ->from('acc_coa')
            ->where('isBankNature', 0)  
            ->where('isCashNature', 0)  
            ->where('HeadLevel', 4)  
            ->where('IsActive', 1) 
            ->order_by('HeadName')
            ->get()
            ->result();
    }

    //Generate Voucher No
    public function voNO() {
      return  $data = $this->db->select("VNo as voucher")
            ->from('acc_transaction') 
            ->like('VNo', 'DV-', 'after')
            ->order_by('ID','desc')
            ->get()
            ->row();
           
    }

    // Accounts list
    public function getCashbankHead( ) {
      return  $data = $this->db->select("*")
            ->from('acc_coa')            
            ->where('HeadLevel', 4)  
            ->where('IsActive', 1) 
            ->where('isBankNature', 1)  
            ->or_where('isCashNature', 1)  
            ->order_by('HeadName')
            ->get()
            ->result();
    }
    
}