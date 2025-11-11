<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#------------------------------------    
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------    

class Parseinvoice_model extends CI_Model {

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
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id');
        $this->db->from('salesman_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->where('a.salesman_id', $salesman_id);
        $this->db->limit($perpage, $page);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // salesman commission list count
    public function salesman_commission_list_count($salesman_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('salesman_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.salesman_id', $salesman_id);
        $this->db->limit('500');
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
        $data = array(
            'paid_amount' => $this->input->post('paid_amount_new', TRUE),
            'due_amount' => $this->input->post('due_amount_new', TRUE),
        );
        $update = $this->db->where('salesman_pr_id', $commission_id)->update('salesman_product', $data);
        
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $Narration          = "Sales commission Voucher for Salesman";
        $Comment            = "Sales commission Voucher for Salesman";
        $reVID              = $predefine_account->salesCode;
        
        $amnt = $this->input->post('paid_amount',TRUE);
        $amnt_type  = 'Debit';
        $dbtid      = $predefine_account->customerCode;
        $subcode    = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
        
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
            'referenceNo' => $this->input->post('invoice_id',TRUE),
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
            'salesman_id' => $this->input->post('salesman_id',TRUE),
        );

        
        $creditinsert['Debit'] = $amnt;
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        // for inventory & cost of goods sold end
        
        return $update;
    }
    
    // broker commission list 
    public function broker_commission_list($broker_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->where('a.broker_id', $broker_id);
        $this->db->limit($perpage, $page);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // broker commission list count
    public function broker_commission_list_count($broker_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.broker_id', $broker_id);
        $this->db->limit('500');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }
    
    // broker commission detail 
    public function broker_commission_details($commission_id) {
        $this->db->select('a.*,d.product_id,d.product_name');
        $this->db->from('broker_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->where('a.broker_pr_id', $commission_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    
    // broker commission detail date
    public function broker_commission_date_details($invoice_id) {
        $this->db->select('*');
        $this->db->from('acc_vaucher');
        $this->db->where('referenceNo', $invoice_id);
        $this->db->where('Narration',"Sales commission Voucher for Broker");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    
    // broker commission update 
    public function broker_commission_update($commission_id) {
        $data = array(
            'paid_amount' => $this->input->post('paid_amount_new', TRUE),
            'due_amount' => $this->input->post('due_amount_new', TRUE),
        );
        $update = $this->db->where('broker_pr_id', $commission_id)->update('broker_product', $data);
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $Narration          = "Sales commission Voucher for Broker";
        $Comment            = "Sales commission Voucher for Broker";
        $reVID              = $predefine_account->salesCode;
        
        $amnt = $this->input->post('paid_amount',TRUE);
        $amnt_type  = 'Debit';
        $dbtid      = $predefine_account->customerCode;
        $subcode    = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
        
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
            'referenceNo' => $this->input->post('invoice_id',TRUE),
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
            'broker_id' => $this->input->post('broker_id',TRUE),
        );

        
        $creditinsert['Debit'] = $amnt;
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        // for inventory & cost of goods sold end
        
        return $update;
    }

    // supplier payment list 
    public function supplier_payment_list($supplier_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id');
        $this->db->from('supplier_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->where('a.supplier_id', $supplier_id);
        $this->db->limit($perpage, $page);
        
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
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $Narration          = "Sales payment Voucher for Supplier";
        $Comment            = "Sales payment Voucher for Supplier";
        $reVID              = $predefine_account->salesCode;
        
        $amnt = $this->input->post('paid_amount',TRUE);
        $amnt_type  = 'Debit';
        $dbtid      = $predefine_account->customerCode;
        $subcode    = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
        
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
            'referenceNo' => $this->input->post('invoice_id',TRUE),
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
            'supplier_id' => $this->input->post('supplier_id',TRUE),
        );

        
        $creditinsert['Debit'] = $amnt;
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        // for inventory & cost of goods sold end
        
        return $update;
    }

    // customer payment list 
    public function customer_payment_list($customer_id,$perpage, $page) {
        $this->db->select('a.*,d.product_id,d.product_name,b.inv_id');
        $this->db->from('customer_product a');
        $this->db->join('product_information d', 'd.product_id = a.product_id');
        $this->db->join('invoice b', 'b.id = a.invoice_id');
        $this->db->where('a.customer_id', $customer_id);
        $this->db->limit($perpage, $page);
        
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
        
        $is_credit = 1;
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        // for inventory & cost of goods sold start
        $Narration          = "Sales payment Voucher for Supplier";
        $Comment            = "Sales payment Voucher for Supplier";
        $reVID              = $predefine_account->salesCode;
        
        $amnt = $this->input->post('paid_amount',TRUE);
        $amnt_type  = 'Debit';
        $dbtid      = $predefine_account->customerCode;
        $subcode    = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
        
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
            'referenceNo' => $this->input->post('invoice_id',TRUE),
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
            'customer_id' => $this->input->post('customer_id',TRUE),
        );

        
        $creditinsert['Debit'] = $amnt;
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        // for inventory & cost of goods sold end
        
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