<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#------------------------------------    
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------    

class Parseinvoice extends MX_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        require_once APPPATH . 'libraries/pdfparser-master/alt_autoload.php-dist';
        $this->load->model(array(
            'parseinvoice_model'
        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
        
    }
    
    public function upload_invoice()
    {
        // var_dump($data['supplier']);exit;
        if (isset($_POST['upload_invoice_submit']) && isset($_POST['supplier_id'])) {
            $PDFContent = '';
            $PDFContent1 = '';
            $PDFContent2 = '';
            if (isset($_POST['upload_invoice_submit'])) {
                $policytext = '';
                $invoicetext = '';
                $commissiontext = '';
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
                        $PDFContent = '<p>Only PDF file is allowed to upload.</p>';
                    }
                } else {
                    $PDFContent = '<p>Please select a policy file.</p>';
                }
                echo $policytext;exit;
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
                        $invoiceContent = '<p>Only PDF file is allowed to upload.</p>';
                    }
                } else {
                    $PDFContent1 = '<p>Please select a invoice file.</p>';
                }
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
                        $PDFContent = '<p>Only PDF file is allowed to upload.</p>';
                    }
                } else {
                    $PDFContent2 = '<p>Please select a commission file.</p>';
                }
                $policytext = str_replace(' ', '##', $policytext);
                $invoicetext = str_replace(' ', '##', $invoicetext);
                $commissiontext = str_replace(' ', '##', $commissiontext);
                $data['supplier_id']=$this->input->post('supplier_id');
                if ($this->input->post('supplier_id') == 5 && $policytext != '') {
                    // echo $invoicetext;echo '<br>';
                    // echo $commissiontext;echo '<br>';
                    // $policytext = preg_replace('/[^A-Za-z0-9,.-]/', '', $policytext);
                    
                    $data['policy_no'] = preg_replace('/[^A-Za-z0-9,. ]/', '####', $this->between ('Policy##Number', 'Policy##Issuance##Date', $policytext));
                    $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                    var_dump($data['policy_no']);echo '<br>';
                    
                    $data['document_date']= date("Y-m-d", strtotime(str_replace('##', '-', $this->between ('Policy##Issuance##Date', 'Insurance##Period', $policytext))));
                    var_dump($data['document_date']);echo '<br>';
                    
                    $data['policy_from']= date("Y-m-d", strtotime(str_replace('##', '-', $this->between ('Insurance##Period', '####', $policytext))));
                    var_dump($data['policy_from']);echo '<br>';
                    
                    $data['policy_to']= date("Y-m-d", strtotime(str_replace('##', '-', $this->between ('-####', '####', $policytext))));
                    var_dump($data['policy_to']);echo '<br>';
                    
                    $data['customer_name']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('Insured##Name', 'Date##of##Birth', $policytext)));
                    $data['customer_name'] = str_replace('####', '', $data['customer_name']);
                    var_dump($data['customer_name']);echo '<br>';
                    
                    $data['sum_insured']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('Sum##Insured', 'Financed##by', $policytext)));
                    $data['sum_insured'] = str_replace('####', '', $data['sum_insured']);
                    var_dump($data['sum_insured']);echo '<br>';
                    
                    if($invoicetext != '') {
                        $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Total##Amount', 'Total##Amount', $invoicetext));
                        $resp = str_replace('####', '', $resp);
                        $totals = explode('##',$resp);
                        
                        $data['premium_amount'] = str_replace(',', '', $totals[1]);
                        $data['premium_vat'] = str_replace(',', '', $totals[2]);
                        $data['total_premium_amount'] = str_replace(',', '', $totals[3]);
                        $data['grand_total_price'] = str_replace(',', '', $totals[3]);
                    }
                    
                    if($commissiontext != '') {
                        $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Qty', 'Unit##price', $commissiontext));
                        $resp = str_replace('####', '', $resp);
                        $totals = explode('##',$resp);
                        
                        $data['gross_commission_amount'] = $totals[1];
                        $data['gross_commission_vat'] = $totals[2];
                        $data['total_gross_commission_amount'] = $totals[3];
                        
                    }
                    // exit;
                    $data['supplier'] = $this->parseinvoice_model->supplier_list();
                    $data['broker'] = $this->parseinvoice_model->broker_list();
                    $data['salesman'] = $this->parseinvoice_model->salesman_list();
                    
                    $data['module']        = "parseinvoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 14 && $policytext != '') {
                    
                    $data['customer_name']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('NAME##OF##INSURED', 'ADDRESS:', $policytext)));
                    $data['customer_name'] = str_replace('####', '', $data['customer_name']);
                    var_dump($data['customer_name']);echo '<br>';
                    
                    $data['policy_no'] = preg_replace('/[^A-Za-z0-9,.-]/', '####', $this->between ('POLICY##NO:', 'CERTIFICATE##NO', $policytext));
                    $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                    var_dump($data['policy_no']);echo '<br>';
                    
                    
                    $resp= $this->between ('DATE##OF##ISSUE:', '##', $policytext);
                    $da = explode('/',$resp);
                    $data['document_date']= $da[2] .'-'. $da[1] .'-'. $da[0];
                    var_dump($data['document_date']);echo '<br>';
                    
                    $data['premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Commission##&##all##allowance):AED##', 'PERIOD##OF##INSURANCE', $policytext));
                    // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                    var_dump($data['premium_amount']);echo '<br>';
                    
                    
                    $data['premium_vat'] = preg_replace('/[^A-Za-z0-9,.]/', '####', str_replace('####', '', $this->between ('VAT@5%####', 'Premium##Payable', $policytext)));
                    $data['premium_vat'] = str_replace('####', '', $data['premium_vat']);
                    var_dump($data['premium_vat']);echo '<br>';
                    
                    $data['total_premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Premium##Payable##################', '##:
:AED', $policytext));
                    $data['total_premium_amount'] = str_replace('####', '', $data['total_premium_amount']);
                    $data['grand_total_price'] = str_replace('####', '', $data['total_premium_amount']);
                    var_dump($data['total_premium_amount']);echo '<br>';
                    
                    // $data['policy_from']= date("Y-m-d", strtotime($this->between ('PERIOD##OF##INSURANCE##:##FROM########', '##', $policytext)));
                    // var_dump($data['policy_from']);echo '<br>';
                    
                    // $data['policy_to']= date("Y-m-d", strtotime($this->between ('HrsTO', '##', $policytext)));
                    // var_dump($data['policy_to']);echo '<br>';
                    
                    // $data['sum_insured']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('Sum##Insured', 'Financed##by', $policytext)));
                    // $data['sum_insured'] = str_replace('####', '', $data['sum_insured']);
                    // var_dump($data['sum_insured']);echo '<br>';
                    
                    if($invoicetext != '') {
                    
                        $resp= $this->between ('PERIOD##OF##INSURANCE##:##FROM########', '##', $invoicetext);
                        $da = explode('/',$resp);
                        $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        var_dump($data['policy_from']);echo '<br>';
                        
                        $resp= $this->between ('########To######', '##', $invoicetext);
                        $da = explode('/',$resp);
                        $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        var_dump($data['policy_to']);echo '<br>';
                    
                        $data['debit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $invoicetext));
                        $data['debit_note_no'] = str_replace('####', '', $data['debit_note_no']);
                        var_dump($data['debit_note_no']);echo '<br>';
                    
                        $data['policy_type'] = preg_replace('/[^A-Za-z0-9,.#]/', '####', $this->between ('POLICY##TYPE', 'CERTIFICATE', $invoicetext));
                        $data['policy_type'] = str_replace('##', ' ', $data['policy_type']);
                        var_dump($data['policy_type']);echo '<br>';
                    
                        $data['debit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $invoicetext));
                        $data['debit_note_no'] = str_replace('####', '', $data['debit_note_no']);
                        var_dump($data['debit_note_no']);echo '<br>';
                        
                        // $data['premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Pol##No.##'.$data['policy_no'].'##', '##END', $invoicetext));
                        // // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        // var_dump($data['premium_amount']);echo '<br>';
                    
                    
                        // $data['premium_vat'] = preg_replace('/[^A-Za-z0-9,.]/', '####', str_replace('####', '', $this->between ('ONLY##', 'Total##', $policytext)));
                        // $data['premium_vat'] = str_replace('####', '', $data['premium_vat']);
                        // var_dump($data['premium_vat']);echo '<br>';
                        
                    
                        
                        // $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Total##Amount', 'Total##Amount', $invoicetext));
                        // $resp = str_replace('####', '', $resp);
                        // $totals = explode('##',$resp);
                        
                        // $data['premium_amount'] = str_replace(',', '', $totals[1]);
                        // $data['premium_vat'] = str_replace(',', '', $totals[2]);
                        // $data['premium_basmah'] = str_replace(',', '', $totals[3]);
                    }
                    
                    if($commissiontext != '') {
                    
                        $data['commission_credit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $commissiontext));
                        $data['commission_credit_note_no'] = str_replace('####', '', $data['commission_credit_note_no']);
                        var_dump($data['commission_credit_note_no']);echo '<br>';
                        
                        $data['gross_commission_amount'] = $this->between ('Commission##on##'.$data['policy_no'].'####', '##END', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        var_dump($data['gross_commission_amount']);echo '<br>';
                        
                        $data['gross_commission_vat'] = $this->after ('VAT@##5%######', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        var_dump($data['gross_commission_vat']);echo '<br>';
                        
                        $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Qty', 'Unit##price', $commissiontext));
                        $resp = str_replace('####', '', $resp);
                        $totals = explode('##',$resp);
                        
                        // $data['gross_commission_amount'] = $totals[1];
                        // $data['gross_commission_vat'] = $totals[2];
                        // $data['total_gross_commission_amount'] = $totals[3];
                        
                        $data['total_gross_commission_amount'] = $this->between ('##DUE##', 'Amount##', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        var_dump($data['total_gross_commission_amount']);echo '<br>';
                        
                    }
                    // exit;
                    
                    $data['supplier'] = $this->parseinvoice_model->supplier_list();
                    $data['broker'] = $this->parseinvoice_model->broker_list();
                    $data['salesman'] = $this->parseinvoice_model->salesman_list();
                    $data['module']        = "parseinvoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } elseif ($this->input->post('supplier_id') == 8 && $policytext != '') {
                    echo $policytext;
                    echo '<br>';
                    echo '<br>';
                    echo '<br>';
                    // echo $invoicetext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // echo $commissiontext;
                    // echo '<br>';
                    // echo '<br>';
                    // echo '<br>';
                    // exit;
                    
                    // var_dump($policytext);echo '<br>';echo '<br>';echo '<br>';
                    
                    $data['policy_no'] = $this->getBetween($invoicetext,'Promotion##scheme##Policy##No.##', '####');
                    $data['policy_no'] = preg_replace('/[^A-Za-z0-9,.\/]/', '', $data['policy_no']);
                    var_dump($data['policy_no']);echo '<br>';
                    
                    $resp= $this->between ($data['policy_no'], '##to##', $invoicetext);
                    $data['policy_from']= date("Y-m-d", strtotime($resp));
                    var_dump($data['policy_from']);echo '<br>';
                    
                    $data['policy_to']= date("Y-m-d", strtotime($this->between ('##to##', 'Policy##', $invoicetext)));
                    var_dump($data['policy_to']);echo '<br>';
                    
                    $data['policy_no']= $this->between ($data['policy_no'].'##', '##', $policytext);
                    var_dump($data['policy_no']);echo '<br>';
                    
                    $data['customer_name']= str_replace('##', ' ', $this->between ('Name##of##Insured##', '##ﻪ', $policytext));
                    var_dump($data['customer_name']);echo '<br>';
                    
                    $data['document_date']= date("Y-m-d", strtotime($this->between ('Tax##Invoice##No##', 'Tax##Invoice##', $invoicetext)));
                    var_dump($data['document_date']);echo '<br>';
                    
                    $texts= $this->between ('Total##Agreed', '####(Sub', $policytext);
                    $texts =preg_replace('/[^A-Za-z0-9,.\/#+-=]/', '', $texts);
                    // var_dump($texts);echo '<br>';
                    
                    $data['sum_insured']= $this->between ('##Premium##AED##', '##/-', $texts);
                    var_dump($data['sum_insured']);echo '<br>';
                    
                    $data['premium_amount']= $this->between ('##/-AED##', '##/-##+##', $texts);
                    var_dump($data['premium_amount']);echo '<br>';
                    
                    $data['premium_vat']= $this->between ('VAT####', '####=##', $texts);
                    var_dump($data['premium_vat']);echo '<br>';
                    
                    $data['total_premium_amount']= $this->between ('####=##AED##', '##/-', $texts);
                    var_dump($data['total_premium_amount']);echo '<br>';
                    
                    $data['grand_total_price']= $this->between ('####=##AED##', '##/-', $texts);
                    var_dump($data['grand_total_price']);echo '<br>';exit;
                    
//                     $resp= $this->between ('PERIOD##OF##INSURANCE##:##FROM########', '##', $invoicetext);
//                     $da = explode('/',$resp);
//                     $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
//                     var_dump($data['policy_from']);echo '<br>';
                    
//                     // $data['policy_no'] = preg_replace('/[^A-Za-z0-9,.\/: ]/', '*', $this->between ('Policy##Period##', '##to##', $policytext));
//                     // $data['policy_no'] = str_replace('*******************************', '%%', $data['policy_no']);
//                     // $data['policy_no'] = $this->between ('%%', '*', $data['policy_no']);
//                     // var_dump($data['policy_no']);echo '<br>';
                    
                    
//                     $data['customer_name']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('NAME##OF##INSURED', 'ADDRESS:', $policytext)));
//                     $data['customer_name'] = str_replace('####', '', $data['customer_name']);
//                     var_dump($data['customer_name']);echo '<br>';
                    
                    
//                     $resp= $this->between ('DATE##OF##ISSUE:', '##', $policytext);
//                     $da = explode('/',$resp);
//                     $data['document_date']= $da[2] .'-'. $da[1] .'-'. $da[0];
//                     var_dump($data['document_date']);echo '<br>';
                    
//                     $data['premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Commission##&##all##allowance):AED##', 'PERIOD##OF##INSURANCE', $policytext));
//                     // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
//                     var_dump($data['premium_amount']);echo '<br>';
                    
                    
//                     $data['premium_vat'] = preg_replace('/[^A-Za-z0-9,.]/', '####', str_replace('####', '', $this->between ('VAT@5%####', 'Premium##Payable', $policytext)));
//                     $data['premium_vat'] = str_replace('####', '', $data['premium_vat']);
//                     var_dump($data['premium_vat']);echo '<br>';
                    
//                     $data['total_premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Premium##Payable##################', '##:
// :AED', $policytext));
//                     $data['total_premium_amount'] = str_replace('####', '', $data['total_premium_amount']);
//                     var_dump($data['total_premium_amount']);echo '<br>';
                    
                    // $data['policy_from']= date("Y-m-d", strtotime($this->between ('PERIOD##OF##INSURANCE##:##FROM########', '##', $policytext)));
                    // var_dump($data['policy_from']);echo '<br>';
                    
                    // $data['policy_to']= date("Y-m-d", strtotime($this->between ('HrsTO', '##', $policytext)));
                    // var_dump($data['policy_to']);echo '<br>';
                    
                    // $data['sum_insured']= preg_replace('/[^A-Za-z0-9,. ]/', '####', str_replace('##', ' ', $this->between ('Sum##Insured', 'Financed##by', $policytext)));
                    // $data['sum_insured'] = str_replace('####', '', $data['sum_insured']);
                    // var_dump($data['sum_insured']);echo '<br>';
                    
                    if($invoicetext != '') {
                    
                        $resp= $this->between ('PERIOD##OF##INSURANCE##:##FROM########', '##', $invoicetext);
                        $da = explode('/',$resp);
                        $data['policy_from']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        var_dump($data['policy_from']);echo '<br>';
                        
                        $resp= $this->between ('########To######', '##', $invoicetext);
                        $da = explode('/',$resp);
                        $data['policy_to']= $da[2] .'-'. $da[1] .'-'. $da[0];
                        var_dump($data['policy_to']);echo '<br>';
                    
                        $data['debit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $invoicetext));
                        $data['debit_note_no'] = str_replace('####', '', $data['debit_note_no']);
                        var_dump($data['debit_note_no']);echo '<br>';
                    
                        $data['policy_type'] = preg_replace('/[^A-Za-z0-9,.#]/', '####', $this->between ('POLICY##TYPE', 'CERTIFICATE', $invoicetext));
                        $data['policy_type'] = str_replace('##', ' ', $data['policy_type']);
                        var_dump($data['policy_type']);echo '<br>';
                    
                        $data['debit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $invoicetext));
                        $data['debit_note_no'] = str_replace('####', '', $data['debit_note_no']);
                        var_dump($data['debit_note_no']);echo '<br>';
                        
                        // $data['premium_amount'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('Pol##No.##'.$data['policy_no'].'##', '##END', $invoicetext));
                        // // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        // var_dump($data['premium_amount']);echo '<br>';
                    
                    
                        // $data['premium_vat'] = preg_replace('/[^A-Za-z0-9,.]/', '####', str_replace('####', '', $this->between ('ONLY##', 'Total##', $policytext)));
                        // $data['premium_vat'] = str_replace('####', '', $data['premium_vat']);
                        // var_dump($data['premium_vat']);echo '<br>';
                        
                    
                        
                        // $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Total##Amount', 'Total##Amount', $invoicetext));
                        // $resp = str_replace('####', '', $resp);
                        // $totals = explode('##',$resp);
                        
                        // $data['premium_amount'] = str_replace(',', '', $totals[1]);
                        // $data['premium_vat'] = str_replace(',', '', $totals[2]);
                        // $data['premium_basmah'] = str_replace(',', '', $totals[3]);
                    }
                    
                    if($commissiontext != '') {
                    
                        $data['commission_credit_note_no'] = preg_replace('/[^A-Za-z0-9,.]/', '####', $this->between ('DOCUMENT##NO', 'DATE', $commissiontext));
                        $data['commission_credit_note_no'] = str_replace('####', '', $data['commission_credit_note_no']);
                        var_dump($data['commission_credit_note_no']);echo '<br>';
                        
                        $data['gross_commission_amount'] = $this->between ('Commission##on##'.$data['policy_no'].'####', '##END', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        var_dump($data['gross_commission_amount']);echo '<br>';
                        
                        $data['gross_commission_vat'] = $this->after ('VAT@##5%######', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        var_dump($data['gross_commission_vat']);echo '<br>';
                        
                        $resp = preg_replace('/[^A-Za-z0-9,. ]/', '##', $this->between ('Qty', 'Unit##price', $commissiontext));
                        $resp = str_replace('####', '', $resp);
                        $totals = explode('##',$resp);
                        
                        // $data['gross_commission_amount'] = $totals[1];
                        // $data['gross_commission_vat'] = $totals[2];
                        // $data['total_gross_commission_amount'] = $totals[3];
                        
                        $data['total_gross_commission_amount'] = $this->between ('##DUE##', 'Amount##', $commissiontext);
                        // $data['policy_no'] = str_replace('####', '', $data['policy_no']);
                        var_dump($data['total_gross_commission_amount']);echo '<br>';
                        
                    }
                    // exit;
                    
                    $data['supplier'] = $this->parseinvoice_model->supplier_list();
                    $data['broker'] = $this->parseinvoice_model->broker_list();
                    $data['salesman'] = $this->parseinvoice_model->salesman_list();
                    $data['module']        = "parseinvoice";
                    $data['page']          = "add_invoice_form"; 
                    echo modules::run('template/layout', $data);
                    exit;
                } else {
                }
                // Display content
                echo $PDFContent;
                exit;
            }
        } else {
            
            $data['supplier'] = $this->parseinvoice_model->supplier_list();
            $data['broker'] = $this->parseinvoice_model->broker_list();
            $data['salesman'] = $this->parseinvoice_model->salesman_list();
            $data['title']  = 'Invoice Parser';
            $data['module'] = "parseinvoice";
            $data['page']   = "invoice_upload";
            echo Modules::run('template/layout', $data);
        }
    }
    function after ($first, $second)
    {
        if (!is_bool(strpos($second, $first)))
        return substr($second, strpos($second,$first)+strlen($first));
    }

    function after_last ($first, $second)
    {
        if (!is_bool(strrevpos($second, $first)))
        return substr($second, strrevpos($second, $first)+strlen($first));
    }

    function before ($first, $second)
    {
        return substr($second, 0, strpos($second, $first));
    }
    function between ($first, $that, $second)
    {
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
    public function cloudsubset_update_salesman_commission($id)
    {
        $this->form_validation->set_rules('salesman_pr_id', display('salesman_pr_id'), 'required');
        // $this->form_validation->set_rules('salesman_id', display('salesman_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount'), 'required');
        $this->form_validation->set_rules('due_amount_new', display('due_amount'), 'required');
        $this->form_validation->set_rules('paid_amount_new', display('paid_amount'), 'required');
        
        
        if ($this->form_validation->run() === true) {
            $update = $this->payment_model->salesman_commission_update($id);
            
            if (!empty($update)) {
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['salesman_commission'] = $this->payment_model->salesman_commission_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            } else {
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
            
        } else {
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("salesman_commission_edit/" . $id);
    }
    
    public function cloudsubset_broker_commission()
    {
        $data['broker'] = $this->payment_model->broker_list();
        $data['title']  = 'Salesman Commission';
        $data['module'] = "payment";
        $data['page']   = "broker_form";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_broker_commission_search()
    {
        if ($this->input->post('broker_id') != '') {
            $broker_id = trim($this->input->post('broker_id', TRUE));
            $broker    = $this->db->select('*')->from('broker_information')->where('broker_id', $broker_id)->get()->row();
            
            $config["base_url"]         = base_url('broker_commission_search/');
            $config["total_rows"]       = $this->payment_model->broker_commission_list_count($broker_id);
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
            $page          = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
            $data["links"] = $this->pagination->create_links();
            
            
            $data['broker_id']          = $broker_id;
            $data['broker']             = $broker;
            $data['broker_commissions'] = $this->payment_model->broker_commission_list($broker_id, $config["per_page"], $page);
            $data['module']             = "payment";
            $data['page']               = "broker_commission_list";
            echo Modules::run('template/layout', $data);
        } else {
            redirect('broker_commission_search');
        }
    }
    
    public function cloudsubset_broker_commission_edit($id)
    {
        $data['broker_commission']       = $this->payment_model->broker_commission_details($id);
        $data['broker_commission_dates'] = $this->payment_model->broker_commission_date_details($data['broker_commission']->invoice_id);
        $data['title']                   = 'Salesman Commission';
        $data['module']                  = "payment";
        $data['page']                    = "broker_commission_edit";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_update_broker_commission($id)
    {
        $this->form_validation->set_rules('broker_pr_id', display('broker_pr_id'), 'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount'), 'required');
        $this->form_validation->set_rules('due_amount_new', display('due_amount'), 'required');
        $this->form_validation->set_rules('paid_amount_new', display('paid_amount'), 'required');
        
        
        if ($this->form_validation->run() === true && $this->input->post('paid_amount') > 0) {
            $update = $this->payment_model->broker_commission_update($id);
            
            if (!empty($update)) {
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['broker_commission'] = $this->payment_model->broker_commission_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            } else {
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
            
        } else {
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("broker_commission_edit/" . $id);
    }
    
    public function cloudsubset_supplier_payment()
    {
        $data['supplier'] = $this->payment_model->supplier_list();
        $data['title']    = 'Salesman Commission';
        $data['module']   = "payment";
        $data['page']     = "supplier_form";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_supplier_payment_search()
    {
        if ($this->input->post('supplier_id') != '') {
            $supplier_id = trim($this->input->post('supplier_id', TRUE));
            $supplier    = $this->db->select('*')->from('supplier_information')->where('supplier_id', $supplier_id)->get()->row();
            
            $config["base_url"]         = base_url('supplier_payment_search/');
            $config["total_rows"]       = $this->payment_model->supplier_payment_list_count($supplier_id);
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
            $page          = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
            $data["links"] = $this->pagination->create_links();
            
            
            $data['supplier_id']       = $supplier_id;
            $data['supplier']          = $supplier;
            $data['supplier_payments'] = $this->payment_model->supplier_payment_list($supplier_id, $config["per_page"], $page);
            $data['module']            = "payment";
            $data['page']              = "supplier_payment_list";
            echo Modules::run('template/layout', $data);
        } else {
            redirect('supplier_payment_search');
        }
    }
    
    public function cloudsubset_supplier_payment_edit($id)
    {
        $data['supplier_payment']       = $this->payment_model->supplier_payment_details($id);
        $data['supplier_payment_dates'] = $this->payment_model->supplier_payment_date_details($data['supplier_payment']->invoice_id);
        $data['title']                  = 'Salesman Commission';
        $data['module']                 = "payment";
        $data['page']                   = "supplier_payment_edit";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_update_supplier_payment($id)
    {
        $this->form_validation->set_rules('supplier_pr_id', display('supplier_pr_id'), 'required');
        // $this->form_validation->set_rules('supplier_id', display('supplier_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount'), 'required');
        $this->form_validation->set_rules('due_amount_new', display('due_amount'), 'required');
        $this->form_validation->set_rules('paid_amount_new', display('paid_amount'), 'required');
        
        
        if ($this->form_validation->run() === true) {
            $update = $this->payment_model->supplier_payment_update($id);
            
            if (!empty($update)) {
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['supplier_payment'] = $this->payment_model->supplier_payment_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            } else {
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
            
        } else {
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("supplier_payment_edit/" . $id);
    }
    
    public function cloudsubset_customer_payment()
    {
        $data['customer'] = $this->payment_model->customer_list();
        $data['title']    = 'Salesman Commission';
        $data['module']   = "payment";
        $data['page']     = "customer_form";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_customer_payment_search()
    {
        if ($this->input->post('customer_id') != '') {
            $customer_id = trim($this->input->post('customer_id', TRUE));
            $customer    = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
            
            $config["base_url"]         = base_url('customer_payment_search/');
            $config["total_rows"]       = $this->payment_model->customer_payment_list_count($customer_id);
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
            $page          = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
            $data["links"] = $this->pagination->create_links();
            
            
            $data['customer_id']       = $customer_id;
            $data['customer']          = $customer;
            $data['customer_payments'] = $this->payment_model->customer_payment_list($customer_id, $config["per_page"], $page);
            $data['module']            = "payment";
            $data['page']              = "customer_payment_list";
            echo Modules::run('template/layout', $data);
        } else {
            redirect('customer_payment_search');
        }
    }
    
    public function cloudsubset_customer_payment_edit($id)
    {
        $data['customer_payment']       = $this->payment_model->customer_payment_details($id);
        $data['customer_payment_dates'] = $this->payment_model->customer_payment_date_details($data['customer_payment']->invoice_id);
        $data['title']                  = 'Salesman Commission';
        $data['module']                 = "payment";
        $data['page']                   = "customer_payment_edit";
        echo Modules::run('template/layout', $data);
    }
    
    public function cloudsubset_update_customer_payment($id)
    {
        $this->form_validation->set_rules('customer_pr_id', display('customer_pr_id'), 'required');
        // $this->form_validation->set_rules('customer_id', display('customer_id') ,'required');
        $this->form_validation->set_rules('paid_amount', display('paid_amount'), 'required');
        $this->form_validation->set_rules('due_amount_new', display('due_amount'), 'required');
        $this->form_validation->set_rules('paid_amount_new', display('paid_amount'), 'required');
        
        
        if ($this->form_validation->run() === true) {
            $update = $this->payment_model->customer_payment_update($id);
            
            if (!empty($update)) {
                // $data['status'] = true;
                // $data['message'] = display('update_successfully');
                $data['customer_payment'] = $this->payment_model->customer_payment_details($id);
                
                $this->session->set_flashdata('message', display('update_successfully'));
            } else {
                // $data['status'] = false;
                // $data['exception'] = 'Please Try Again';
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
            
        } else {
            // $data['status'] = false;
            // $data['exception'] = validation_errors();  
            $this->session->set_flashdata('exception', validation_errors());
        }
        redirect("customer_payment_edit/" . $id);
    }
    
    public function payment_invoice()
    {
        $finyear = $this->input->post('finyear', true);
        
        if ($finyear <= 0) {
            $this->session->set_flashdata('exception', 'Please Create Financial Year First');
            redirect("payment_form");
        } else {
            
            $invoice_id = $this->payment_model->payment_invoice_entry();
            if ($invoice_id) {
                $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
                if ($setting_data[0]['is_autoapprove_v'] == 1) {
                    
                    $new = $this->autoapprove($invoice_id);
                }
                
                
                $this->session->set_flashdata(array(
                    'message' => display('successfully_added')
                ));
                
                redirect("invoice_payment_details/" . $invoice_id);
            }
        }
    }
    
    public function autoapprove($invoice_id)
    {
        
        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $invoice_id)->where('status', 0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
        
    }
}