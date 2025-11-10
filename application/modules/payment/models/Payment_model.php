<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#------------------------------------    
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------    

class Payment_model extends CI_Model
{

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
    
    // salesman commission list 
    public function salesman_commission_list($salesman_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name');
        $this->db->from('salesman_product a');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
        $this->db->where('a.salesman_id', $salesman_id);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit($perpage, $page);
        $this->db->order_by('a.invoice_id');
        
        $query = $this->db->get();
        // $query->result_array();
        // echo $this->db->last_query();exit;
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // salesman commission list count
    public function salesman_commission_list_count($salesman_id) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name');
        $this->db->from('salesman_product a');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
        $this->db->where('a.salesman_id', $salesman_id);
        $this->db->where('a.due_amount >', 0);
        // $this->db->limit('500');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
    
    // salesman commission detail 
    public function salesman_commission_details($commission_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('salesman_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.salesman_pr_id', $commission_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    
    // salesman commission detail date
    public function salesman_commission_date_details($invoice_id) {
        $this->db->select('*');
        $this->db->from('acc_vaucher');
        $this->db->where('referenceNo', $invoice_id);
        $this->db->where('Narration',"Sales commission Voucher for Salesman");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // salesman commission update 
    public function salesman_commission_update($commission_id) {
            
        $salesmanpr  = $this->db->select('*')->from('salesman_product')->where('salesman_pr_id', $commission_id)->get()->row();
        
        $data = array(
            'paid_amount' => $this->input->post('paid_amount_new', TRUE),
            'due_amount' => $this->input->post('due_amount_new', TRUE),
        );
        $update = $this->db->where('salesman_pr_id', $commission_id)->update('salesman_product', $data);
        
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
    
        
        // for inventory & cost of goods sold start
        $dbtid   = '40103';
        $Narration  = "Paid Sales commission for Salesman";
        $Comment = "Paid Sales commission Voucher for Salesman";
        $reVID   = $predefine_account->bankCode;
        $reNarration  = "Bank/ Cash in Hand account";
        $reComment = "Bank/ Cash in Hand account";
    
        $amnt_type  = 'Debit';
        $subcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $salesmanpr->salesman_id)->where('subTypeId', 5)->get()->row()->id;
        
        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'JV', 'VNo');
        $vaucherNo = "JV-" . ($maxid + 1);

        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'JV',
            'referenceNo' => $salesmanpr->invoice_id,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'RevCodde' => $reVID,
            'subType' => 5,
            'subCode' => $subcode,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'salesman_id' => $salesmanpr->salesman_id,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );

        
        $creditinsert['Debit'] = $this->input->post('paid_amount',TRUE);
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        return $update;
    }
    
    // salesman_commission clear dues
    public function salesman_commission_clear() {
        for($i=0;$i<count($this->input->post('salesman_pr_id'));$i++){
            
            $salesmanpr  = $this->db->select('*')->from('salesman_product')->where('salesman_pr_id', $this->input->post('salesman_pr_id')[$i])->get()->row();
            // var_dump($salesmanpr);exit;
            $salesman_id = $salesmanpr->salesman_id;
            $invoice_id = $salesmanpr->invoice_id;
            $paid = $salesmanpr->paid_amount;
            $due = $salesmanpr->due_amount;
            $price = $salesmanpr->salesman_price;
            $amnt = $salesmanpr->due_amount;
            
            $data = array(
                'paid_amount' => $price,
                'due_amount' => 0.00
            );
            $update = $this->db->where('salesman_pr_id', $this->input->post('salesman_pr_id')[$i])->update('salesman_product', $data);
            
            $is_credit = 1;
            $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
            
            // for inventory & cost of goods sold start
            $dbtid   = '40103';
            $Narration  = "Paid Sales commission for Salesman";
            $Comment = "Paid Sales commission Voucher for Salesman";
            $reVID   = $predefine_account->bankCode;
            $reNarration  = "Bank/ Cash in Hand account";
            $reComment = "Bank/ Cash in Hand account";
            
    
            $amnt_type  = 'Debit';
            $subcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $salesmanpr->salesman_id)->where('subTypeId', 5)->get()->row()->id;
            
            $fyear = financial_year();
            $VDate = date('Y-m-d');
            $CreateBy = $this->session->userdata('id');
            $createdate = date('Y-m-d H:i:s');
            // Cash & credit voucher insert
            $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'JV', 'VNo');
            $vaucherNo = "JV-" . ($maxid + 1);
    
            $creditinsert = array(
                'fyear' => $fyear,
                'VNo' => $vaucherNo,
                'Vtype' => 'JV',
                'referenceNo' => $invoice_id,
                'VDate' => $VDate,
                'COAID' => $dbtid,
                'Narration' => $Narration,
                'ledgerComment' => $Comment,
                'RevCodde' => $reVID,
                'subType' => 5,
                'subCode' => $subcode,
                'isApproved' => 0,
                'CreateBy' => $CreateBy,
                'CreateDate' => $createdate,
                'status' => 0,
                'salesman_id' => $salesman_id,
                'reNarration' => $reNarration,
                'reledgerComment' => $reComment,
                'resubType' => 0,
                'resubCode' => 0,
            );
        
            $creditinsert['Debit'] = $due;
            $creditinsert['Credit'] = 0.00;
            
            $id = $this->db->insert('acc_vaucher', $creditinsert);
            // for inventory & cost of goods sold end
            
            
        
        
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
                        $this->store_transation_summery($row->RevCodde, $row->VDate
                        );
                    }
                }
            }
        }
        return $update;
    }
    
    // supplier payment list 
    public function supplier_payment_list($supplier_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name');
        $this->db->from('supplier_product a');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
        $this->db->where('a.supplier_id', $supplier_id);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit($perpage, $page);
        $this->db->order_by('a.invoice_id');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // supplier payment list count
    public function supplier_payment_list_count($supplier_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('supplier_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.supplier_id', $supplier_id);
        $this->db->limit('500');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
    
    // supplier payment detail 
    public function supplier_payment_details($payment_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('supplier_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.supplier_pr_id', $payment_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    
    // supplier payment detail date
    public function supplier_payment_date_details($invoice_id) {
        $this->db->select('*');
        $this->db->from('acc_vaucher');
        $this->db->where('referenceNo', $invoice_id);
        $this->db->like('Narration',"Sales payment Voucher for Supplier");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // supplier payment update 
    public function supplier_payment_update($payment_id) {
        $data = array(
            'paid_amount' => $this->input->post('paid_amount_new', TRUE),
            'due_amount' => $this->input->post('due_amount_new', TRUE),
        );
        $update = $this->db->where('supplier_pr_id', $payment_id)->update('supplier_product', $data);
        
        if($this->input->post('due_amount_new')<=0.00){
            $dataas['premium_paid_date'] = date('Y-m-d H:i:s');
            $this->db->where('id', $this->input->post('invoice_id'))->update('invoice', $dataas);
        }
        
            
        $supplierpr  = $this->db->select('*')->from('supplier_product')->where('supplier_pr_id', $payment_id)->get()->row();
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $dbtid  = $predefine_account->supplierCode;
        $Narration = "Sales cost for Supplier";
        $Comment   = "Sales cost Voucher for Supplier";
        $reVID  = $predefine_account->customerCode;
        $reNarration = "Sales cost of policy";
        $reComment   = "Sales cost of policy Voucher for customer";
        
        $subcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplierpr->supplier_id)->where('subTypeId', 4)->get()->row()->id;
        $resubcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplierpr->customer_id)->where('subTypeId', 3)->get()->row()->id;
        
        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $vaucherNo = "DV-" . ($maxid + 1);

        $debitinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'DV',
            'referenceNo' => $this->input->post('invoice_id',TRUE),
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'RevCodde' => $reVID,
            'subType' => 4,
            'subCode' => $subcode,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'supplier_id' => $supplierpr->supplier_id,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 3,
            'resubCode' => $resubcode,
        );

        
        $debitinsert['Debit'] = $this->input->post('paid_amount',TRUE);
        $debitinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $debitinsert);
        
        
        
        
        $setting_data = $this->db->select("is_autoapprove_v")->from("web_setting")->where("setting_id", 1)->get()->result_array();
        if ($setting_data[0]["is_autoapprove_v"] == 1) {
            $vouchers = $this->db
                ->select("referenceNo, VNo")
                ->from("acc_vaucher")
                ->where("referenceNo", $this->input->post('invoice_id',TRUE))
                ->where("status", 0)
                ->get()
                ->result();
            foreach ($vouchers as $value) {
                $data = $this->approved_vaucher($value->VNo, "active");
            }
        }
        // for inventory & cost of goods sold end
        
        return $update;
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
    // supplier receipts clear dues
    public function supplier_payment_clear() {
        for($i=0;$i<count($this->input->post('supplier_pr_id'));$i++){
            
            $supplierpr  = $this->db->select('*')->from('supplier_product')->where('supplier_pr_id', $this->input->post('supplier_pr_id')[$i])->get()->row();
            // var_dump($supplierpr);exit;
            $supplier_id = $supplierpr->supplier_id;
            $invoice_id = $supplierpr->invoice_id;
            $paid = $supplierpr->paid_amount;
            $due = $supplierpr->due_amount;
            $price = $supplierpr->supplier_price;
            $amnt = $supplierpr->due_amount;
            
            $data = array(
                'paid_amount' => $price,
                'due_amount' => 0.00
            );
            $update = $this->db->where('supplier_pr_id', $this->input->post('supplier_pr_id')[$i])->update('supplier_product', $data);
            
            $is_credit = 1;
            $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
            
            $dbtid  = $predefine_account->supplierCode;
            $Narration = "Sales cost for Supplier";
            $Comment   = "Sales cost Voucher for Supplier";
            $reVID  = $predefine_account->customerCode;
            $reNarration = "Sales cost of policy";
            $reComment   = "Sales cost of policy Voucher for customer";
            
            $subcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplierpr->supplier_id)->where('subTypeId', 4)->get()->row()->id;
            $resubcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplierpr->customer_id)->where('subTypeId', 3)->get()->row()->id;
        
            $fyear = financial_year();
            $VDate = date('Y-m-d');
            $CreateBy = $this->session->userdata('id');
            $createdate = date('Y-m-d H:i:s');
            // Cash & credit voucher insert
            $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
            $vaucherNo = "DV-" . ($maxid + 1);
    
            $creditinsert = array(
                'fyear' => $fyear,
                'VNo' => $vaucherNo,
                'Vtype' => 'DV',
                'referenceNo' => $invoice_id,
                'VDate' => $VDate,
                'COAID' => $dbtid,
                'Narration' => $Narration,
                'ledgerComment' => $Comment,
                'RevCodde' => $reVID,
                'subType' => 4,
                'subCode' => $subcode,
                'isApproved' => 0,
                'CreateBy' => $CreateBy,
                'CreateDate' => $createdate,
                'status' => 0,
                'supplier_id' => $supplierpr->supplier_id,
                'reNarration' => $reNarration,
                'reledgerComment' => $reComment,
                'resubType' => 3,
                'resubCode' => $resubcode,
            );
        
            $creditinsert['Debit'] = $due;
            $creditinsert['Credit'] = 0.00;
            
            $this->db->insert('acc_vaucher', $creditinsert);
            // for inventory & cost of goods sold end
        }
        return $update;
    }

    // customer payment list 
    public function customer_payment_list($customer_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id,b.document_date,b.policy_no,b.customer_id,c.customer_name');
        $this->db->from('customer_product a');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('customer_information c', 'c.customer_id = b.customer_id');
        $this->db->where('a.customer_id', $customer_id);
        $this->db->limit($perpage, $page);
        $this->db->order_by('a.invoice_id');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // customer payment list count
    public function customer_payment_list_count($customer_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('customer_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.customer_id', $customer_id);
        $this->db->limit('500');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
    
    // customer payment detail 
    public function customer_payment_details($payment_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('customer_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.customer_pr_id', $payment_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    
    // customer payment detail date
    public function customer_payment_date_details($invoice_id) {
        $this->db->select('*');
        $this->db->from('acc_vaucher');
        $this->db->where('referenceNo', $invoice_id);
        $this->db->like('Narration',"Sales payment Voucher for Supplier");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // customer payment update 
    public function customer_payment_update($payment_id) {
        $data = array(
            'paid_amount' => $this->input->post('paid_amount_new', TRUE),
            'due_amount' => $this->input->post('due_amount_new', TRUE),
        );
        $update = $this->db->where('customer_pr_id', $payment_id)->update('customer_product', $data);
        
        if($this->input->post('due_amount_new')<=0.00){
            $dataas['premium_paid_date'] = date('Y-m-d H:i:s');
            $this->db->where('id', $this->input->post('invoice_id'))->update('invoice', $dataas);
        }
        
        $supplierpr  = $this->db->select('*')->from('customer_product')->where('customer_pr_id', $payment_id)->get()->row();
            
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $dbtid   = $predefine_account->customerCode;
        $Narration  = "Premium payment";
        $Comment = "Premium payment of customer";
        $reVID   = $predefine_account->supplierCode;
        $reNarration = "Sales cost for Supplier";
        $reComment   = "Sales cost Voucher for Supplier"; 

        $amnt_type  = 'Credit';
        $subcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplierpr->customer_id)->where('subTypeId', 3)->get()->row()->id;
        $resubcode = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplierpr->supplier_id)->where('subTypeId', 4)->get()->row()->id;
        
        
        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $vaucherNo = "JV-" . ($maxid + 1);

        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'JV',
            'referenceNo' => $supplierpr->invoice_id,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'RevCodde' => $reVID,
            'subType' => 3,
            'subCode' => $subcode,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'customer_id' => $supplierpr->customer_id,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 4,
            'resubCode' => $resubcode,
        );

        
        $creditinsert['Credit'] = $this->input->post('paid_amount',TRUE);
        $creditinsert['Debit'] = 0.00;
        
        $id = $this->db->insert('acc_vaucher', $creditinsert);
        // for inventory & cost of goods sold end
        
        
        
        
        
        
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
                    $this->store_transation_summery($row->RevCodde, $row->VDate
                    );
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
    
}