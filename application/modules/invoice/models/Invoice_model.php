<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------
class Invoice_model extends CI_Model
{

    public function customer_list() {
        $query = $this->db->select('*')->from('customer_information')->where('status', '1')->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function tax_fileds() {
        return $taxfield = $this->db->select('tax_name,default_value')->from('tax_settings')->get()->result_array();
    }

    public function pos_customer_setup() {
        $query = $this->db->select('*')->from('customer_information')->where('customer_name', 'Walking Customer')->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function allproduct() {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->order_by('product_name', 'asc');
        $this->db->limit(30);
        $query = $this->db->get();
        $itemlist = $query->result();
        return $itemlist;
    }

    public function vat_tax_setting() {
        $this->db->select('*');
        $this->db->from('vat_tax_setting');
        $query = $this->db->get();
        return $query->row();
    }

    public function todays_invoice() {
        $this->db->select('a.*,b.customer_name');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->where('a.date', date('Y-m-d'));
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function customer_dropdown() {
        $data = $this->db->select("*")->from('customer_information')->get()->result();

        $list[''] = 'Select Customer';
        if (!empty($data))
        {
            foreach ($data as $value) $list[$value->customer_id] = $value->customer_name;
            return $list;
        }
        else
        {
            return false;
        }
    }

    public function customer_search($customer_id) {
        $query = $this->db->select('*')->from('customer_information')->group_start()->like('customer_name', $customer_id)->or_like('customer_mobile', $customer_id)->group_end()->limit(30)->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function count_invoice() {
        return $this->db->count_all("invoice");
    }

    public function getInvoiceList($postData = null) {
        $response = array();
        $usertype = $this->session->userdata('user_type');
        $fromdate = $this->input->post('fromdate', true);
        $todate = $this->input->post('todate', true);
        if (!empty($fromdate))
        {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        }
        else
        {
            $datbetween = "";
        }
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        ## Search
        $searchQuery = "";
        if ($searchValue != '')
        {
            $searchQuery = " (b.customer_name like '%" . $searchValue . "%' or a.invoice like '%" . $searchValue . "%' or a.date like'%" . $searchValue . "%' or a.inv_id like'%" . $searchValue . "%' or u.first_name like'%" . $searchValue . "%'or u.last_name like'%" . $searchValue . "%')";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = a.sales_by', 'left');
        if ($usertype == 2)
        {
            $this->db->where('a.sales_by', $this->session->userdata('user_id'));
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = a.sales_by', 'left');
        if ($usertype == 2)
        {
            $this->db->where('a.sales_by', $this->session->userdata('user_id'));
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.customer_name,u.first_name,u.last_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = a.sales_by', 'left');
        if ($usertype == 2)
        {
            $this->db->where('a.sales_by', $this->session->userdata('user_id'));
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);

        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        // var_dump($records);exit;
        $data = array();
        $sl = 1;

        foreach ($records as $record)
        {
            // echo $record->inv_id;exit;
            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";

            $button .= '  <a href="' . $base_url . 'invoice_details/' . $record->inv_id . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('invoice') . '"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';

            $button .= '  <a href="' . $base_url . 'invoice_pad_print/' . $record->inv_id . '" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('pad_print') . '"><i class="fa fa-fax" aria-hidden="true"></i></a>';

            $button .= '  <a href="' . $base_url . 'pos_print/' . $record->inv_id . '" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('pos_invoice') . '"><i class="fa fa-fax" aria-hidden="true"></i></a>';
            
            $button .= ' <a href="' . $base_url . 'invoice_edit/' . $record->inv_id . '" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                    
            // if ($this->permission1->method('manage_invoice', 'update')->access())
            // {
            //     $approve = $this->db->select('status,referenceNo')->from('acc_vaucher')->where('referenceNo', $record->inv_id)->where('status', 1)->get()->num_rows();
            //     if ($approve == 0)
            //     {
            //         // if ($record->ret_adjust_amnt == '')
            //         // {

            //             $button .= ' <a href="' . $base_url . 'invoice_edit/' . $record->inv_id . '" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
            //         // }
            //     }
            // }

            $details = '  <a href="' . $base_url . 'invoice_details/' . $record->inv_id . '" class="" >' . $record->invoice . '</a>';
            $invoice_type ='';
            if($record->invoice_type == 1) {
                $invoice_type = 'New';
            }else if($record->invoice_type == 2) {
                $invoice_type = 'Renewal';
            }else if($record->invoice_type == 3) {
                $invoice_type = 'Endorsement';
            }else if($record->invoice_type == 4) {
                $invoice_type = 'Incentive';
            }
                
            $data[] = array(
                'sl' => $sl,
                'invoice' => $details,
                'salesman' => $record->first_name . ' ' . $record->last_name,
                'customer_name' => $record->customer_name,
                'invoice_type' => $invoice_type,
                'final_date' => date("d-M-Y", strtotime($record->date)),
                'total_amount' => $record->total_amount,
                'button' => $button,

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

    public function invoice_taxinfo($invoice_id) {
        return $this->db->select('*')->from('tax_collection')->where('relation_id', $invoice_id)->get()->result_array();
    }

    public function retrieve_invoice_editdata($invoice_id) {
        $this->db->select('a.*,a.id as dbinv_id, a.total_tax as taxs,a. prevous_due,b.customer_name,c.*,c.product_id,d.product_name,d.product_model');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('product_information d', 'd.product_id = c.product_id');
        $this->db->where('a.inv_id', $invoice_id);
        $this->db->group_by('d.product_id');

        $query = $this->db->get();
        $result=$query->result_array();
        // var_dump($result);
        // var_dump($this->db->last_query());exit;
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function pmethod_dropdown() {

        $data = $this->db->select('HeadName, HeadCode')->from('acc_coa')->where('PHeadName', 'Cash')->or_where('PHeadName', 'Cash at Bank')->get()->result();

        $list[''] = 'Select Method';
        if (!empty($data))
        {
            $list[0] = 'Credit Sale';
            foreach ($data as $value) $list[$value->HeadCode] = $value->HeadName;
            return $list;
        }
        else
        {
            return false;
        }
    }
    
    public function pmethod_dropdown_new() {
        $data = $this->db->select('*')->from('acc_coa')->where('PHeadName', 'Cash')->or_where('PHeadName', 'Cash at Bank')->get()->result();

        $list[''] = 'Select Method';
        if (!empty($data))
        {

            foreach ($data as $value) $list[$value->HeadCode] = $value->HeadName;
            return $list;
        }
        else
        {
            return false;
        }
    }
    
    public function invoice_entry($incremented_id) {
        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;

        $createby = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        $product_id = $this->input->post('product_id');
        $currency_details = $this->db->select('*')->from('web_setting')->get()->result_array();
        $quantity = $this->input->post('product_quantity', true);
        $invoice_no_generated = $this->input->post('invoic_no');
        // $changeamount  = $this->input->post('change',TRUE);
        $multipayamount = $this->input->post('pamount_by_method', true);
        $multipaytype = $this->input->post('multipaytype', true);
        $paidamount = $this->input->post('paid_amount', true);
        $invoice_no = $incremented_id;

        $bank_id = $this->input->post('bank_id', true);
        if (!empty($bank_id))
        {
            $bankname = $this->db->select('bank_name')->from('bank_add')->where('bank_id', $bank_id)->get()->row()->bank_name;

            $bankcoaid = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankname)->get()->row()->HeadCode;
        }
        else
        {
            $bankcoaid = '';
        }

        $customer_id = $this->input->post('customer_id', true);

        //Full or partial Payment record.
        $paid_amount = $this->input->post('paid_amount', true);
        $transection_id = $this->generator(8);

        if ($multipaytype[0] == 0 || $this->input->post('paid_amount')>0.00)
        {
            $is_credit = 1;
        }
        else
        {
            $is_credit = '';
        }

        $fixordyn = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $is_fixed = '';
        $is_dynamic = '';

        if ($fixordyn->fixed_tax == 1)
        {
            $is_fixed = 1;
            $is_dynamic = 0;
            $paid_tax = $this->input->post('total_vat_amnt', true);
        }
        elseif ($fixordyn->dynamic_tax == 1)
        {
            $is_fixed = 0;
            $is_dynamic = 1;
            $paid_tax = $this->input->post('total_tax', true);
        }
        //Data inserting into invoice table
        

        if (empty($_FILES['attachment']['name']))
        {
            $file_url = NULL;
        }
        else
        {
            $file_url = $this->fileupload->do_upload('./my-assets/file/invoice/', 'attachment');
        }
        $datainv = array(
            'inv_id' => $invoice_no,
            'customer_id' => $customer_id,
            'supplier_id' => $this->input->post('supplier_id', true),
            'broker_id' => $this->input->post('broker_id', true),
            'salesman_id' => $this->input->post('salesman_id', true),
            'invoice_type' => $this->input->post('invoice_type', true),
            'date' => (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d')),
            'total_amount' => $this->input->post('grand_total_price', true),
            'document_date' => $this->input->post('document_date', true),
            'invoice_no' => $this->input->post('invoice_no', true),
            'debit_note_no' => $this->input->post('debit_note_no', true),
            // 'dbn_date'  => $this->input->post('dbn_date',TRUE),
            // 'credit_note_no' => $this->input->post('credit_note_no', true),
            'commission_credit_note_no' => $this->input->post('commission_credit_note_no', true),
            'incentive_credit_note_no' => $this->input->post('incentive_credit_note_no', true),
            // 'crn_date'  => $this->input->post('crn_date',TRUE),
            'policy_type' => $this->input->post('policy_type', true),
            'policy_no' => $this->input->post('policy_no', true),
            'policy_from' => $this->input->post('policy_from', true),
            'policy_to' => $this->input->post('policy_to', true),
            'endorsement_no' => $this->input->post('endorsement_no', true),
            'narration' => $this->input->post('narration', true),
            'interest' => $this->input->post('interest', true),
            // 'insurer'  => $this->input->post('insurer',TRUE),
            'broker' => $this->input->post('broker', true),
            'salesman' => $this->input->post('salesman', true),
            // 'grand_vat_price'  => $this->input->post('grand_vat_price',TRUE),
            // 'prevous_due' => $this->input->post('previous',TRUE),
            'paid_amount' => $this->input->post('paid_amount', true),
            'due_amount' => $this->input->post('due_amount', true),
            // 'total_tax' => $this->input->post('total_tax',TRUE),
            'invoice' => $incremented_id,
            'invoice_details' => (!empty($this->input->post('inva_details', true)) ? $this->input->post('inva_details', true) : 'Thank you for shopping with us'),
            // 'invoice_discount'=> $this->input->post('invoice_discount',TRUE),
            'total_discount'  => $this->input->post('total_discount',TRUE),
            'total_vat_amnt' => $this->input->post('premium_vat', true),
            'sales_by' => $this->session->userdata('id'),
            'status' => 1,
            'payment_type' => 1,
            'bank_id' => (!empty($this->input->post('bank_id', true)) ? $this->input->post('bank_id', true) : null),
            'is_credit' => $is_credit,
            'is_fixed' => $is_fixed,
            'is_dynamic' => $is_dynamic,
            'attachment' => $file_url
        );
        if($this->input->post('paid_amount')>0.00){
        }else{
            $datainv['due_amount'] =$this->input->post('grand_total_price');
        }
        if($this->input->post('due_amount')<=0.00){
            $datainv['premium_paid_date'] = date('Y-m-d H:i:s');
        }
        $this->db->insert('invoice', $datainv);
        $inv_insert_id = $this->db->insert_id();

        $data1 = array(
            'invoice_details_id' => $this->generator(15),
            'invoice_id' => $inv_insert_id,
            'product_id' => $this->input->post('product_id', true),
            'sum_insured' => $this->input->post('sum_insured', true),
            'premium_amount' => $this->input->post('premium_amount', true),
            'premium_policy' => $this->input->post('premium_policy', true),
            'premium_vat' => $this->input->post('premium_vat', true),
            'premium_basmah' => $this->input->post('premium_basmah', true),
            'total_premium_amount' => $this->input->post('total_premium_amount', true),
            'gross_commission' => $this->input->post('gross_commission', true),
            'gross_commission_amount' => $this->input->post('gross_commission_amount', true),
            'gross_commission_vat' => $this->input->post('gross_commission_vat', true),
            'total_gross_commission_amount' => $this->input->post('total_gross_commission_amount', true),
            'broker_commission' => $this->input->post('broker_commission', true),
            'broker_commission_amount' => $this->input->post('broker_commission_amount', true),
            'aggregator_commission' => $this->input->post('aggregator_commission', true),
            'aggregator_commission_amount' => $this->input->post('aggregator_commission_amount', true),
            'salesman_commission' => $this->input->post('salesman_commission', true),
            'salesman_commission_amount' => $this->input->post('salesman_commission_amount', true),
            'gross_incentive' => $this->input->post('gross_incentive', true),
            'gross_incentive_amount' => $this->input->post('gross_incentive_amount', true),
            'broker_incentive' => $this->input->post('broker_incentive', true),
            'broker_incentive_amount' => $this->input->post('broker_incentive_amount', true),
            'aggregator_incentive' => $this->input->post('aggregator_incentive', true),
            'aggregator_incentive_amount' => $this->input->post('aggregator_incentive_amount', true),
            'salesman_incentive' => $this->input->post('salesman_incentive', true),
            'salesman_incentive_amount' => $this->input->post('salesman_incentive_amount', true),
            'status' => 1
        );
        $this->db->insert('invoice_details', $data1);
        
        $taxdata['customer_id'] = $customer_id;
        $taxdata['date'] = (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d'));
        $taxdata['relation_id'] = $invoice_no;
        $this->db->insert('tax_collection', $taxdata);
        
        
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        
        
        $sup_price = $this->input->post('broker_commission_amount', true);
        $s_id = $this->input->post('supplier_id', true);
        $sal_price = $this->input->post('salesman_commission_amount', true);
        $sa_id = $this->input->post('salesman_id', true);
        
        if($this->input->post('invoice_type') == 1 || $this->input->post('invoice_type') == 2) {
            for ($i = 0, $n = count($s_id);$i < $n;$i++) {

                //customer Price
                $cus_prd = array(
                    'invoice_id' => $inv_insert_id,
                    'type'=> $this->input->post('invoice_type'),
                    'product_id' => $product_id,
                    'customer_id' => $this->input->post('customer_id', true),
                    'customer_price' => $this->input->post('grand_total_price'),
                    'paid_amount' => $this->input->post('paid_amount', true),
                    'due_amount' => $this->input->post('due_amount', true)
                );

                $this->db->insert('customer_product', $cus_prd);
                
                
                
                $supp_id = $s_id[$i];
                $spprice = $this->input->post('total_premium_amount');
                
                //Supplier Price(Orient, Adamjee,...)
                $supp_prd = array(
                    'invoice_id' => $inv_insert_id,
                    'type'=> $this->input->post('invoice_type'),
                    'product_id' => $product_id,
                    'supplier_id' => $this->input->post('supplier_id', true),
                    'supplier_price' =>  $this->input->post('grand_total_price'),
                    'paid_amount' => $this->input->post('paid_amount', true),
                    'due_amount' => $this->input->post('due_amount', true)
                );

                $this->db->insert('supplier_product', $supp_prd);
                $sall_id = $sa_id[$i];
                
                
                //Salesman Price(Firoz)
                if($this->input->post('salesman_id') != '' && $this->input->post('salesman_id')!=0){
                    $supp_prd = array(
                        'invoice_id' => $inv_insert_id,
                        'type'=> $this->input->post('invoice_type'),
                        'product_id' => $product_id,
                        'salesman_id' => $this->input->post('salesman_id', true),
                        'salesman_price' => $this->input->post('salesman_commission_amount', true) - $this->input->post('total_discount',true),
                        // 'products_model' => $product_model,
                        'paid_amount' => $this->input->post('salesman_commission_paid_amount', TRUE),
                        'due_amount' => $this->input->post('salesman_commission_due_amount', TRUE)
                    );
    
                    $this->db->insert('salesman_product', $supp_prd);
                    
                    if($this->input->post('salesman_incentive_amount')!=0){
                        
                        $salesman_incentive = array(
                            'invoice_id' => $inv_insert_id,
                            'type'=> 4,
                            'product_id' => $product_id,
                            'salesman_id' => $this->input->post('salesman_id', true),
                            'salesman_price' => $this->input->post('salesman_incentive_amount'),
                            'paid_amount' => 0.00,
                            'due_amount' => $this->input->post('salesman_incentive_amount', true) 
                        );
        
                        $this->db->insert('salesman_product', $salesman_incentive);
                    }
                }
                
                //Broker Price(Insurance Club)
                if($this->input->post('broker_id') != '' && $this->input->post('broker_id')!=0){
                    $broker_prd = array(
                        'invoice_id' => $inv_insert_id,
                        'type'=> $this->input->post('invoice_type'),
                        'product_id' => $product_id,
                        'broker_id' => $this->input->post('broker_id'),
                        'broker_price' => $this->input->post('salesman_commission_amount', true) + $this->input->post('aggregator_commission_amount', true) - $this->input->post('total_discount',true),
                        'paid_amount' => 0.00,
                        'due_amount' => $this->input->post('salesman_commission_amount', true) + $this->input->post('aggregator_commission_amount', true) - $this->input->post('total_discount',true) 
                    );
                    $this->db->insert('broker_product', $broker_prd);
                    
                    if($this->input->post('salesman_incentive_amount')>0 || $this->input->post('aggregator_incentive_amount')>0){
                        
                        $broker_incentive = array(
                            'invoice_id' => $inv_insert_id,
                            'type'=> 4,
                            'product_id' => $product_id,
                            'broker_id' => $this->input->post('broker_id'),
                            'broker_price' => $this->input->post('salesman_incentive_amount', true) + $this->input->post('aggregator_incentive_amount', true),
                            'paid_amount' => 0.00,
                            'due_amount' => $this->input->post('salesman_incentive_amount', true) + $this->input->post('aggregator_incentive_amount', true)
                        );
        
                        $this->db->insert('broker_product', $broker_incentive);
                    }
                }

            }
        }
        
        $is_credit = 1;
        
        // inventory & cost of goods sold start for customer
        $goodsCOAID = $predefine_account->customerCode;
        $purchasevalue  = $this->input->post('grand_total_price', true);
        $goodsNarration = "Premium on Policy No. " . $this->input->post('policy_no', true);
        $goodsComment = "Premium voucher for customer on Policy No. " . $this->input->post('policy_no', true);
        $goodsreVID = $predefine_account->supplierCode;
        
        $goodsreNarration = "Premium on Policy No. ". $this->input->post('policy_no', true);
        $goodsreComment = "Premium voucher for Supplier on Policy No. " . $this->input->post('policy_no', true);
        $supplier_id = $this->input->post('supplier_id', true);
        
        $this->insert_sale_inventory_voucher($invoice_no,$goodsCOAID,$purchasevalue,$goodsNarration,$goodsComment,$goodsreVID,$customer_id,$goodsreNarration,$goodsreComment,$supplier_id);
        // for inventory & cost of goods sold end
        
        // for inventory & cost of goods sold start
        $Narration = "Payment on Policy No. " . $this->input->post('policy_no', true);
        $Comment = "Payment of customer on Policy No. " . $this->input->post('policy_no', true);
        $reVID = $predefine_account->supplierCode;
        
        
        if($this->input->post('paid_amount')>0.00){

            if ($multipaytype[0] == 0) { 

                $amount_pay = $this->input->post('paid_amount',TRUE);
                $amnt_type  = 'Credit';
                $COAID  = $predefine_account->customerCode;
                $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
                    
                $resubcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
                $reNarration = "Payment on Policy No. " . $this->input->post('policy_no', true);
                $reComment = "Payment of supplier on Policy No. " . $this->input->post('policy_no', true);
                $supplier_id = $this->input->post('supplier_id', true);
                $this->insert_sale_creditvoucher($is_credit,$invoice_no,$COAID,$amnt_type,$amount_pay,$Narration,$Comment,$reVID,$subcode, $customer_id,$reNarration=null,$reComment=null,$resubcode=null);

            }else {
                $amnt_type = 'Credit';
                for ($i=0; $i < count($multipaytype); $i++) {

                    $COAID = $multipaytype[$i];
                    $amount_pay = $multipayamount[$i];
                    $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
                        
                    $resubcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
                    
                    $reNarration = "Payment on Policy No. " . $this->input->post('policy_no', true);
                    $reComment = "Payment of supplier on Policy No. " . $this->input->post('policy_no', true);
                    $supplier_id = $this->input->post('supplier_id', true);
                    $this->insert_sale_creditvoucher($is_credit,$invoice_no,$COAID,$amnt_type,$amount_pay,$Narration,$Comment,$reVID,$subcode, $customer_id,$reNarration=null,$reComment=null,$resubcode=null);
                    
                }
            }
            
        } 
        
        // for broker voucher
        $dbtid  = $predefine_account->brokerCode;
        $Narration = "Referral Fees on Policy No. " . $this->input->post('policy_no', true);
        $Comment = "Referral Fees from Broker on Policy No. " . $this->input->post('policy_no', true);
        $reVID = $predefine_account->salesCode;
        $reNarration = "Sales account on Policy No. " . $this->input->post('policy_no', true);
        $reComment = "Sales account for income on Policy No. " . $this->input->post('policy_no', true);
        
        $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $this->input->post('broker_id', true))->where('subTypeId', 7)->get()->row()->id;
        
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
            'referenceNo' => $invoice_no,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'RevCodde' => $reVID,
            'subType' => 7,
            'subCode' => $subcode,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'broker_id' => $this->input->post('broker_id',TRUE),
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );

        
        $creditinsert['Debit'] = ($this->input->post('total_gross_commission_amount', true)-$this->input->post('broker_commission_amount', true))-(($this->input->post('total_gross_commission_amount', true)-$this->input->post('broker_commission_amount', true))*5)/100;
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        
        // for inventory & cost of goods sold start
        $dbtid  = $predefine_account->brokerCode;
        $Narration  = "Vat on Policy No. " . $this->input->post('policy_no', true);
        $Comment  = "Vat from Broker on Policy No." . $this->input->post('policy_no', true);
        $dbtid  = $predefine_account->tax;
        $reNarration = "Vat on Policy No. " . $this->input->post('policy_no', true);
        $reComment = "Vat account for income on Policy No. " . $this->input->post('policy_no', true);
        
        
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $vaucherNo = "DV-" . ($maxid + 1);

        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'DV',
            'referenceNo' => $invoice_no,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'RevCodde' => $reVID,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'Credit' =>(($this->input->post('total_gross_commission_amount', true)-$this->input->post('broker_commission_amount', true))*5)/100,
            'Debit' => 0.00,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        // for inventory & cost of goods sold start
        $dbtid  = $predefine_account->salesmanCode;
        $Narration = "Referral Fees on Policy No. " . $this->input->post('policy_no', true);
        $Comment = "Referral Fees Voucher on Policy No. " . $this->input->post('policy_no', true);
        $reVID = '40103';
        $reNarration = "Expense account on Policy No. " . $this->input->post('policy_no', true);
        $reComment = "Expense account on Policy No. " . $this->input->post('policy_no', true);
        
        $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $this->input->post('salesman_id', true) )->where('subTypeId', 5)->get()->row()->id;
        
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $vaucherNo = "DV-" . ($maxid + 1);

        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'DV',
            'referenceNo' => $invoice_no,
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
            'salesman_id' => $this->input->post('salesman_id',TRUE),
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );

        
        $creditinsert['Credit'] = $this->input->post('salesman_commission_amount', true) - $this->input->post('total_discount',true);
        $creditinsert['Debit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        if($this->input->post('salesman_incentive_amount')>0){
            $creditinsert['Narration'] = "Sales incentive income on Policy No. " . $this->input->post('policy_no', true);
            $creditinsert['ledgerComment ']  = "Sales incentive income Voucher on Policy No. " . $this->input->post('policy_no', true);
            $creditinsert['Credit'] = $this->input->post('total_gross_incentive_amount') - $this->input->post('salesman_incentive_amount');
            $creditinsert['Debit'] = 0.00;
            
            $this->db->insert('acc_vaucher', $creditinsert);
        
        }
        
        if($this->input->post('salesman_commission_paid_amount')>0){
            
            // for inventory & cost of goods sold start
            $dbtid  = '40103';
            $Narration = "Paid Referral fees on " . $this->input->post('policy_no', true);
            $Comment = "Paid Referral fees Voucher for Salesman on " . $this->input->post('policy_no', true);
            $reVID = $predefine_account->bankCode;
            $reNarration = "Bank / Cash in Hand account on " . $this->input->post('policy_no', true);
            $reComment = "Bank / Cash in Hand account on " . $this->input->post('policy_no', true);
            
            $amnt_type  = 'Debit';
            $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $this->input->post('salesman_id',TRUE))->where('subTypeId', 5)->get()->row()->id;
            
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
                'referenceNo' => $inv_insert_id,
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
                'salesman_id' => $this->input->post('salesman_id',TRUE),
                'reNarration' => $reNarration,
                'reledgerComment' => $reComment,
                'resubType' => 0,
                'resubCode' => 0,
            );
    
            
            $creditinsert['Debit'] = $this->input->post('salesman_commission_paid_amount',TRUE);
            $creditinsert['Credit'] = 0.00;
            
            $this->db->insert('acc_vaucher', $creditinsert);
        }
        return $incremented_id;
    }

    // insert sales debitvoucher
    public function insert_sale_creditvoucher($is_credit = null, $invoice_id = null, $dbtid = null, $amnt_type = null, $amnt = null, $Narration = null, $Comment = null, $reVID = null, $subcode = null, $customer_id=null,$reNarration=null,$reComment=null,$resubcode=null) {

        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        // Cash & credit voucher insert
        if ($is_credit == 1)
        {
            $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'CV', 'VNo');
            $vaucherNo = "CV-" . ($maxid + 1);

            $creditinsert = array(
                'fyear' => $fyear,
                'VNo' => $vaucherNo,
                'Vtype' => 'CV',
                'referenceNo' => $invoice_id,
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
                'reNarration' => $reNarration,
                'reledgerComment' => $reComment,
                'resubType' => 4,
                'resubCode' => $resubcode,
            );

        }
        else
        {
            $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'CV', 'VNo');
            $vaucherNo = "CV-" . ($maxid + 1);
            $creditinsert = array(
                'fyear' => $fyear,
                'VNo' => $vaucherNo,
                'Vtype' => 'CV',
                'referenceNo' => $invoice_id,
                'VDate' => $VDate,
                'COAID' => $dbtid,
                'Narration' => $Narration,
                'ledgerComment' => $Comment,
                'RevCodde' => $reVID,
                'isApproved' => 0,
                'CreateBy' => $CreateBy,
                'CreateDate' => $createdate,
                'status' => 0,
                'customer_id' => $customer_id,
                'reNarration' => $reNarration,
                'reledgerComment' => $reComment,
                'resubType' => 4,
                'resubCode' => $resubcode,
            );

        }
        if ($amnt_type == 'Debit')
        {

            $creditinsert['Debit'] = $amnt;
            $creditinsert['Credit'] = 0.00;
        }
        else
        {

            $creditinsert['Debit'] = 0.00;
            $creditinsert['Credit'] = $amnt;
        }

        $this->db->insert('acc_vaucher', $creditinsert);

        return true;
    }
    
    public function insert_sale_inventory_voucher($invoice_id = null, $dbtid = null, $amnt = null, $Narration = null, $Comment = null, $reVID = null, $customer_id=null,$reNarration=null,$reComment=null,$supplier_id=null) {
        $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
        $resubcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');

        // cost of goods sold voucher insert
        $maxidforgoods = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $vaucherNogoods = "DV-" . ($maxidforgoods + 1);
        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNogoods,
            'Vtype' => 'DV',
            'referenceNo' => $invoice_id,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'Debit' => $amnt,
            'RevCodde' => $reVID,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'customer_id' => $customer_id,
            'subType' => 3,
            'subCode' => $subcode,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 4,
            'resubCode' => $resubcode,
        );

        $this->db->insert('acc_vaucher', $creditinsert);

        return true;
    }
    
    public function insert_sale_taxvoucher($invoice_id = null, $dbtid = null, $amnt = null, $Narration = null, $Comment = null, $reVID = null) {

        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');

        // cost of goods sold voucher insert
        $maxidtax = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'JV', 'VNo');
        $vauchertax = "JV-" . ($maxidtax + 1);
        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vauchertax,
            'Vtype' => 'JV',
            'referenceNo' => $invoice_id,
            'VDate' => $VDate,
            'COAID' => $dbtid,
            'Narration' => $Narration,
            'ledgerComment' => $Comment,
            'Debit' => $amnt,
            'RevCodde' => $reVID,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
        );

        $this->db->insert('acc_vaucher', $creditinsert);

        return true;
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

    public function update_invoice() {
        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;
        $dbinv_id = $this->input->post('dbinv_id', true);
        $invoice_id = $this->input->post('invoice_id', true);
        $invoice_no = $this->input->post('invoice', true);
        $createby = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        $customer_id = $this->input->post('customer_id', true);
        $quantity = $this->input->post('product_quantity', true);
        $product_id = $this->input->post('product_id', true);
        $multipayamount = $this->input->post('pamount_by_method', true);
        $multipaytype = $this->input->post('multipaytype', true);
        //   $changeamount = $this->input->post('change',TRUE);
        // if($changeamount > 0){
        // $paidamount = $this->input->post('n_total',TRUE);
        // }else{
        $paidamount = $this->input->post('paid_amount', true);
        // }
        

        $bank_id = $this->input->post('bank_id', true);
        if (!empty($bank_id))
        {
            $bankname = $this->db->select('bank_name')->from('bank_add')->where('bank_id', $bank_id)->get()->row()->bank_name;

            $bankcoaid = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankname)->get()->row()->HeadCode;
        }
        else
        {
            $bankcoaid = '';
        }

        $transection_id = $this->generator(8);

        $this->db->where('referenceNo', $invoice_id);
        $this->db->delete('acc_vaucher');

        $this->db->where('referenceNo', $invoice_id);
        $this->db->delete('acc_transaction');
        
        $this->db->where('relation_id', $invoice_id);
        $this->db->delete('tax_collection');
        if ($multipaytype[0] == 0)
        {
            $is_credit = 1;
        }
        else
        {
            $is_credit = '';
        }

        $fixordyn = $this->db->select('*')->from('vat_tax_setting')->get()->row();

        if ($fixordyn->fixed_tax == 1)
        {

            $paid_tax = $this->input->post('total_vat_amnt', true);
        }
        elseif ($fixordyn->dynamic_tax == 1)
        {

            $paid_tax = $this->input->post('total_tax', true);
        }

        // $data = array(
        //     'invoice_id' => $invoice_id,
        //     'customer_id' => $this->input->post('customer_id', true),
        //     'date' => $this->input->post('invoice_date', true),
        //     'total_amount' => $this->input->post('grand_total_price', true),
        //     'total_tax' => $this->input->post('total_tax', true),
        //     'invoice_details' => $this->input->post('inva_details', true),
        //     'due_amount' => $this->input->post('due_amount', true),
        //     'paid_amount' => $this->input->post('paid_amount', true),
        //     'invoice_discount' => $this->input->post('invoice_discount', true),
        //     'total_discount' => $this->input->post('total_discount', true),
        //     'total_vat_amnt' => $this->input->post('total_vat_amnt', true),
        //     'prevous_due' => $this->input->post('previous', true),
        //     'shipping_cost' => $this->input->post('shipping_cost', true),
        //     'payment_type' => $this->input->post('paytype', true),
        //     'bank_id' => (!empty($this->input->post('bank_id', true)) ? $this->input->post('bank_id', true) : null),
        //     'is_credit' => $is_credit,
        // );
        
        $data = array(
            
            'inv_id' => $invoice_id,
            'customer_id' => $this->input->post('customer_id', true),
            'supplier_id' => $this->input->post('supplier_id', true),
            'broker_id' => $this->input->post('broker_id', true),
            'salesman_id' => $this->input->post('salesman_id', true),
            'date' => (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d')),
            'total_amount' => $this->input->post('grand_total_price', true),
            'document_date' => $this->input->post('document_date', true),
            'debit_note_no' => $this->input->post('debit_note_no', true),
            // 'dbn_date'  => $this->input->post('dbn_date',TRUE),
            // 'credit_note_no' => $this->input->post('credit_note_no', true),
            'commission_credit_note_no' => $this->input->post('commission_credit_note_no', true),
            'incentive_credit_note_no' => $this->input->post('incentive_credit_note_no', true),
            // 'crn_date'  => $this->input->post('crn_date',TRUE),
            'policy_type' => $this->input->post('policy_type', true),
            'policy_no' => $this->input->post('policy_no', true),
            'policy_from' => $this->input->post('policy_from', true),
            'policy_to' => $this->input->post('policy_to', true),
            'endorsement_no' => $this->input->post('endorsement_no', true),
            'narration' => $this->input->post('narration', true),
            'interest' => $this->input->post('interest', true),
            'broker' => $this->input->post('broker', true),
            'salesman' => $this->input->post('salesman', true),
            'paid_amount' => $this->input->post('paid_amount', true),
            'due_amount' => $this->input->post('due_amount', true),
            // 'invoice_discount'=> $this->input->post('invoice_discount',TRUE),
            'total_discount'  => $this->input->post('total_discount',TRUE),
            'total_vat_amnt' => $this->input->post('premium_vat', true),
            'sales_by' => $this->session->userdata('id'),
            'status' => 1,
            'payment_type' => 1,
            'bank_id' => (!empty($this->input->post('bank_id', true)) ? $this->input->post('bank_id', true) : null),
            'is_credit' => $is_credit,
            'is_fixed' => $is_fixed,
            'is_dynamic' => $is_dynamic,
            'attachment' => $file_url
            );
            
        if($this->input->post('due_amount')<=0.00){
            $data['premium_paid_date'] = date('Y-m-d H:i:s');
        }
        $prinfo = $this->db->select('product_id,Avg(rate) as product_rate')->from('product_purchase_details')->where_in('product_id', $product_id)->group_by('product_id')->get()->result();
        $purchase_ave = [];
        $i = 0;
        foreach ($prinfo as $avg)
        {
            $purchase_ave[] = $avg->product_rate * $quantity[$i];
            $i++;
        }
        $sumval = array_sum($purchase_ave);

        if ($invoice_id != '')
        {
            $this->db->where('inv_id', $invoice_id);
            $this->db->update('invoice', $data);
        }



        $iddd = $this->db->select('id')->from('invoice')->where('inv_id', $invoice_id)->get()->row();
        
        
        
        $this->db->where('invoice_id', $iddd->id);
        $this->db->delete('customer_product');

        //customer Price
        $cus_prd = array(
            'invoice_id' => $iddd->id,
            'type'=> $this->input->post('invoice_type'),
            'product_id' => $product_id,
            'customer_id' => $this->input->post('customer_id'),
            'customer_price' => $this->input->post('grand_total_price'),
            'paid_amount' => $this->input->post('paid_amount', true),
            'due_amount' => $this->input->post('due_amount', true)
        );

        $this->db->insert('customer_product', $cus_prd);
        
        $this->db->where('invoice_id', $iddd->id);
        $this->db->delete('supplier_product');
        
        $supp_id = $s_id[$i];
        
        //Supplier Price(Orient, Adamjee)
        $spprice = $this->input->post('total_premium_amount');
        $supp_prd = array(
            'invoice_id' => $iddd->id,
            'type'=> $this->input->post('invoice_type'),
            'product_id' => $this->input->post('product_id'),
            'supplier_id' => $this->input->post('supplier_id'),
            'supplier_price' => $this->input->post('grand_total_price'),
            'paid_amount' => $this->input->post('paid_amount', true),
            'due_amount' => $this->input->post('due_amount', true)
        );

        $this->db->insert('supplier_product', $supp_prd);
        
        $sall_id = $sa_id[$i];
        
        $this->db->where('invoice_id', $iddd->id);
        $this->db->delete('salesman_product');

        //Salesman Amount(Firoz)
        if($this->input->post('salesman_id') != '' && $this->input->post('salesman_id')!=0){
            $supp_prd = array(
                'invoice_id' => $iddd->id,
                'type'=> $this->input->post('invoice_type'),
                'product_id' => $this->input->post('product_id'),
                'salesman_id' => $this->input->post('salesman_id'),
                'salesman_price' => $this->input->post('salesman_commission_amount', true)  - $this->input->post('total_discount',true),
                'paid_amount' => 0.00,
                'due_amount' => $this->input->post('salesman_commission_amount', true) - $this->input->post('total_discount',true) 
            );
            

            $this->db->insert('salesman_product', $supp_prd);
            
            
            if($this->input->post('salesman_incentive_amount')!=0){
                
                $salesman_incentive = array(
                    'invoice_id' => $iddd->id,
                    'type'=> 4,
                    'product_id' => $product_id,
                    'salesman_id' => $this->input->post('salesman_id'),
                    'salesman_price' => $this->input->post('salesman_incentive_amount', true),
                    'paid_amount' => 0.00,
                    'due_amount' => $this->input->post('salesman_incentive_amount', true) 
                );

                $this->db->insert('salesman_product', $salesman_incentive);
            }
        }
        
        $this->db->where('invoice_id', $iddd->id);
        $this->db->delete('broker_product');
        
         //Broker Price(Insurance Club)
        if($this->input->post('broker_id') != '' && $this->input->post('broker_id')!=0 && $this->input->post('broker_commission_amount') > 0){
            $broker_prd = array(
                'invoice_id' => $iddd->id,
                'type'=> $this->input->post('invoice_type'),
                'product_id' => $product_id,
                'broker_id' => $this->input->post('broker_id'),
                'broker_price' => $this->input->post('broker_commission_amount'),
                'paid_amount' => 0.00,
                'due_amount' => $this->input->post('broker_commission_amount', true) 
            );
            $this->db->insert('broker_product', $broker_prd);
            
            if($this->input->post('broker_incentive_amount')!=0){
                
                $broker_incentive = array(
                    'invoice_id' => $iddd->id,
                    'type'=> 4,
                    'product_id' => $product_id,
                    'broker_id' => $this->input->post('broker_id'),
                    'broker_price' => $this->input->post('broker_incentive_amount'),
                    'paid_amount' => 0.00,
                    'due_amount' => $this->input->post('broker_incentive_amount', true) 
                );

                $this->db->insert('broker_product', $broker_incentive);
            }
        }
        
        $taxdata['customer_id'] = $customer_id;
        $taxdata['date'] = (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d'));
        $taxdata['relation_id'] = $invoice_id;
        $this->db->insert('tax_collection', $taxdata);



        // Inserting for Accounts adjustment.
        ############ default table :: customer_payment :: inflow_92mizdldrv #################
        $invoice_d_id = $this->input->post('invoice_details_id', true);
        $quantity = $this->input->post('product_quantity', true);
        $rate = $this->input->post('product_rate', true);
        $p_id = $this->input->post('product_id', true);
        $total_amount = $this->input->post('total_price', true);
        $discount_rate = $this->input->post('discountvalue', true);
        $discount_per = $this->input->post('discount', true);
        $vat_amnt = $this->input->post('vatvalue', true);
        $vat_amnt_pcnt = $this->input->post('vatpercent', true);
        $invoice_description = $this->input->post('desc', true);
        $this->db->where('invoice_id', $dbinv_id);
        $this->db->delete('invoice_details');
        $serial_n = $this->input->post('serial_no', true);
        for ($i = 0, $n = count($p_id);$i < $n;$i++)
        {
            $data1 = array(
                'invoice_details_id' => $this->generator(15),
                'invoice_id' => $dbinv_id,
                'product_id' => $this->input->post('product_id', true),
                'sum_insured' => $this->input->post('sum_insured', true),
                'premium_amount' => $this->input->post('premium_amount', true),
                'premium_policy' => $this->input->post('premium_policy', true),
                'premium_vat' => $this->input->post('premium_vat', true),
                'premium_basmah' => $this->input->post('premium_basmah', true),
                'total_premium_amount' => $this->input->post('total_premium_amount', true),
                'gross_commission' => $this->input->post('gross_commission', true),
                'gross_commission_amount' => $this->input->post('gross_commission_amount', true),
                'gross_commission_vat' => $this->input->post('gross_commission_vat', true),
                'total_gross_commission_amount' => $this->input->post('total_gross_commission_amount', true),
                'broker_commission' => $this->input->post('broker_commission', true),
                'broker_commission_amount' => $this->input->post('broker_commission_amount', true),
                'aggregator_commission' => $this->input->post('aggregator_commission', true),
                'aggregator_commission_amount' => $this->input->post('aggregator_commission_amount', true),
                'salesman_commission' => $this->input->post('salesman_commission', true),
                'salesman_commission_amount' => $this->input->post('salesman_commission_amount', true),
                'gross_incentive' => $this->input->post('gross_incentive', true),
                'gross_incentive_amount' => $this->input->post('gross_incentive_amount', true),
                'broker_incentive' => $this->input->post('broker_incentive', true),
                'broker_incentive_amount' => $this->input->post('broker_incentive_amount', true),
                'aggregator_incentive' => $this->input->post('aggregator_incentive', true),
                'aggregator_incentive_amount' => $this->input->post('aggregator_incentive_amount', true),
                'salesman_incentive' => $this->input->post('salesman_incentive', true),
                'salesman_incentive_amount' => $this->input->post('salesman_incentive_amount', true),
                'status' => 1
            );
            $this->db->insert('invoice_details', $data1);

            $product_price = array(

                'price' => $product_rate
            );
            // $this->db->insert('invoice_details', $data1);

            $this->db->where('product_id', $product_id)->update('product_information', $product_price);

            $customer_id = $this->input->post('customer_id', true);

        }
        
        $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        
        // inventory & cost of goods sold start for customer
        $goodsCOAID = $predefine_account->customerCode;
        $purchasevalue  = $this->input->post('grand_total_price', true);
        $supplier_id = $this->input->post('supplier_id', true);
        $goodsNarration = "Premium on Policy No. " . $this->input->post('policy_no', true);
        $goodsComment = "Premium voucher for customer on Policy No. " . $this->input->post('policy_no', true);
        $goodsreVID = $predefine_account->supplierCode;
        
        $goodsreNarration = "Premium on Policy No. ". $this->input->post('policy_no', true);
        $goodsreComment = "Premium voucher for Supplier on Policy No. " . $this->input->post('policy_no', true);
        
        $this->insert_sale_inventory_voucher($invoice_id,$goodsCOAID,$purchasevalue,$goodsNarration,$goodsComment,$goodsreVID,$customer_id,$goodsreNarration,$goodsreComment,$supplier_id);
        // for inventory & cost of goods sold end
        
        // for inventory & cost of goods sold start
        $Narration = "Payment on Policy No. " . $this->input->post('policy_no', true);
        $Comment = "Payment of customer on Policy No. " . $this->input->post('policy_no', true);
        $reVID = $predefine_account->supplierCode;
        
        
        if($this->input->post('paid_amount')>0.00){

            if ($multipaytype[0] == 0) { 

                $amount_pay = $this->input->post('paid_amount',TRUE);
                $amnt_type  = 'Credit';
                $COAID  = $predefine_account->customerCode;
                $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
                    
                $resubcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
                $reNarration = "Payment on Policy No. " . $this->input->post('policy_no', true);
                $reComment = "Payment of supplier on Policy No. " . $this->input->post('policy_no', true);
                $supplier_id = $this->input->post('supplier_id', true);
                $this->insert_sale_creditvoucher($is_credit,$invoice_id,$COAID,$amnt_type,$amount_pay,$Narration,$Comment,$reVID,$subcode, $customer_id,$reNarration=null,$reComment=null,$resubcode=null);

            }else {
                $amnt_type = 'Credit';
                for ($i=0; $i < count($multipaytype); $i++) {

                    $COAID = $multipaytype[$i];
                    $amount_pay = $multipayamount[$i];
                    $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row()->id;
                        
                    $resubcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
                    
                    $reNarration = "Payment on Policy No. " . $this->input->post('policy_no', true);
                    $reComment = "Payment of supplier on Policy No. " . $this->input->post('policy_no', true);
                    $supplier_id = $this->input->post('supplier_id', true);
                    $this->insert_sale_creditvoucher($is_credit,$invoice_id,$COAID,$amnt_type,$amount_pay,$Narration,$Comment,$reVID,$subcode, $customer_id,$reNarration=null,$reComment=null,$resubcode=null);
                    
                }
            }
            
        } 
        // for broker voucher
        $dbtid  = $predefine_account->brokerCode;
        
        $Narration = "Referral Fees on Policy No. " . $this->input->post('policy_no', true);
        $Comment = "Referral Fees from Broker on Policy No. " . $this->input->post('policy_no', true);
        $reVID = $predefine_account->salesCode;
        $reNarration = "Sales account on Policy No. " . $this->input->post('policy_no', true);
        $reComment = "Sales account for income on Policy No. " . $this->input->post('policy_no', true);
        
        $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $this->input->post('broker_id', true))->where('subTypeId', 7)->get()->row()->id;
        
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
            'subType' => 7,
            'subCode' => $subcode,
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'broker_id' => $this->input->post('broker_id',TRUE),
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );

        
        $creditinsert['Debit'] = ($this->input->post('total_gross_commission_amount', true)-$this->input->post('broker_commission_amount', true))-(($this->input->post('total_gross_commission_amount', true)-$this->input->post('broker_commission_amount', true))*5)/100;
        $creditinsert['Credit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        
        // for inventory & cost of goods sold start
        $dbtid  = $predefine_account->brokerCode;
        $Narration  = "Vat on Policy No. " . $this->input->post('policy_no', true);
        $Comment  = "Vat from Broker on Policy No. " . $this->input->post('policy_no', true);
        $dbtid  = $predefine_account->tax;
        $reNarration = "Vat on Policy No. " . $this->input->post('policy_no', true);
        $reComment = "Vat account for income on Policy No. " . $this->input->post('policy_no', true);
        
        
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
            'isApproved' => 0,
            'CreateBy' => $CreateBy,
            'CreateDate' => $createdate,
            'status' => 0,
            'Credit' =>(($this->input->post('total_gross_commission_amount', true)-$this->input->post('broker_commission_amount', true))*5)/100,
            'Debit' => 0.00,
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        
        
        // for inventory & cost of goods sold start
        $dbtid  = $predefine_account->salesmanCode;
        $Narration = "Referral Fees on Policy No. " . $this->input->post('policy_no', true);
        $Comment = "Referral Fees Voucher on Policy No. " . $this->input->post('policy_no', true);
        $reVID = '40103';
        $reNarration = "Expense account on Policy No. " . $this->input->post('policy_no', true);
        $reComment = "Expense account on Policy No. " . $this->input->post('policy_no', true);
        
        
        $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $this->input->post('salesman_id', true) )->where('subTypeId', 5)->get()->row()->id;
        
        // Cash & credit voucher insert
        $maxid = $this->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'CV', 'VNo');
        $vaucherNo = "CV-" . ($maxid + 1);

        $creditinsert = array(
            'fyear' => $fyear,
            'VNo' => $vaucherNo,
            'Vtype' => 'CV',
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
            'salesman_id' => $this->input->post('salesman_id',TRUE),
            'reNarration' => $reNarration,
            'reledgerComment' => $reComment,
            'resubType' => 0,
            'resubCode' => 0,
        );

        
        $creditinsert['Credit'] = $this->input->post('salesman_commission_amount', true) - $this->input->post('total_discount',true);
        $creditinsert['Debit'] = 0.00;
        
        $this->db->insert('acc_vaucher', $creditinsert);
        
        if($this->input->post('salesman_incentive_amount')>0){
            $creditinsert['Narration'] = "Sales incentive income on " . $this->input->post('policy_no', true);
            $creditinsert['ledgerComment ']  = "Sales incentive income Voucher on " . $this->input->post('policy_no', true);
            $creditinsert['Credit'] = $this->input->post('total_gross_incentive_amount') - $this->input->post('salesman_incentive_amount');
            $creditinsert['Debit'] = 0.00;
            
            $this->db->insert('acc_vaucher', $creditinsert);
        
        }
        
        
        if($this->input->post('salesman_commission_paid_amount')>0){
            
            // for inventory & cost of goods sold start
            $dbtid  = '40103';
            $Narration = "Paid Referral fees on " . $this->input->post('policy_no', true);
            $Comment = "Paid Referral fees Voucher for Salesman on " . $this->input->post('policy_no', true);
            $reVID = $predefine_account->bankCode;
            $reNarration = "Bank/ Cash in Hand account on " . $this->input->post('policy_no', true);
            $reComment = "Bank/ Cash in Hand account on " . $this->input->post('policy_no', true);
            
            $amnt_type  = 'Debit';
            $subcode  = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $this->input->post('salesman_id',TRUE))->where('subTypeId', 5)->get()->row()->id;
            
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
                'subType' => 5,
                'subCode' => $subcode,
                'isApproved' => 0,
                'CreateBy' => $CreateBy,
                'CreateDate' => $createdate,
                'status' => 0,
                'salesman_id' => $this->input->post('salesman_id',TRUE),
                'reNarration' => $reNarration,
                'reledgerComment' => $reComment,
                'resubType' => 0,
                'resubCode' => 0,
            );
    
            
            $creditinsert['Debit'] = $this->input->post('salesman_commission_paid_amount',TRUE);
            $creditinsert['Credit'] = 0.00;
            
