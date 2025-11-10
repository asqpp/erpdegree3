<?php
defined('BASEPATH') or exit('No direct script access allowed');

#------------------------------------
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------
class Report_model extends CI_Model
{
    public function salesman_list() {
        $this->db->select('*');
        $this->db->from('salesman_information');
        $this->db->order_by('salesman_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function broker_list() {
        $this->db->select('*');
        $this->db->from('broker_information');
        $this->db->order_by('broker_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function cloudsubset_getStock($postData = null)
    {

        $response = array();

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
        if ($searchValue != '')
        {
            $searchQuery = " (a.product_name like '%" . $searchValue . "%' or a.product_model like '%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchValue != '')
        {
            $this->db->where($searchQuery);
        }
        $this->db->group_by('a.product_id');
        $records = $this->db->get()->num_rows();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchValue != '')
        {
            $this->db->where($searchQuery);
        }
        $this->db->group_by('a.product_id');
        $records = $this->db->get()->num_rows();
        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select("a.*,
                a.product_name,
                a.product_id,
                a.product_model
                ");
        $this->db->from('product_information a');
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->group_by('a.product_id');
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;
        foreach ($records as $record)
        {
            $stockin = $this->db->select('sum(quantity) as totalSalesQnty')->from('invoice_details')->where('product_id', $record->product_id)->get()->row();
            $stockout = $this->db->select('sum(quantity) as totalPurchaseQnty,Avg(rate) as purchaseprice')->from('product_purchase_details')->where('product_id', $record->product_id)->get()->row();

            $sprice = (!empty($record->price) ? $record->price : 0);
            $pprice = (!empty($stockout->purchaseprice) ? sprintf('%0.2f', $stockout->purchaseprice) : 0);
            $stock = (!empty($stockout->totalPurchaseQnty) ? $stockout->totalPurchaseQnty : 0) - (!empty($stockin->totalSalesQnty) ? $stockin->totalSalesQnty : 0);
            $data[] = array(
                'sl' => $sl,
                'product_name' => $record->product_name,
                'product_model' => $record->product_model,
                'sales_price' => sprintf('%0.2f', $sprice) ,
                'purchase_p' => $pprice,
                'totalPurchaseQnty' => $stockout->totalPurchaseQnty,
                'totalSalesQnty' => $stockin->totalSalesQnty,
                'stok_quantity' => sprintf('%0.2f', $stock) ,

                'total_sale_price' => ($stockout->totalPurchaseQnty - $stockin->totalSalesQnty) * $sprice,
                'purchase_total' => ($stockout->totalPurchaseQnty - $stockin->totalSalesQnty) * $pprice,
            );
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw) ,
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function totalnumberof_product()
    {

        $this->db->select("a.*,
                a.product_name,
                a.product_id,
                a.product_model,
                c.supplier_price
                ");
        $this->db->from('product_information a');

        $this->db->join('supplier_product c', 'c.product_id = a.product_id', 'left');
        $this->db->group_by('a.product_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->num_rows();
        }
        return false;

    }

    public function accounts_closing_data()
    {
        $last_closing_amount = $this->get_last_closing_amount();
        $cash_in = $this->cash_data_receipt();
        $cash_out = $this->cash_data();
        if ($last_closing_amount != null)
        {
            $last_closing_amount = $last_closing_amount[0]['amount'];
            $cash_in_hand = ($last_closing_amount + $cash_in) - $cash_out;
        }
        else
        {
            $last_closing_amount = 0;
            $cash_in_hand = $cash_in - $cash_out;
        }

        return array(
            "last_day_closing" => number_format($last_closing_amount, 2, '.', ',') ,
            "cash_in" => number_format($cash_in, 2, '.', ',') ,
            "cash_out" => number_format($cash_out, 2, '.', ',') ,
            "cash_in_hand" => number_format($cash_in_hand, 2, '.', ',')
        );
    }

    public function get_last_closing_amount()
    {
        $sql = "SELECT amount FROM daily_closing WHERE date = (SELECT MAX(date) FROM daily_closing)";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if ($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function cash_data_receipt()
    {
        //-----------
        $cash = 0;
        $datse = date('Y-m-d');
        $this->db->select('sum(Debit) as amount');
        $this->db->from('acc_transaction');
        $this->db->where('COAID', 111000001);
        $this->db->where('VDate', $datse);
        $result_amount = $this->db->get();
        $amount = $result_amount->result_array();
        $cash += $amount[0]['amount'];
        return $cash;
    }

    public function cash_data()
    {
        //-----------
        $cash = 0;
        $datse = date('Y-m-d');
        $this->db->select('sum(Credit) as amount');
        $this->db->from('acc_transaction');
        $this->db->where('COAID', 111000001);
        $this->db->where('VDate', $datse);
        $result_amount = $this->db->get();
        $amount = $result_amount->result_array();
        $cash += $amount[0]['amount'];
        return $cash;
    }

    //CLOSING ENTRY
    public function daily_closing_entry($data)
    {
        return $this->db->insert('daily_closing', $data);
    }

    public function get_closing_report()
    {
        $this->db->select("* ,(opening_balance + amount_in) - amount_out as 'cash_in_hand'");
        $this->db->from('closing_records');
        $this->db->where('status', 1);
        $this->db->order_by('datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_date_wise_closing_report($from_date, $to_date)
    {
        $dateRange = "DATE(datetime) BETWEEN '$from_date' AND '$to_date'";
        $this->db->select("* ,(opening_balance + amount_in) - amount_out as 'cash_in_hand'");
        $this->db->from('closing_records');
        $this->db->where('status', 1);
        $this->db->where($dateRange, NULL, false);
        $this->db->order_by('datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Retrieve todays_sales_report
    public function todays_sales_report()
    {
        $today = date('Y-m-d');
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,j.paid_amount,j.due_amount,e.supplier_name,f.salesman_name,g.broker_name, h.paid_amount as salesmanpaid, h.due_amount as salesmandue,
                            ( 
                            case 
                              when 
                                `a`.`premium_paid_date` is not null
                              then timestampdiff( day, `a`.`document_date`, `a`.`premium_paid_date` ) 
                              else 'NOT PAID'
                            end
                          ) as datediff, 
                            ( 
                            case 
                              when 
                                `a`.`document_date` is not null
                              then timestampdiff( day,  `a`.`document_date`, NOW()) 
                              else 0
                            end
                            ) as documentdatediff");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('salesman_product h', 'h.invoice_id = a.id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        $this->db->join('broker_product i', 'i.invoice_id = a.id');
        $this->db->join('customer_product j', 'j.invoice_id = a.id');
        $this->db->where('a.date', $today);
        $this->db->where('d.total_gross_commission_amount >', 0);
        $this->db->group_by('a.inv_id');
        // $this->db->order_by('a.inv_id', 'desc');
        $this->db->order_by('a.document_date', 'desc');
        $query = $this->db->get();

        // var_dump($this->db->last_query());exit;
        if ($query->num_rows() > 0)
        {
            $records = $query->result_array();
            $i=0;
            $rec = array();
            foreach ($records as $record){
                if($record['gross_incentive'] != '' && $record['gross_incentive'] != NULL && $record['gross_incentive'] != 0.00){
                    // $rec[$i] = $record;
                    // echo 'oh';exit;
                    $button = '';
                    $base_url = base_url();
                    $customer = $record['customer_name'];
                    
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_commission_amount']>0) {
                        $aggregator_fee = $record['aggregator_commission'];
                        $aggregator_fee_amount =  $record['aggregator_commission_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_commission'] + $record['salesman_commission'];
                        $aggregator_share_amount =  $record['aggregator_commission_amount'] + $record['salesman_commission_amount'];
                        
                    // }
                    
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => $record['premium_amount'],
                        'premium_vat' => $record['premium_vat'],
                        'total_premium_amount' => $record['total_premium_amount'],
                        'gross_commission' => $record['gross_commission'],
                        'gross_commission_amount' => $record['gross_commission_amount'],
                        'gross_commission_vat' => $record['gross_commission_vat'],
                        'total_gross_commission_amount' => $record['total_gross_commission_amount'],
                        'broker_commission' => $record['broker_commission'],
                        'broker_commission_amount' => $record['broker_commission_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_commission'],
                        'salesman_commission_amount' => $record['salesman_commission_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_commission_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Commission',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                    
                    
                    
                    
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_incentive_amount']>0) {
                        $aggregator_fee = $record['aggregator_incentive'];
                        $aggregator_fee_amount = $record['aggregator_incentive_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_incentive'] + $record['salesman_incentive'];
                        $aggregator_share_amount = $record['aggregator_incentive_amount'] + $record['salesman_incentive_amount'];
                    // }
                    
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => '',
                        'premium_vat' => '',
                        'total_premium_amount' => '',
                        'gross_commission' => $record['gross_incentive'],
                        'gross_commission_amount' => $record['gross_incentive_amount'],
                        'gross_commission_vat' =>$record['gross_incentive_vat'],
                        'total_gross_commission_amount' => $record['total_gross_incentive_amount'],
                        'broker_commission' => $record['broker_incentive'],
                        'broker_commission_amount' => $record['broker_incentive_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_incentive'],
                        'salesman_commission_amount' => $record['salesman_incentive_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_incentive_amount,
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Incentive',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                }else{
                    // $rec[$i] = $record;
                    
                    $button = '';
                    $base_url = base_url();
                    $customer = $record['customer_name'];
                    
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_commission_amount']>0) {
                        $aggregator_fee = $record['aggregator_commission'];
                        $aggregator_fee_amount =  $record['aggregator_commission_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_commission'] + $record['salesman_commission'];
                        $aggregator_share_amount =  $record['aggregator_commission_amount'] + $record['salesman_commission_amount'];
                    // }
        
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => $record['premium_amount'],
                        'premium_vat' => $record['premium_vat'],
                        'total_premium_amount' => $record['total_premium_amount'],
                        'gross_commission' => $record['gross_commission'],
                        'gross_commission_amount' => $record['gross_commission_amount'],
                        'gross_commission_vat' => $record['gross_commission_vat'],
                        'total_gross_commission_amount' => $record['total_gross_commission_amount'],
                        'broker_commission' => $record['broker_commission'],
                        'broker_commission_amount' => $record['broker_commission_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_commission'],
                        'salesman_commission_amount' => $record['salesman_commission_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_commission_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Commission',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                }
            
            }
            return $records;
            // return $query->result_array();
        }
        return false;
    }
    public function getSalesReportList($postData = null)
    {

        $response = array();

        
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $salesman_id =  $this->input->post('salesman_id');
        $broker_id =  $this->input->post('broker_id');
        if (!empty($fromdate))
        {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        }
        else
        {
            $datbetween = "";
        }

        ## Search
        $searchQuery = "";
        if ($searchValue != '')
        {
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.inv_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%' or e.supplier_name like'%" . $searchValue . "%' or f.salesman_name like'%" . $searchValue . "%' or g.broker_name like'%" . $searchValue . "%') ";
        }




        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id',left);
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id',left);
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id',left);
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id',left);
        
        if(!empty($salesman_id)){
            $this->db->where('a.salesman_id', $salesman_id);
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->group_by('a.inv_id');
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;
        
        
        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id',left);
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id',left);
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id',left);
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id',left);
        
        if(!empty($salesman_id)){
            $this->db->where('a.salesman_id', $salesman_id);
        }
        if(!empty($broker_id)){
            $this->db->where('a.broker_id', $broker_id);
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        $this->db->where('(c.gross_incentive != "" AND c.gross_incentive IS NOT NULL AND c.gross_incentive != 0.00) ');
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->group_by('a.inv_id');
        $records = $this->db->get()->result();
        
        $totalRecordwithFilter = $totalRecordwithFilter + $records[0]->allcount;
        
        
        
        
        
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = ($postData['length'] == -1) ? $totalRecordwithFilter : $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = 'inv_id'; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id',left);
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id',left);
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id',left);
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id',left);
        if(!empty($salesman_id)){
            $this->db->where('a.salesman_id', $salesman_id);
        }
        if(!empty($broker_id)){
            $this->db->where('a.broker_id', $broker_id);
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->group_by('a.inv_id');
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;


        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id',left);
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id',left);
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id',left);
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id',left);
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->group_by('a.inv_id');
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;
        
        
        ## Total number of record with filtering with incentives
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id',left);
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id',left);
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id',left);
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id',left);
        if(!empty($salesman_id)){
            $this->db->where('a.salesman_id', $salesman_id);
        }
        if(!empty($broker_id)){
            $this->db->where('a.broker_id', $broker_id);
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        $this->db->where('(c.gross_incentive != "" AND c.gross_incentive IS NOT NULL AND c.gross_incentive != 0.00) ');
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->group_by('a.inv_id');
        $records = $this->db->get()->result();
        
        $totalRecordwithFilter = $totalRecordwithFilter + $records[0]->allcount;
// echo $totalRecordwithFilter;exit;
        ## Fetch records
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,j.paid_amount,j.due_amount,e.supplier_name,f.salesman_name,g.broker_name, h.paid_amount as salesmanpaid, h.due_amount as salesmandue, 
                            ( 
                            case 
                              when 
                                `a`.`premium_paid_date` is not null
                              then timestampdiff( day, `a`.`document_date`, `a`.`premium_paid_date` ) 
                              else 'NOT PAID'
                            end
                          ) as datediff, 
                            ( 
                            case 
                              when 
                                `a`.`document_date` is not null
                              then timestampdiff( day,  `a`.`document_date`, NOW()) 
                              else 0
                            end
                            ) as documentdatediff");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id',left);
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id',left);
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id',left);
        $this->db->join('salesman_product h', 'h.invoice_id = a.id',left);
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id',left);
        $this->db->join('broker_product i', 'i.invoice_id = a.id',left);
        $this->db->join('customer_product j', 'j.invoice_id = a.id',left);
        if(!empty($salesman_id)){
            $this->db->where('a.salesman_id', $salesman_id);
        }
        if(!empty($broker_id)){
            $this->db->where('a.broker_id', $broker_id);
        }
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->where('d.total_gross_commission_amount >', 0);
        $this->db->group_by('a.inv_id');
        $this->db->order_by($columnName, $columnSortOrder);
        // $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        // var_dump($this->db->last_query());exit;
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        
        $rec = array();
        // echo count($records);exit;
        $h=0;
        foreach ($records as $record)
        {
            
            if($record->gross_incentive != '' && $record->gross_incentive != NULL && $record->gross_incentive != 0.00){
                // $rec[$i] = $record;
                // echo 'oh';exit;
                $button = '';
                $base_url = base_url();
                $customer = $record->customer_name;
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_commission_amount>0) {
                    $aggregator_fee = $record->aggregator_commission;
                    $aggregator_fee_amount =  $record->aggregator_commission_amount;
                // } else {
                    $aggregator_share = $record->aggregator_commission + $record->salesman_commission;
                    $aggregator_share_amount =  $record->aggregator_commission_amount + $record->salesman_commission_amount;
                // }
                
                $rec[] = array(
                    's_n' =>$sl,
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => $record->premium_amount,
                    'premium_vat' => $record->premium_vat,
                    'total_premium_amount' => $record->total_premium_amount,
                    'gross_commission' => $record->gross_commission,
                    'gross_commission_amount' => $record->gross_commission_amount,
                    'gross_commission_vat' => $record->gross_commission_vat,
                    'total_gross_commission_amount' => $record->total_gross_commission_amount,
                    'broker_commission' => $record->broker_commission,
                    'broker_commission_amount' => $record->broker_commission_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_commission,
                    'salesman_commission_amount' => $record->salesman_commission_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_commission_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesman_due,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Commission',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_discount' => $record->total_discount,
                    'total_amount' => $record->total_amount
                );
                // $i++;
                
                
                
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_incentive_amount>0) {
                    $aggregator_fee = $record->aggregator_incentive;
                    $aggregator_fee_amount =  $record->aggregator_incentive_amount;
                // } else {
                    $aggregator_share = $record->aggregator_incentive + $record->salesman_incentive;
                    $aggregator_share_amount = $record->aggregator_incentive_amount + $record->salesman_incentive_amount;
                // }
                
                $rec[] = array(
                    's_n' =>$sl,
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => '',
                    'premium_vat' => '',
                    'total_premium_amount' => '',
                    'gross_commission' => $record->gross_incentive,
                    'gross_commission_amount' => $record->gross_incentive_amount,
                    'gross_commission_vat' =>$record->gross_incentive_vat,
                    'total_gross_commission_amount' => $record->total_gross_incentive_amount,
                    'broker_commission' => $record->broker_incentive,
                    'broker_commission_amount' => $record->broker_incentive_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_incentive,
                    'salesman_commission_amount' => $record->salesman_incentive_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_incentive_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Incentive',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_discount' => $record->total_discount,
                    'total_amount' => $record->total_amount
                );
                // $i++;
            }else{
                
                $button = '';
                $base_url = base_url();
                $customer = $record->customer_name;
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_commission_amount>0) {
                    $aggregator_fee = $record->aggregator_commission;
                    $aggregator_fee_amount =  $record->aggregator_commission_amount;
                // } else {
                    $aggregator_share = $record->aggregator_commission + $record->salesman_commission;
                    $aggregator_share_amount =  $record->aggregator_commission_amount + $record->salesman_commission_amount;
                // }
    
                $rec[] = array(
                    's_n' =>$sl,
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => $record->premium_amount,
                    'premium_vat' => $record->premium_vat,
                    'total_premium_amount' => $record->total_premium_amount,
                    'gross_commission' => $record->gross_commission,
                    'gross_commission_amount' => $record->gross_commission_amount,
                    'gross_commission_vat' => $record->gross_commission_vat,
                    'total_gross_commission_amount' => $record->total_gross_commission_amount,
                    'broker_commission' => $record->broker_commission,
                    'broker_commission_amount' => $record->broker_commission_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_commission,
                    'salesman_commission_amount' => $record->salesman_commission_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_commission_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Commission',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_discount' => $record->total_discount,
                    'total_amount' => $record->total_amount
                );
                $i++;
            }
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw) ,
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $rec
        );

        return $response;
    }

    //Retrieve all Report
    public function retrieve_dateWise_SalesReports($from_date, $to_date)
    {
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,j.paid_amount,j.due_amount,e.supplier_name,f.salesman_name,g.broker_name, h.paid_amount as salesmanpaid, h.due_amount as salesmandue, 
                            ( 
                            case 
                              when 
                                `a`.`premium_paid_date` is not null
                              then timestampdiff( day, `a`.`document_date`, `a`.`premium_paid_date` ) 
                              else 'NOT PAID'
                            end
                          ) as datediff, 
                            ( 
                            case 
                              when 
                                `a`.`document_date` is not null
                              then timestampdiff( day,  `a`.`document_date`, NOW()) 
                              else 0
                            end
                            ) as documentdatediff");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('salesman_product h', 'h.invoice_id = a.id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        $this->db->join('broker_product i', 'i.invoice_id = a.id');
        $this->db->join('customer_product j', 'j.invoice_id = a.id');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->where('d.total_gross_commission_amount >', 0);
        $this->db->group_by('a.inv_id');
        // $this->db->order_by('a.date', 'desc');
        $this->db->order_by('a.document_date', 'desc');
        $query = $this->db->get();
            $records = $query->result_array();
        echo $this->db->last_query();exit;
        if ($query->num_rows() > 0)
        {
            // return $query->result_array();
            
            $records = $query->result_array();
            $i=0;
            $rec = array();
            foreach ($records as $record){
                if($record['gross_incentive'] != '' && $record['gross_incentive'] != NULL && $record['gross_incentive'] != 0.00){
                    // $rec[$i] = $record;
                    // echo 'oh';exit;
                    $button = '';
                    $base_url = base_url();
                    $customer = $record['customer_name'];
                
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_commission_amount']>0) {
                        $aggregator_fee = $record['aggregator_commission'];
                        $aggregator_fee_amount =  $record['aggregator_commission_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_commission'] + $record['salesman_commission'];
                        $aggregator_share_amount =  $record['aggregator_commission_amount'] + $record['salesman_commission_amount'];
                    // }
        
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => $record['premium_amount'],
                        'premium_vat' => $record['premium_vat'],
                        'total_premium_amount' => $record['total_premium_amount'],
                        'gross_commission' => $record['gross_commission'],
                        'gross_commission_amount' => $record['gross_commission_amount'],
                        'gross_commission_vat' => $record['gross_commission_vat'],
                        'total_gross_commission_amount' => $record['total_gross_commission_amount'],
                        'broker_commission' => $record['broker_commission'],
                        'broker_commission_amount' => $record['broker_commission_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_commission'],
                        'salesman_commission_amount' => $record['salesman_commission_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_commission_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Commission',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                    
                    
                
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_incentive_amount']>0) {
                        $aggregator_fee = $record['aggregator_incentive'];
                        $aggregator_fee_amount =  $record['aggregator_incentive_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_incentive'] + $record['salesman_incentive'];
                        $aggregator_share_amount = $record['aggregator_incentive_amount'] + $record['salesman_incentive_amount'];
                    // }
                    
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => '',
                        'premium_vat' => '',
                        'total_premium_amount' => '',
                        'gross_commission' => $record['gross_incentive'],
                        'gross_commission_amount' => $record['gross_incentive_amount'],
                        'gross_commission_vat' =>$record['gross_incentive_vat'],
                        'total_gross_commission_amount' => $record['total_gross_incentive_amount'],
                        'broker_commission' => $record['broker_incentive'],
                        'broker_commission_amount' => $record['broker_incentive_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_incentive'],
                        'salesman_commission_amount' => $record['salesman_incentive_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_incentive_amount,
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Incentive',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                }else{
                    // $rec[$i] = $record;
                    
                
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_commission_amount']>0) {
                        $aggregator_fee = $record['aggregator_commission'];
                        $aggregator_fee_amount =  $record['aggregator_commission_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_commission'] + $record['salesman_commission'];
                        $aggregator_share_amount =  $record['aggregator_commission_amount'] + $record['salesman_commission_amount'];
                    // }
                    $button = '';
                    $base_url = base_url();
                    $customer = $record['customer_name'];
        
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => $record['premium_amount'],
                        'premium_vat' => $record['premium_vat'],
                        'total_premium_amount' => $record['total_premium_amount'],
                        'gross_commission' => $record['gross_commission'],
                        'gross_commission_amount' => $record['gross_commission_amount'],
                        'gross_commission_vat' => $record['gross_commission_vat'],
                        'total_gross_commission_amount' => $record['total_gross_commission_amount'],
                        'broker_commission' => $record['broker_commission'],
                        'broker_commission_amount' => $record['broker_commission_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_commission'],
                        'salesman_commission_amount' => $record['salesman_commission_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_commission_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Commission',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                }
            
            }
            return $records;
        }
        return false;
    }

    //Retrieve todays_purchase_report
    public function todays_purchase_report()
    {
        $today = date('Y-m-d');
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_date', $today);
        $this->db->order_by('a.purchase_id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    //    ======= its for  todays_customer_receipt ===========
    public function todays_customer_receipt($today = null)
    {
        $this->db->select('a.*,b.HeadName, c.name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
        $this->db->join('acc_subcode c', 'a.subCode=c.id');
        $this->db->where('a.subType', 3);
        $this->db->where('a.Credit >', 0);
        $this->db->where('DATE(a.VDate)', $today);
        $this->db->where('a.IsAppove', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function filter_customer_wise_receipt($custome_id = null, $from_date = null)
    {
        $this->db->select('a.Narration,b.HeadName,a.Credit,b.HeadName, c.name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
        $this->db->join('acc_subcode c', 'a.subCode=c.id');
        $this->db->where('c.referenceNo', $custome_id);
        $this->db->where('a.Credit >', 0);
        $this->db->where('a.subType', 3);
        $this->db->where('DATE(a.VDate)', $from_date);
        $this->db->where('a.IsAppove', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function customerinfo_rpt($customer_id)
    {
        return $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->result_array();
    }

    // ======================= user sales report ================
    public function user_sales_report($from_date, $to_date, $user_id)
    {
        $this->db->select("sum(total_amount) as amount,count(a.inv_id) as toal_invoice,a.*,b.first_name,b.last_name");
        $this->db->from('invoice a');
        $this->db->join('users b', 'b.user_id = a.sales_by', 'left');
        if (!empty($user_id))
        {
            $this->db->where('a.sales_by', $user_id);
        }
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.sales_by');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function userList()
    {
        $this->db->select("*");
        $this->db->from('users');
        $this->db->order_by('first_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_dateWise_DueReports($from_date, $to_date)
    {
        $this->db->select("a.*,b.*,c.*,d.product_name,d.paid_amount,d.due,e.supplier_name,f.salesman_name,g.broker_name");
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('product_information d', 'd.product_id = b.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = d.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        
        $this->db->where('a.date BETWEEN "' . $from_date . '" and "' . $to_date . '"');
        $this->db->where('a.due_amount >', 0);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }
    public function get_retrieve_dateWise_DueReports($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        if (!empty($fromdate))
        {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        }
        else
        {
            $datbetween = "";
        }
        // dd($datbetween);
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
        if ($searchValue != '')
        {
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.invoice_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
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
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,j.paid_amount,j.due_amount,e.supplier_name,f.salesman_name,g.broker_name, h.paid_amount as salesmanpaid, h.due_amount as salesmandue, 
                            ( 
                            case 
                              when 
                                `a`.`premium_paid_date` is not null
                              then timestampdiff( day, `a`.`document_date`, `a`.`premium_paid_date` ) 
                              else 'NOT PAID'
                            end
                          ) as datediff, 
                            ( 
                            case 
                              when 
                                `a`.`document_date` is not null
                              then timestampdiff( day,  `a`.`document_date`, NOW()) 
                              else 0
                            end
                            ) as documentdatediff");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('salesman_product h', 'h.invoice_id = a.id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        $this->db->join('broker_product i', 'i.invoice_id = a.id');
        $this->db->join('customer_product j', 'j.invoice_id = a.id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->where('d.total_gross_commission_amount >', 0);
        $this->db->group_by('a.inv_id');
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        // dd($records);
        foreach ($records as $record)
        {
            // $button = '';
            // $base_url = base_url();
            // $customer = $record->customer_name;

            // $data[] = array(
            //     'date' => date("d/m/Y", strtotime($record->date)),
            //     'invoice_id' => $record->invoice_id,
            //     'customer_name' => $customer,
            //     'total_amount' => $record->total_amount,
            //     'paid_amount' => $record->paid_amount,
            //     'due_amount' => $record->due_amount,
            // );
            
            
            if($record->gross_incentive != '' && $record->gross_incentive != NULL && $record->gross_incentive != 0.00){
                // $rec[$i] = $record;
                // echo 'oh';exit;
                $button = '';
                $base_url = base_url();
                $customer = $record->customer_name;
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_commission_amount>0) {
                    $aggregator_fee = $record->aggregator_commission;
                    $aggregator_fee_amount =  $record->aggregator_commission_amount;
                // } else {
                    $aggregator_share = $record->aggregator_commission + $record->salesman_commission;
                    $aggregator_share_amount =  $record->aggregator_commission_amount + $record->salesman_commission_amount;
                // }
    
                $rec[] = array(
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => $record->premium_amount,
                    'premium_vat' => $record->premium_vat,
                    'total_premium_amount' => $record->total_premium_amount,
                    'gross_commission' => $record->gross_commission,
                    'gross_commission_amount' => $record->gross_commission_amount,
                    'gross_commission_vat' => $record->gross_commission_vat,
                    'total_gross_commission_amount' => $record->total_gross_commission_amount,
                    'broker_commission' => $record->broker_commission,
                    'broker_commission_amount' => $record->broker_commission_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_commission,
                    'salesman_commission_amount' => $record->salesman_commission_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_commission_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Commission',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_amount' => $record->total_amount
                );
                // $i++;
                
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_incentive_amount>0) {
                    $aggregator_fee = $record->aggregator_incentive;
                    $aggregator_fee_amount =  $record->aggregator_incentive_amount;
                // } else {
                    $aggregator_share = $record->aggregator_incentive + $record->salesman_incentive;
                    $aggregator_share_amount = $record->aggregator_incentive_amount + $record->salesman_incentive_amount;
                // }
                
                
                $rec[] = array(
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => '',
                    'premium_vat' => '',
                    'total_premium_amount' => '',
                    'gross_commission' => $record->gross_incentive,
                    'gross_commission_amount' => $record->gross_incentive_amount,
                    'gross_commission_vat' =>$record->gross_incentive_vat,
                    'total_gross_commission_amount' => $record->total_gross_incentive_amount,
                    'broker_commission' => $record->broker_incentive,
                    'broker_commission_amount' => $record->broker_incentive_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_incentive,
                    'salesman_commission_amount' => $record->salesman_incentive_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_incentive_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Incentive',
                    'total_amount' => $record->total_amount
                );
                // $i++;
            }else{
                
                $button = '';
                $base_url = base_url();
                $customer = $record->customer_name;
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_commission_amount>0) {
                    $aggregator_fee = $record->aggregator_commission;
                    $aggregator_fee_amount =  $record->aggregator_commission_amount;
                // } else {
                    $aggregator_share = $record->aggregator_commission + $record->salesman_commission;
                    $aggregator_share_amount =  $record->aggregator_commission_amount + $record->salesman_commission_amount;
                // }
                
                $rec[] = array(
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => $record->premium_amount,
                    'premium_vat' => $record->premium_vat,
                    'total_premium_amount' => $record->total_premium_amount,
                    'gross_commission' => $record->gross_commission,
                    'gross_commission_amount' => $record->gross_commission_amount,
                    'gross_commission_vat' => $record->gross_commission_vat,
                    'total_gross_commission_amount' => $record->total_gross_commission_amount,
                    'broker_commission' => $record->broker_commission,
                    'broker_commission_amount' => $record->broker_commission_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_commission,
                    'salesman_commission_amount' => $record->salesman_commission_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_commission_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Commission',
                    'total_amount' => $record->total_amount
                );
                $i++;
            }
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw) ,
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $data
        );

        return $response;
    }

    // ================= Shipping cost ===========================
    public function retrieve_dateWise_Shippingcost($from_date, $to_date)
    {
        $this->db->select("a.*");
        $this->db->from('invoice a');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve todays_purchase_report
    public function cloudsubset_purchase_report($from_date, $to_date)
    {
        $today = date('Y-m-d');
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_date >=', $from_date);
        $this->db->where('a.purchase_date <=', $to_date);
        $this->db->order_by('a.purchase_date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }
    public function getReportList($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        if (!empty($fromdate))
        {
            $datbetween = "(a.purchase_date BETWEEN '$fromdate' AND '$todate')";
        }
        else
        {
            $datbetween = "";
        }
        // dd($datbetween);
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
        if ($searchValue != '')
        {
            $searchQuery = " (a.purchase_date like '%" . $searchValue . "%' or a.purchase_id like '%" . $searchValue . "%' or a.grand_total_amount like'%" . $searchValue . "%' or b.supplier_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $purchase_amount = 0;
        // dd($records);
        foreach ($records as $record)
        {
            $button = '';
            $base_url = base_url();
            $supplier = $record->supplier_name;

            $data[] = array(
                'purchase_date' => $record->purchase_date,
                'purchase_id' => $record->purchase_id,
                'supplier_name' => $supplier,
                'due_amount' => $record->due_amount,
                'grand_total_amount' => $record->grand_total_amount,
            );
            $purchase_amount += $record->grand_total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw) ,
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "purchase_amount" => $purchase_amount,
            "aaData" => $data
        );

        return $response;
    }

    public function category_list_product()
    {
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

    //    ============= its for purchase_report_category_wise ===============
    public function purchase_report_category_wise($from_date, $to_date, $category)
    {
        $this->db->select('b.product_name, b.product_model, SUM(a.total_amount) as total_amount, d.purchase_date, c.category_name');
        $this->db->group_by('b.product_id, c.category_id');
        $this->db->from('product_purchase_details a');
        $this->db->join('product_information b', 'b.product_id = a.product_id');
        $this->db->join('product_category c', 'c.category_id = b.category_id');
        $this->db->join('product_purchase d', 'd.id = a.purchase_id');
        $this->db->where('d.purchase_date >=', $from_date);
        $this->db->where('d.purchase_date <=', $to_date);
        if ($category)
        {
            $this->db->where('c.category_id', $category);
        }
        $query = $this->db->get();
        return $query->result();
    }

    //RETRIEVE DATE WISE SINGE PRODUCT REPORT
    public function retrieve_product_sales_report($from_date, $to_date, $product_id)
    {
        $this->db->select("a.*,b.product_name,b.product_model,c.date,c.invoice,c.total_amount,d.customer_name");
        $this->db->from('invoice_details a');
        $this->db->join('product_information b', 'b.product_id = a.product_id');
        $this->db->join('invoice c', 'c.id = a.invoice_id');
        $this->db->join('customer_information d', 'd.customer_id = c.customer_id');
        $this->db->where('c.date >=', $from_date);
        $this->db->where('c.date <=', $to_date);
        if ($product_id)
        {
            $this->db->where('a.product_id', $product_id);
        }
        $this->db->order_by('c.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function product_list()
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    //    ============= its for sales_report_category_wise ===============
    public function sales_report_category_wise($from_date, $to_date, $category)
    {
        // $this->db->select('b.product_name, b.product_model, sum(a.total_premium_amount) as total_price, d.date, c.category_name');
        // $this->db->from('invoice_details a');
        // $this->db->join('product_information b', 'b.product_id = a.product_id');
        // $this->db->join('product_category c', 'c.category_id = b.category_id');
        // $this->db->join('invoice d', 'd.id = a.invoice_id');
        
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,a.paid_amount,a.due_amount,e.supplier_name,h.salesman_name,g.broker_name, c.product_model, sum(d.total_premium_amount) as total_price,f.category_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information h', 'h.salesman_id = a.salesman_id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        $this->db->join('product_category f', 'f.category_id = c.category_id');
        
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        if ($category)
        {
            $this->db->where('f.category_id', $category);
        }
        $this->db->group_by('f.product_id, f.category_id');
        $query = $this->db->get();
        return $query->result();
    }

    // sales return data
    public function sales_return_list($start, $end)
    {
        $this->db->select('a.net_total_amount,a.*,b.customer_name');
        $this->db->from('product_return a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('usablity', 1);
        $this->db->where('a.date_return >=', $start);
        $this->db->where('a.date_return <=', $end);
        $this->db->order_by('a.date_return', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    // return supplier
    public function supplier_return($start, $end)
    {
        $this->db->select('a.net_total_amount,a.*,b.supplier_name');
        $this->db->from('product_return a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('usablity', 2);
        $this->db->where('a.date_return >=', $start);
        $this->db->where('a.date_return <=', $end);
        $this->db->order_by('a.date_return', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    // tax report query
    public function retrieve_dateWise_tax($from_date, $to_date)
    {
        $this->db->select("a.*");
        $this->db->from('invoice a');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    //Total profit report
    public function total_profit_report($start_date, $end_date)
    {
        $this->db->select("a.date,a.invoice,b.invoice_id, CAST(sum(total_price) AS DECIMAL(16,2)) as total_sale");
        $this->db->select('CAST(sum(`quantity`*`supplier_rate`) AS DECIMAL(16,2)) as total_supplier_rate', false);
        $this->db->select("CAST(SUM(total_price) - SUM(`quantity`*`supplier_rate`) AS DECIMAL(16,2)) AS total_profit");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.id');
        $this->db->where('a.date >=', $start_date);
        $this->db->where('a.date <=', $end_date);
        $this->db->group_by('b.invoice_id');
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }

    public function payment_methods()
    {
        return $data = $this->db->select('*')->from('acc_coa')->where('PHeadName', 'Cash')->or_where('PHeadName', 'Cash at Bank')->get()->result();
    }

    public function received_bypayment_method($seller_id, $headcode)
    {
        $data = $this->db->select('sum(Debit) as total_received')->from('acc_transaction')->where('COAID', $headcode)->where('CreateBy', $seller_id)->where('VDate', date('Y-m-d'))->where('IsAppove', 1)->get()->row();
        return ($data ? $data->total_received : 0);
    }

    public function paid_bypayment_method($seller_id, $headcode)
    {
        $data = $this->db->select('sum(Credit) as total_paid')->from('acc_transaction')->where('COAID', $headcode)->where('CreateBy', $seller_id)->where('VDate', date('Y-m-d'))->where('IsAppove', 1)->get()->row();
        return ($data ? $data->total_paid : 0);
    }

    public function create_opening($data = [])
    {
        return $this->db->insert('closing_records', $data);
    }
    //Retrieve todays_production_report
    
    
    public function todays_commission_report()
    {
        $today = date('Y-m-d');
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,j.paid_amount,j.due_amount,e.supplier_name,f.salesman_name,g.broker_name, h.paid_amount as salesmanpaid, h.due_amount as salesmandue, 
                            ( 
                            case 
                              when 
                                `a`.`premium_paid_date` is not null
                              then timestampdiff( day, `a`.`document_date`, `a`.`premium_paid_date` ) 
                              else 'NOT PAID'
                            end
                            ) as datediff, 
                            ( 
                            case 
                              when 
                                `a`.`document_date` is not null
                              then timestampdiff( day,  `a`.`document_date`, NOW()) 
                              else 0
                            end
                            ) as documentdatediff");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('salesman_product h', 'h.invoice_id = a.id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        $this->db->join('broker_product i', 'i.invoice_id = a.id');
        $this->db->join('customer_product j', 'j.invoice_id = a.id');
        $this->db->where('a.date', $today);
        $this->db->where('d.total_gross_commission_amount >', 0);
        $this->db->group_by('a.inv_id');
        // $this->db->order_by('a.inv_id', 'desc');
        $this->db->order_by('a.document_date', 'desc');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            // return $query->result_array();
            
            $records = $query->result_array();
            $i=0;
            $rec = array();
            foreach ($records as $record){
                if($record['gross_incentive'] != '' && $record['gross_incentive'] != NULL && $record['gross_incentive'] != 0.00){
                    // $rec[$i] = $record;
                    // echo 'oh';exit;
                    $button = '';
                    $base_url = base_url();
                    $customer = $record['customer_name'];
                
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_commission_amount'] >0) {
                        $aggregator_fee = $record['aggregator_commission'];
                        $aggregator_fee_amount =  $record['aggregator_commission_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_commission'] + $record['salesman_commission'];
                        $aggregator_share_amount =  $record['aggregator_commission_amount'] + $record['salesman_commission_amount'];
                    // }
        
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => $record['premium_amount'],
                        'premium_vat' => $record['premium_vat'],
                        'total_premium_amount' => $record['total_premium_amount'],
                        'gross_commission' => $record['gross_commission'],
                        'gross_commission_amount' => $record['gross_commission_amount'],
                        'gross_commission_vat' => $record['gross_commission_vat'],
                        'total_gross_commission_amount' => $record['total_gross_commission_amount'],
                        'broker_commission' => $record['broker_commission'],
                        'broker_commission_amount' => $record['broker_commission_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_commission'],
                        'salesman_commission_amount' => $record['salesman_commission_amount'], 
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_commission_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Commission',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                    
                
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_incentive_amount'] >0) {
                        $aggregator_fee = $record['aggregator_incentive'];
                        $aggregator_fee_amount =  $record['aggregator_incentive_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_incentive'] + $record['salesman_incentive'];
                        $aggregator_share_amount = $record['aggregator_incentive_amount'] + $record['salesman_incentive_amount'];
                    // }
                    
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => '',
                        'premium_vat' => '',
                        'total_premium_amount' => '',
                        'gross_commission' => $record['gross_incentive'],
                        'gross_commission_amount' => $record['gross_incentive_amount'],
                        'gross_commission_vat' =>$record['gross_incentive_vat'],
                        'total_gross_commission_amount' => $record['total_gross_incentive_amount'],
                        'broker_commission' => $record['broker_incentive'],
                        'broker_commission_amount' => $record['broker_incentive_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_incentive'],
                        'salesman_commission_amount' => $record['salesman_incentive_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_incentive_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Incentive',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                }else{
                    // $rec[$i] = $record;
                    
                    $button = '';
                    $base_url = base_url();
                    $customer = $record['customer_name'];
                
                    $aggregator_share =0;
                    $aggregator_share_amount = 0;
                    $aggregator_fee=0;
                    $aggregator_fee_amount =0;
                    
                    // if($record['salesman_commission_amount']>0) {
                        $aggregator_fee = $record['aggregator_commission'];
                        $aggregator_fee_amount =  $record['aggregator_commission_amount'];
                    // } else {
                        $aggregator_share = $record['aggregator_commission'] + $record['salesman_commission'];
                        $aggregator_share_amount =  $record['aggregator_commission_amount'] + $record['salesman_commission_amount'];
                    // }
        
                    $rec[$i] = array(
                        'invoice_id' => $record['inv_id'],
                        'date' => date("d/m/Y", strtotime($record['date'])),
                        'document_date' => date("d/m/Y", strtotime($record['document_date'])),
                        'customer_name' => $customer,
                        'supplier_name' => $record['supplier_name'],
                        'salesman_name' => $record['salesman_name'],
                        'broker_name' => $record['broker_name'],
                        'policy_type' => $record['policy_type'],
                        'product_name' => $record['product_name'],
                        'policy_no' => $record['policy_no'],
                        'endorsement_no' => $record['endorsement_no'],
                        'policy_from' => date("d/m/Y", strtotime($record['policy_from']))  . ' to ' . date("d/m/Y", strtotime($record['policy_to'])),
                        'policy_to' => $record['policy_to'],
                        'premium_amount' => $record['premium_amount'],
                        'premium_vat' => $record['premium_vat'],
                        'total_premium_amount' => $record['total_premium_amount'],
                        'gross_commission' => $record['gross_commission'],
                        'gross_commission_amount' => $record['gross_commission_amount'],
                        'gross_commission_vat' => $record['gross_commission_vat'],
                        'total_gross_commission_amount' => $record['total_gross_commission_amount'],
                        'broker_commission' => $record['broker_commission'],
                        'broker_commission_amount' => $record['broker_commission_amount'],
                        'aggregator_share' => $aggregator_share,
                        'aggregator_share_amount' => $aggregator_share_amount,
                        'aggregator_fee' => $aggregator_fee,
                        'aggregator_fee_amount' => $aggregator_fee_amount,
                        'salesman_commission' => $record['salesman_commission'],
                        'salesman_commission_amount' => $record['salesman_commission_amount'],
                        'aggregator_salesman_total' => $aggregator_fee_amount + $record['salesman_commission_amount'],
                        'debit_note_no' => $record['debit_note_no'],
                        'commission_credit_note_no' => $record['commission_credit_note_no'],
                        'incentive_credit_note_no' => $record['incentive_credit_note_no'],
                        'broker_paid_amount' => $record['brokerpaid'],
                        'salesman_paid_amount' => $record['salesmanpaid'],
                        'broker_due' => $record['brokerdue'],
                        'salesman_due' => $record['salesmandue'],
                        'paid_amount' => $record['paid_amount'],
                        'due_amount' => $record['due_amount'],
                        'type' => 'Commission',
                        'datediff'=> $record['datediff'],
                        'documentdatediff'=> $record['documentdatediff'],
                        'total_amount' => $record['total_amount']
                    );
                    $i++;
                }
            
            }
            return $records;
        }
        return false;
    }

    public function getCommissionReportList($postData = null)
    {
        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
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
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        ## Search
        $searchQuery = "";
        if ($searchValue != '')
        {
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.inv_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%' or e.supplier_name like'%" . $searchValue . "%' or f.salesman_name like'%" . $searchValue . "%' or g.broker_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('product_information d', 'd.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        if ($datbetween!='')
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
// var_dump($records);exit;
        
        $totalRecords = $records[0]->allcount;
        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('product_information d', 'd.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.customer_id,b.customer_name,c.*,d.invoice_details_id, d.invoice_id, d.product_id, d.sum_insured, d.premium_amount, d.premium_vat, 
                            d.total_premium_amount, d.gross_commission, d.gross_commission_amount, d.gross_commission_vat, d.total_gross_commission_amount, d.broker_commission, 
                            d.broker_commission_amount, d.aggregator_commission, d.aggregator_commission_amount, d.salesman_commission, d.salesman_commission_amount,
                            d.gross_incentive, d.gross_incentive_amount, d.gross_incentive_vat, d.total_gross_incentive_amount, d.broker_incentive, d.broker_incentive_amount, d.aggregator_incentive, d.aggregator_incentive_amount, d.salesman_incentive, d.salesman_incentive,
                            c.product_name,j.paid_amount,j.due_amount,e.supplier_name,f.salesman_name,g.broker_name, h.paid_amount as salesmanpaid, h.due_amount as salesmandue,
                            ( 
                            case 
                              when 
                                `a`.`premium_paid_date` is not null
                              then timestampdiff( day, `a`.`document_date`, `a`.`premium_paid_date` ) 
                              else 'NOT PAID'
                            end
                          ) as datediff, 
                            ( 
                            case 
                              when 
                                `a`.`document_date` is not null
                              then timestampdiff( day,  `a`.`document_date`, NOW()) 
                              else 0
                            end
                            ) as documentdatediff");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details d', 'd.invoice_id = a.id');
        $this->db->join('product_information c', 'c.product_id = d.product_id');
        $this->db->join('supplier_information e', 'e.supplier_id = a.supplier_id');
        $this->db->join('salesman_information f', 'f.salesman_id = a.salesman_id');
        $this->db->join('salesman_product h', 'h.invoice_id = a.id');
        $this->db->join('broker_information g', 'g.broker_id = a.broker_id');
        $this->db->join('broker_product i', 'i.invoice_id = a.id');
        $this->db->join('customer_product j', 'j.invoice_id = a.id');
        if (!empty($fromdate) && !empty($todate))
        {
            $this->db->where($datbetween);
        }
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->where('d.total_gross_commission_amount >', 0);
        $this->db->group_by('a.inv_id');
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        foreach ($records as $record)
        {
            if($record->gross_incentive != '' && $record->gross_incentive != NULL && $record->gross_incentive != 0.00){
                // $rec[$i] = $record;
                // echo 'oh';exit;
                $button = '';
                $base_url = base_url();
                $customer = $record->customer_name;
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_commission_amount>0) {
                    $aggregator_fee = $record->aggregator_commission;
                    $aggregator_fee_amount =  $record->aggregator_commission_amount;
                // } else {
                    $aggregator_share = $record->aggregator_commission + $record->salesman_commission;
                    $aggregator_share_amount =  $record->aggregator_commission_amount + $record->salesman_commission_amount;
                // }
    
                $rec[] = array(
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => $record->premium_amount,
                    'premium_vat' => $record->premium_vat,
                    'total_premium_amount' => $record->total_premium_amount,
                    'gross_commission' => $record->gross_commission,
                    'gross_commission_amount' => $record->gross_commission_amount,
                    'gross_commission_vat' =>$record->gross_commission_vat,
                    'total_gross_commission_amount' => $record->total_gross_commission_amount,
                    'broker_commission' => $record->broker_commission,
                    'broker_commission_amount' => $record->broker_commission_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_commission,
                    'salesman_commission_amount' => $record->salesman_commission_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_commission_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Commission',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_amount' => $record->total_amount
                );
                // $i++;
                
                
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_incentive_amount>0) {
                    $aggregator_fee = $record->aggregator_incentive;
                    $aggregator_fee_amount =  $record->aggregator_incentive_amount;
                // } else {
                    $aggregator_share = $record->aggregator_incentive + $record->salesman_incentive;
                    $aggregator_share_amount = $record->aggregator_incentive_amount + $record->salesman_incentive_amount;
                // }
                
                $rec[] = array(
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => '',
                    'premium_vat' => '',
                    'total_premium_amount' => '',
                    'gross_commission' => $record->gross_incentive,
                    'gross_commission_amount' => $record->gross_incentive_amount,
                    'gross_commission_vat' =>$record->gross_incentive_vat,
                    'total_gross_commission_amount' => $record->total_gross_incentive_amount,
                    'broker_commission' => $record->broker_incentive,
                    'broker_commission_amount' => $record->broker_incentive_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_incentive,
                    'salesman_commission_amount' => $record->salesman_incentive_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_incentive_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Incentive',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_amount' => $record->total_amount
                );
                // $i++;
            }else{
                
                $button = '';
                $base_url = base_url();
                $customer = $record->customer_name;
                
                $aggregator_share =0;
                $aggregator_share_amount = 0;
                $aggregator_fee=0;
                $aggregator_fee_amount =0;
                
                // if($record->salesman_commission_amount>0) {
                    $aggregator_fee = $record->aggregator_commission;
                    $aggregator_fee_amount =  $record->aggregator_commission_amount;
                // } else {
                    $aggregator_share = $record->aggregator_commission + $record->salesman_commission;
                    $aggregator_share_amount =  $record->aggregator_commission_amount + $record->salesman_commission_amount;
                // }
    
                $rec[] = array(
                    'invoice_id' => $record->inv_id,
                    'date' => date("d/m/Y", strtotime($record->date)),
                    'document_date' => date("d/m/Y", strtotime($record->document_date)),
                    'customer_name' => $customer,
                    'supplier_name' => $record->supplier_name,
                    'salesman_name' => $record->salesman_name,
                    'broker_name' => $record->broker_name,
                    'policy_type' => $record->policy_type,
                    'product_name' => $record->product_name,
                    'policy_no' => $record->policy_no,
                    'endorsement_no' => $record->endorsement_no,
                    'policy_from' => date("d/m/Y", strtotime($record->policy_from))  . ' to ' . date("d/m/Y", strtotime($record->policy_to)),
                    'policy_to' => $record->policy_to,
                    'premium_amount' => $record->premium_amount,
                    'premium_vat' => $record->premium_vat,
                    'total_premium_amount' => $record->total_premium_amount,
                    'gross_commission' => $record->gross_commission,
                    'gross_commission_amount' => $record->gross_commission_amount,
                    'gross_commission_vat' => $record->gross_commission_vat,
                    'total_gross_commission_amount' => $record->total_gross_commission_amount,
                    'broker_commission' => $record->broker_commission,
                    'broker_commission_amount' => $record->broker_commission_amount,
                    'aggregator_share' => $aggregator_share,
                    'aggregator_share_amount' => $aggregator_share_amount,
                    'aggregator_fee' => $aggregator_fee,
                    'aggregator_fee_amount' => $aggregator_fee_amount,
                    'salesman_commission' => $record->salesman_commission,
                    'salesman_commission_amount' => $record->salesman_commission_amount,
                    'aggregator_salesman_total' => $aggregator_fee_amount + $record->salesman_commission_amount,
                    'debit_note_no' => $record->debit_note_no,
                    'commission_credit_note_no' => $record->commission_credit_note_no,
                    'incentive_credit_note_no' => $record->incentive_credit_note_no,
                    'broker_paid_amount' => $record->brokerpaid,
                    'salesman_paid_amount' => $record->salesmanpaid,
                    'broker_due' => $record->brokerdue,
                    'salesman_due' => $record->salesmandue,
                    'paid_amount' => $record->paid_amount,
                    'due_amount' => $record->due_amount,
                    'type' => 'Commission',
                    'datediff'=> $record->datediff,
                    'documentdatediff'=> $record->documentdatediff,
                    'total_amount' => $record->total_amount
                );
                $i++;
            }
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw) ,
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $data
        );

        return $response;
    }
    

    public function referral_list()
    {
        $this->db->select('*');
        $this->db->from('salesman_information');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        return false;
    }
}