            $this->db->insert('acc_vaucher', $creditinsert);
        }
        return $invoice_id;
    }

    //POS invoice entry
    public function pos_invoice_setup($product_id) {
        $product_information = $this->db->select('*')->from('product_information')->join('supplier_product', 'product_information.product_id = supplier_product.product_id')->where('product_information.product_id', $product_id)->get()->row();

        if ($product_information != null)
        {

            $this->db->select('SUM(a.quantity) as total_purchase');
            $this->db->from('product_purchase_details a');
            $this->db->where('a.product_id', $product_id);
            $total_purchase = $this->db->get()->row();

            $this->db->select('SUM(b.quantity) as total_sale');
            $this->db->from('invoice_details b');
            $this->db->where('b.product_id', $product_id);
            $total_sale = $this->db->get()->row();

            $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);

            $data2 = (object)array(
                'total_product' => $available_quantity,
                'supplier_price' => $product_information->supplier_price,
                'price' => $product_information->price,
                'supplier_id' => $product_information->supplier_id,
                'product_id' => $product_information->product_id,
                'product_name' => $product_information->product_name,
                'product_model' => $product_information->product_model,
                'unit' => $product_information->unit,
                'tax' => $product_information->tax,
                'image' => $product_information->image,
                'serial_no' => $product_information->serial_no,
                'product_vat' => $product_information->product_vat,
            );

            return $data2;
        }
        else
        {
            return false;
        }
    }

    public function searchprod($cid) {
        $this->db->select('*');
        $this->db->from('product_information');
        if ($cid != 'all')
        {
            $this->db->where('category_id', $cid);
        }
        $this->db->order_by('product_name', 'asc');
        $query = $this->db->get();
        $itemlist = $query->result();
        if ($cid = '')
        {
            return false;
        }
        else
        {
            return $itemlist;
        }

    }
    
    public function searchprod_byname($pname = null) {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->like('product_name', $pname);
        $this->db->order_by('product_name', 'asc');
        $this->db->limit(20);
        $query = $this->db->get();
        $itemlist = $query->result();
        return $itemlist;
    }

    public function walking_customer() {
        return $data = $this->db->select('*')->from('customer_information')->like('customer_name', 'walking', 'after')->get()->result_array();
    }

    public function category_dropdown() {
        $data = $this->db->select("*")->from('product_category')->get()->result();

        $list = array(
            '' => 'select_category'
        );
        if (!empty($data))
        {
            foreach ($data as $value) $list[$value->category_id] = $value->category_name;
            return $list;
        }
        else
        {
            return false;
        }
    }

    public function category_list() {
        $this->db->select('*');
        $this->db->from('product_category');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve company Edit Data
    public function retrieve_company() {
        $this->db->select('*');
        $this->db->from('company_information');
        $this->db->limit('1');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_setting_editdata() {
        $this->db->select('*');
        $this->db->from('web_setting');
        $this->db->where('setting_id', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }
    
    //Get Supplier rate of a product
    public function supplier_rate($product_id) {
        $this->db->select('supplier_price');
        $this->db->from('supplier_product');
        $this->db->where(array(
            'product_id' => $product_id
        ));
        $query = $this->db->get();
        return $query->result_array();

        $this->db->select('Avg(rate) as supplier_price');
        $this->db->from('product_purchase_details');
        $this->db->where(array(
            'product_id' => $product_id
        ));
        $query = $this->db->get()->row();
        return $query->result_array();
    }

    public function supplier_price($product_id) {
        $this->db->select('supplier_price');
        $this->db->from('supplier_product');
        $this->db->where(array(
            'product_id' => $product_id
        ));
        $supplier_product = $this->db->get()->row();

        $this->db->select('Avg(rate) as supplier_price');
        $this->db->from('product_purchase_details');
        $this->db->where(array(
            'product_id' => $product_id
        ));
        $purchasedetails = $this->db->get()->row();
        $price = (!empty($purchasedetails->supplier_price) ? $purchasedetails->supplier_price : $supplier_product->supplier_price);

        return (!empty($price) ? $price : 0);
    }

    public function autocompletproductdata($product_name) {
        $query = $this->db->select('*')->from('product_information')->like('product_name', $product_name, 'both')->or_like('product_model', $product_name, 'both')->order_by('product_name', 'asc')->limit(15)->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_invoice_html_data($invoice_id) {
        $this->db->select('a.total_tax,
                        a.*,
                        b.*,
                        c.*,
                        d.product_id,
                        d.product_name,
                        d.product_details,
                        d.unit,
                        d.product_model,
                        a.paid_amount as paid_amount,
                        a.due_amount as due_amount');
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('product_information d', 'd.product_id = c.product_id');
        $this->db->where('a.inv_id', $invoice_id);
        // $this->db->where('c.quantity >', 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function user_invoice_data($user_id) {
        return $this->db->select('*')->from('users')->where('user_id', $user_id)->get()->row();
    }
    
    //product information
    public function get_product_info($product_id) {
        $this->db->select('a.*,b.*');
        $this->db->from('product_information a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where(array(
            'a.product_id' => $product_id,
            'a.status' => 1
        ));
        $product_information = $this->db->get()->row();

        $data2['premium_amount'] = $product_information->premium_amount;
        $data2['premium_vat'] = $product_information->premium_vat;
        $data2['total_premium_amount'] = $product_information->total_premium_amount;
        $data2['gross_commission'] = $product_information->gross_commission;
        $data2['gross_commission_amount'] = $product_information->gross_commission_amount;
        $data2['gross_commission_vat'] = $product_information->gross_commission_vat;
        $data2['total_gross_commission_amount'] = $product_information->total_gross_commission_amount;
        $data2['broker_commission'] = $product_information->broker_commission;
        $data2['broker_commission_amount'] = $product_information->broker_commission_amount;
        $data2['aggregator_commission'] = $product_information->aggregator_commission;
        $data2['aggregator_commission_amount'] = $product_information->aggregator_commission_amount;
        $data2['salesman_commission'] = $product_information->salesman_commission;
        $data2['salesman_commission_amount'] = $product_information->salesman_commission_amount;
        
        $data2['gross_incentive'] = $product_information->gross_incentive;
        $data2['gross_incentive_amount'] = $product_information->gross_incentive_amount;
        $data2['broker_incentive'] = $product_information->broker_incentive;
        $data2['broker_incentive_amount'] = $product_information->broker_incentive_amount;
        $data2['aggregator_incentive'] = $product_information->aggregator_incentive;
        $data2['aggregator_incentive_amount'] = $product_information->aggregator_incentive_amount;
        $data2['salesman_incentive'] = $product_information->salesman_incentive;
        $data2['salesman_incentive_amount'] = $product_information->salesman_incentive_amount;
        $data2['paid_amount'] = $product_information->paid_amount;
        $data2['due'] = $product_information->due;
        return $data2;
    }
    
    // product information retrieve by product id
    public function get_total_product_invoic($product_id) {
        $this->db->select('SUM(a.quantity) as total_purchase');
        $this->db->from('product_purchase_details a');
        $this->db->where('a.product_id', $product_id);
        $total_purchase = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();

        $this->db->select('a.*,b.*');
        $this->db->from('product_information a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where(array(
            'a.product_id' => $product_id,
            'a.status' => 1
        ));
        $product_information = $this->db->get()->row();

        $this->db->select('SUM(quantity) as purchase_qty,batch_id,product_id');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $product_id);
        $this->db->group_by('batch_id');
        $pur_product_batch = $this->db->get()->result();

        $this->db->select('SUM(quantity) as sale_qty,batch_id');
        $this->db->from('invoice_details');
        $this->db->where('product_id', $product_id);
        $this->db->group_by('batch_id');
        $sell_product_batch = $this->db->get()->result();

        $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);
        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;
        $taxfield = '';
        $taxvar = [];
        for ($i = 0;$i < $num_column;$i++)
        {
            $taxfield = 'tax' . $i;
            $data2[$taxfield] = (!empty($product_information->$taxfield) ? $product_information->$taxfield : 0);
            $taxvar[$i] = (!empty($product_information->$taxfield) ? $product_information->$taxfield : 0);
            $data2['taxdta'] = $taxvar;
        }

        $content = explode(',', $product_information->serial_no);

        $html = "";
        if (empty($pur_product_batch))
        {
            $html .= "No Serial Found !";
        }
        else
        {
            // Select option created for product
            $html .= "<select name=\"serial_no[]\" onchange=\"invoice_product_batch()\"  class=\"serial_no_1 form-control basic-single\" id=\"serial_no_1\">";
            $html .= "<option value=''>" . display('select_one') . "</option>";
            foreach ($pur_product_batch as $p_batch)
            {
                $sellt_prod_batch = $this->db->select('SUM(quantity) as sale_qty,batch_id, product_id')->from('invoice_details')->where('product_id', $p_batch->product_id)->where('batch_id', $p_batch->batch_id)->get()->row();
                $pur_prod = (empty($sellt_prod_batch->sale_qty) ? 0 : $sellt_prod_batch->sale_qty);
                $available_prod = $p_batch->purchase_qty - $pur_prod;
                if ($available_prod > 0)
                {
                    # code...
                    $html .= "<option value=" . $p_batch->batch_id . ">" . $p_batch->batch_id . "</option>";
                }

            }
            $html .= "</select>";
        }

        $data2['total_product'] = $available_quantity;
        $data2['supplier_price'] = $product_information->supplier_price;
        $data2['price'] = $product_information->price;
        $data2['supplier_id'] = $product_information->supplier_id;
        $data2['unit'] = $product_information->unit;
        $data2['tax'] = $product_information->tax;
        $data2['product_vat'] = $product_information->product_vat;
        $data2['serial'] = $html;
        $data2['txnmber'] = $num_column;

        return $data2;
    }

    public function generator($lenth) {
        $number = array(
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9"
        );

        for ($i = 0;$i < $lenth;$i++)
        {
            $rand_value = rand(0, 8);
            $rand_number = $number["$rand_value"];

            if (empty($con))
            {
                $con = $rand_number;
            }
            else
            {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }

    public function stock_qty_check($product_id) {
        $this->db->select('SUM(a.quantity) as total_purchase');
        $this->db->from('product_purchase_details a');
        $this->db->where('a.product_id', $product_id);
        $total_purchase = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();

        $this->db->select('a.*,b.*');
        $this->db->from('product_information a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where(array(
            'a.product_id' => $product_id,
            'a.status' => 1
        ));
        $product_information = $this->db->get()->row();

        $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);
        return (!empty($available_quantity) ? $available_quantity : 0);

    }

    public function cloudsubset_invoice_pos_print_direct($invoice_id = null) {
        $invoice_detail = $this->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')->from('tax_settings')->where('is_show', 1)->get()->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname)
        {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_cartoon = 0;
        $subTotal_discount = 0;
        $subTotal_ammount = 0;
        $descript = 0;
        $isserial = 0;
        $is_discount = 0;
        $is_dis_val = 0;
        $vat_amnt_per = 0;
        $vat_amnt = 0;
        $isunit = 0;
        if (!empty($invoice_detail))
        {
            foreach ($invoice_detail as $k => $v)
            {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v)
            {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description']))
                {
                    $descript = $descript + 1;

                }
                if (!empty($invoice_detail[$k]['serial_no']))
                {
                    $isserial = $isserial + 1;

                }
                if (!empty($invoice_detail[$k]['unit']))
                {
                    $isunit = $isunit + 1;

                }
                if (!empty($invoice_detail[$k]['discount_per']))
                {
                    $is_discount = $is_discount + 1;

                }
                if (!empty($invoice_detail[$k]['discount']))
                {
                    $is_dis_val = $is_dis_val + 1;

                }
                if (!empty($invoice_detail[$k]['vat_amnt_per']))
                {
                    $vat_amnt_per = $vat_amnt_per + 1;

                }
                if (!empty($invoice_detail[$k]['vat_amnt']))
                {
                    $vat_amnt = $vat_amnt + 1;

                }
            }
        }

        $payment_method_list = $this->invoice_method_wise_balance($invoice_id);
        $terms_list = $this->db->select('*')->from('seles_termscondi')->get()->result();
        $totalbal = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $user_id = $invoice_detail[0]['sales_by'];
        $currency_details = $this->retrieve_setting_editdata();
        $users = $this->user_invoice_data($user_id);
        $data = array(
            'title' => display('pos_print'),
            'invoice_id' => $invoice_detail[0]['invoice_id'],
            'invoice_no' => $invoice_detail[0]['invoice'],
            'customer_name' => $invoice_detail[0]['customer_name'],
            'customer_address' => $invoice_detail[0]['customer_address'],
            'customer_mobile' => $invoice_detail[0]['customer_mobile'],
            'customer_email' => $invoice_detail[0]['customer_email'],
            'final_date' => $invoice_detail[0]['final_date'],
            'invoice_details' => $invoice_detail[0]['invoice_details'],
            'total_amount' => number_format($totalbal, 2, '.', ','),
            'grand_total' => $invoice_detail[0]['total_amount'],
            'subTotal_cartoon' => $subTotal_cartoon,
            'subTotal_quantity' => $subTotal_quantity,
            'invoice_discount' => number_format($invoice_detail[0]['invoice_discount'], 2, '.', ','),
            'total_discount' => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'total_tax' => number_format($invoice_detail[0]['total_tax'], 2, '.', ','),
            'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
            'paid_amount' => number_format($invoice_detail[0]['paid_amount'], 2, '.', ','),
            'due_amount' => number_format($invoice_detail[0]['due_amount'], 2, '.', ','),
            'shipping_cost' => number_format($invoice_detail[0]['shipping_cost'], 2, '.', ','),
            'invoice_all_data' => $invoice_detail,
            'previous' => number_format($invoice_detail[0]['prevous_due'], 2, '.', ','),
            'is_discount' => $is_discount,
            'users_name' => $users->first_name . ' ' . $users->last_name,
            'tax_regno' => $txregname,
            'is_desc' => $descript,
            'is_serial' => $isserial,
            'is_dis_val' => $is_dis_val,
            'vat_amnt_per' => $vat_amnt_per,
            'vat_amnt' => $vat_amnt,
            'is_unit' => $isunit,
            'company_info' => $this->retrieve_company(),
            'currency' => $currency_details[0]['currency'],
            'position' => $currency_details[0]['currency_position'],
            'discount_type' => $currency_details[0]['discount_type'],
            'logo' => $currency_details[0]['invoice_logo'],

            'all_discount' => number_format($invoice_detail[0]['total_discount'], 2, '.', ','),
            'p_method_list' => $payment_method_list,
            'terms_list' => $terms_list,
            'total_vat' => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),

        );

        return $data;

    }

    public function product_list() {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $this->db->limit(30);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function cloudsubset_print_settingdata() {
        $this->db->select('*');
        $this->db->from('print_setting');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }
        return false;
    }

    public function allterms_list() {
        return $this->db->select('*')->from('seles_termscondi')->get()->result();
    }

    public function create_terms($data = []) {
        return $this->db->insert('seles_termscondi', $data);
    }

    public function update_terms($data = []) {
        return $this->db->where('id', $data['id'])->update('seles_termscondi', $data);
    }

    public function single_terms_data($id) {
        return $this->db->select('*')->from('seles_termscondi')->where('id', $id)->get()->row();
    }

    public function delete_terms($id) {
        $this->db->where('id', $id)->delete("seles_termscondi");
        if ($this->db->affected_rows())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function invoice_method_wise_balance($invoice_id) {

        return $this->db->select('acc_vaucher.Debit,acc_vaucher.COAID,acc_coa.HeadName')->from('acc_vaucher')->join('acc_coa', 'acc_coa.HeadCode=acc_vaucher.COAID', 'left')->where('acc_vaucher.referenceNo', $invoice_id)->where('acc_vaucher.Vtype', 'CV')->get()->result();
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
}