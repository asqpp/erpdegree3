<link href="<?php echo base_url('assets/css/return.css') ?>" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url() ?>my-assets/js/admin_js/return.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>my-assets/js/admin_js/invoice_return.js" type="text/javascript"></script>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('return_invoice') ?></h4>
                        </div>
                    </div>
                    <?php echo form_open('return/returns/return_invoice', array('class' => 'form-vertical', 'id' => 'returnForm')) ?>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12" id="payment_from_1">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label"><?php echo display('customer_name') ?> <i class="text-danger"></i></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="customer_name" value="<?php echo $customer_name?>" class="form-control customerSelection" placeholder='<?php echo display('customer_name') ?>' required id="customer_name" tabindex="1" readonly="">
        
                                                <input type="hidden" class="customer_hidden_value" name="customer_id" value="<?php echo $customer_id?>"/>
                                                <input type="hidden" name="salesman_id" value="<?php echo $salesman_id?>"/>
                                                <input type="hidden" name="supplier_id" value="<?php echo $supplier_id?>"/>
                                                <input type="hidden" name="broker_id" value="<?php echo $broker_id?>"/>
                                            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label"><?php echo display('date') ?> <i class="text-danger"></i></label>
                                            <div class="col-sm-8">
                                                <input type="text" tabindex="2" class="form-control" name="invoice_date" value="<?php echo $date?>"  required readonly="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label">Product</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="product_name"
                                                    value="<?php echo $invoice_all_data[0]['product_name']?>"
                                                    class="form-control" required readonly="" >
                                            <input type="hidden"
                                                class="product_id autocomplete_hidden_value"
                                                name="product_id" value="<?php echo $invoice_all_data[0]['product_id']?>"
                                                id="SchoolHiddenId" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label">Policy No:<i class="text-danger"></i></label>
                                            <div class="col-sm-8">
                                                <input type="text" tabindex="2" class="form-control" name="policy_no" value="<?php echo $policy_no?>"  required readonly="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label">Endorsement No:<i class="text-danger"></i></label>
                                            <div class="col-sm-8">
                                                <input type="text" tabindex="2" class="form-control" name="endorsement_no" value="<?php echo $endorsement_no?>"  required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label">Debit Note No :<i class="text-danger"></i></label>
                                            <div class="col-sm-8">
                                                <input type="text" tabindex="2" class="form-control" name="return_debit_note_no" value="<?php echo $return_debit_note_no?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <label for="product_name" class="col-sm-4 col-form-label">Credit Note No:<i class="text-danger"></i></label>
                                            <div class="col-sm-8">
                                                <input type="text" tabindex="2" class="form-control" name="return_credit_note_no" value="<?php echo $return_credit_note_no?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 table-responsive">
                            
                                <table class="table table-bordered table-hover" id="normalinvoice">
                                <thead>
                                        <tr>
                                            <th colspan="5" class="text-center">Premium</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Policy/ICP Charge</th>
                                            <th class="text-center">VAT</th>
                                            <th class="text-center">Basmah Fees</th>
                                            <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="addinvoiceItem">
        
                                    <?php
                                    foreach($invoice_all_data as $details){?>
                                    <tr>
                                        <td>
                                           <?php echo $details['premium_amount']?>
                                        </td>
                                        <td>
                                           <?php echo $details['premium_policy']?>
                                        </td>
                                        <td>
                                           <?php echo $details['premium_vat']?>
                                        </td>
                                        <td>
                                           <?php echo $details['premium_basmah']?>
                                        </td>
                                        <td>
                                           <?php echo $details['total_premium_amount']?>
                                        </td>
                                    </tr>
                                    <?php }?>
                                     <tr>
                                        <td colspan="2" class="text-center"><strong>Gross Incentive</strong></td>
                                        <td rowspan="2" class="text-center"><strong>Discount</strong></td>
                                        <td colspan="2" rowspan="2" class="text-center"><strong>Grant total</strong></td>
                                     </tr>
                                     <tr>
                                        <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                        <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                     </tr>
                                     <tr>
                                        <td>
                                           <?php echo $details['gross_incentive']?>
                                        </td>
                                        <td>
                                            <?php echo $details['gross_incentive_amount']?>
                                        </td>
                                        <td>
                                            <?php echo $details['total_discount']?>
                                        </td>
                                        <td colspan="2">
                                            <?php echo $details['total_amount']?>
                                        </td>
                                     </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>


                       <!-- ret part -->
                       
                       <!-- ret part end -->
                        <div class="table-responsive">
                            
                            <table class="table table-bordered table-hover" id="normalinvoice" style="font-size: 11px;">
                                <thead>
                                        <tr>
                                            <th colspan="5" class="text-center">Premium</th>
                                            <th colspan="4" class="text-center">Gross Commission</th>
                                            <th colspan="2" class="text-center">Broker Commission</th>
                                            <th colspan="2" class="text-center">Aggregator Fees</th>
                                            <th colspan="2" class="text-center">Salesman Commission</th>
                                            <th rowspan="2" class="text-center"><?php echo display('check_return') ?> <i class="text-danger">*</i></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Policy/ICP Charge</th>
                                            <th class="text-center">VAT</th>
                                            <th class="text-center">Basmah Fees</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">%</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">VAT</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">%</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">%</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">%</th>
                                            <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="addinvoiceItem">
        
                                    <?php
                                    foreach($invoice_all_data as $details){?>
                                    <tr>
                                        <td>
                                            
                                           <input type="text" name="premium_amount" id="premium_amount_1" class="form-control text-left premium_amount" tabindex="6" value="<?php echo $details['premium_amount']?>" />
                                           <input type="hidden"  readonly="" name="premium_amount_dummy" id="premium_amount_dummy" class="form-control text-left premium_amount_dummy" tabindex="6" value="<?php echo $details['total_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text"  name="premium_policy" id="premium_policy_1" class="form-control text-left premium_policy" tabindex="6" value="<?php echo $details['premium_policy']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="premium_vat" id="premium_vat_1" class="form-control text-left premium_vat" tabindex="6" value="<?php echo $details['premium_vat']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="premium_basmah" id="premium_basmah_1" class="form-control text-left premium_basmah" tabindex="6" value="<?php echo $details['premium_basmah']?>" />
                                        </td>
                                        <td>
                                            <input type="text"  name="total_premium_amount" id="total_premium_amount_1" class="form-control text-left total_premium_amount" tabindex="6" value="<?php echo $details['total_premium_amount']?>" />
                                            <input type="hidden" readonly="" name="total_premium_amount_t" value="<?php echo $details['total_premium_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text"  name="gross_commission" id="gross_commission_1" class="form-control text-left gross_commission" tabindex="6" value="<?php echo $details['gross_commission']?>" />
                                        </td>
                                        <td>
                                           <input type="text"  name="gross_commission_amount" id="gross_commission_amount_1" class="form-control text-left gross_commission_amount" tabindex="6" value="<?php echo $details['gross_commission_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text"  name="gross_commission_vat" id="gross_commission_vat_1" class="form-control text-left gross_commission_vat" tabindex="6" value="<?php echo $details['gross_commission_vat']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="total_gross_commission_amount" id="total_gross_commission_amount_1" class="form-control text-left total_gross_commission_amount" tabindex="6" value="<?php echo $details['total_gross_commission_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="broker_commission" id="broker_commission_1" class="form-control text-left broker_commission" tabindex="6" value="<?php echo $details['broker_commission']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="broker_commission_amount" id="broker_commission_amount_1" class="form-control text-left broker_commission_amount" tabindex="6" value="<?php echo $details['broker_commission_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="aggregator_commission" id="aggregator_commission_1" class="form-control text-left aggregator_commission" tabindex="6" value="<?php echo $details['aggregator_commission']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="aggregator_commission_amount" id="aggregator_commission_amount_1" class="form-control text-left aggregator_commission_amount" tabindex="6" value="<?php echo $details['aggregator_commission_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="salesman_commission" id="salesman_commission_1" class="form-control text-left salesman_commission" tabindex="6" value="<?php echo $details['salesman_commission']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="salesman_commission_amount" id="salesman_commission_amount_1" class="form-control text-left salesman_commission_amount" tabindex="6" value="<?php echo $details['salesman_commission_amount']?>" />
                                        </td>
                                        <td>
                                            <input type="checkbox" name='rtn' onclick="checkboxcheck(<?php echo $details['sl']?>)" id="check_id_<?php echo $details['sl']?>" value="<?php echo $details['sl']?>" class="chk" >
                                        </td>
                                        <!--<td>-->
                                        <!--    <input type="text" name="discount[]"-->
                                        <!--        onkeyup="cloudsubset_invoice_quantity_calculate(<?php echo $details['sl']?>);"-->
                                        <!--        onchange="(<?php echo $details['sl']?>);"-->
                                        <!--        id="discount_<?php echo $details['sl']?>" class="form-control text-right"-->
                                        <!--        placeholder="0.00" value="<?php echo $details['discount_per']?>" min="0"-->
                                        <!--        tabindex="6" />-->
        
                                        <!--    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type"-->
                                        <!--        id="discount_type_<?php echo $details['sl']?>">-->
                                        <!--</td>-->
                                        <!--<td>-->
                                        <!--    <input type="text" name="discountvalue[]"-->
                                        <!--        id="discount_value_<?php echo $details['sl']?>" class="form-control  text-right"-->
                                        <!--        min="0" tabindex="18" placeholder="0.00"-->
                                        <!--        value="<?php echo $details['discount']?>" readonly />-->
                                        <!--</td>-->
                                        <!--<td>-->
        
                                        <!--    <input type="hidden" id="total_discount_<?php echo $details['sl']?>" class=""-->
                                        <!--        value="<?php echo $details['discount']?>" />-->
        
                                        <!--    <input type="hidden" id="all_discount_<?php echo $details['sl']?>"-->
                                        <!--        class="total_discount dppr" value="<?php echo $details['discount']?>"-->
                                        <!--        name="discount_amount[]" />-->
        
                                        <!--</td>-->
                                    </tr>
                                    <?php }?>
                                     <tr>
                                        <td colspan="5"></td>
                                        <td colspan="4" class="text-center"><strong>Gross Incentive</strong></td>
                                        <td colspan="2" class="text-center"><strong>Broker Incentive</strong></td>
                                        <td colspan="2" class="text-center"><strong>Aggregator Incentive</strong></td>
                                        <td colspan="2" class="text-center"><strong>Salesman Incentive</strong></td>
                                     </tr>
                                     <tr>
                                        <td colspan="5"></td>
                                        <td colspan="2" class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                            <td colspan="2" class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                        <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                        <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                        <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                        <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                        <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                        <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                     </tr>
                                     <tr>
                                        <td colspan="5"></td>
                                        <td colspan="2">
                                           <input type="text" name="gross_incentive" id="gross_incentive_1" class="form-control text-left gross_incentive" tabindex="6" value="<?php echo $details['gross_incentive']?>" />
                                        </td>
                                        <td colspan="2">
                                           <input type="text" name="gross_incentive_amount" id="gross_incentive_amount_1" class="form-control text-left gross_incentive_amount" tabindex="6" value="<?php echo $details['gross_incentive_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="broker_incentive" id="broker_incentive_1" class="form-control text-left broker_incentive" tabindex="6" value="<?php echo $details['broker_incentive']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="broker_incentive_amount" id="broker_incentive_amount_1" class="form-control text-left broker_incentive_amount" tabindex="6" value="<?php echo $details['broker_incentive_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="aggregator_incentive" id="aggregator_incentive_1" class="form-control text-left aggregator_incentive" tabindex="6" value="<?php echo $details['aggregator_incentive']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="aggregator_incentive_amount" id="aggregator_incentive_amount_1" class="form-control text-left aggregator_incentive_amount" tabindex="6" value="<?php echo $details['aggregator_incentive_amount']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="salesman_incentive" id="salesman_incentive_1" class="form-control text-left salesman_incentive" tabindex="6" value="<?php echo $details['salesman_incentive']?>" />
                                        </td>
                                        <td>
                                           <input type="text" name="salesman_incentive_amount" id="salesman_incentive_amount_1" class="form-control text-left salesman_incentive_amount" tabindex="6" value="<?php echo $details['salesman_incentive_amount']?>" />
                                        </td>
                                     </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="11" rowspan="2">
                                            <center><label sclass="text-center" for="details"
                                                    class="  col-form-label">Reason</label>
                                            </center>
                                            <textarea name="inva_details" id="details" class="form-control" required=""></textarea>
                                        </td>
                                        <td class="text-right" colspan="2"><b><?php echo display('total_discount') ?>:</b></td>
                                        <td class="text-right" colspan="2">
                                            <input type="text" id="total_discount_ammount" class="form-control text-right"
                                                name="total_discount" value="<?php echo $total_discount;?>"
                                                readonly="readonly" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="2"><b><?php echo display('ttl_val') ?>:</b></td>
                                        <td class="text-right" colspan="2">
                                            <input type="text" id="total_vat_amnt" class="form-control text-right"
                                                value="<?php echo $total_vat_amnt;?>" name="total_vat_amnt" value="0.00"
                                                readonly="readonly" />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="13" class="text-right"><b><?php echo display('grand_total') ?>:</b></td>
                                        <td colspan="2" class="text-right">
                                            <input type="text" id="grandTotal" class="form-control grandTotalamnt text-right"
                                                name="grand_total_price" value="<?php echo $total_amount?>"
                                                readonly="readonly" />
                                            <input type="hidden" id="grandTotalhidden"
                                                name="grand_total_pricehidden" value="<?php echo $total_amount?>"
                                                readonly="readonly" />
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                    <!--    <td colspan="15" class="text-right"><b><?php echo display('previous'); ?>:</b></td>-->
                                    <!--    <td colspan="2" class="text-right">-->
                                    <!--        <input type="text" id="previous" class="form-control text-right" name="previous"-->
                                    <!--            value="<?php echo $prev_due?>" readonly="readonly" />-->
                                    <!--    </td>-->
                                    <!--</tr>-->
                                    <!--<tr>-->
                                    <!--    <td colspan="15" class="text-right"><b><?php echo display('net_total'); ?>:</b></td>-->
                                    <!--    <td colspan="2" class="text-right">-->
                                    <!--        <input type="text" id="n_total" class="form-control text-right" name="n_total"-->
                                    <!--            value="<?php echo $net_total;?>" readonly="readonly" placeholder="" />-->
                                    <!--    </td>-->
                                    <!--</tr>-->
                                    <tr>
        
                                        <td class="text-right" colspan="13"><b><?php echo display('paid_ammount') ?>:</b></td>
                                        <td class="text-right" colspan="2">
                                            <input type="text" id="paidAmount" onkeyup="invoice_paidamount();"
                                                class="form-control text-right" name="paid_amount" placeholder="0.00"
                                                tabindex="13" value="" />
                                        </td>
                                    </tr>
                                    <tr>
        
        
                                        <td class="text-right" colspan="13">
                                            <input type="hidden" name="baseUrl" class="baseUrl"
                                                value="<?php echo base_url(); ?>" />
                                            <input type="hidden" name="invoice_id" id="invoice_id"
                                                value="<?php echo $invoice_id?>" />
                                            <input type="hidden" name="invoice" id="invoice" value="<?php echo $invoice_id?>" />
                                            <input type="hidden" name="dbinv_id" id="invoice" value="<?php echo $dbinv_id?>" />
                                            <b><?php echo display('due') ?>:</b>
                                        </td>
                                        <td class="text-right" colspan="2">
                                            <input type="text" id="dueAmmount" class="form-control text-right" name="due_amount"
                                                value="" readonly="readonly" />
                                        </td>
                                    </tr>
                                    <!--<tr>-->
        
                                    <!--    <td class="text-right" colspan="15"><b><?php echo display('change') ?>:</b></td>-->
                                    <!--    <td class="text-right" colspan="2">-->
                                    <!--        <input type="text" id="change" class="form-control text-right" name="change"-->
                                    <!--            value="0" readonly="readonly" />-->
                                    <!--    </td>-->
                                    <!--</tr>-->
                                </tfoot>
                            </table>
                            <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                        </div>
                        <div class="form-group row">
                            <label for="example-text-input" class=" col-form-label"></label>
                            <div class="col-sm-12 text-right" >

                                <input type="" id="add_invoice" class="btn btn-success btn-large" onclick="checkreturnamount()" name="add-invoice"  value="<?php echo display('return') ?>" readonly="readonly" tabindex="9"/>

                            </div>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>

<style>
    .form-control, select, .select2-selection__rendered {
        font-size: 11px;
    }
    /*.table > tbody > tr > td{*/
    /*    min-width: 85px;*/
    /*}*/
</style>
<script>

    $(document).ready(function () {
        $('.premium_amount').on('change', function () {
            var premium_amount = $(this).val();
            var premium_amount_dummy = $('.premium_amount_dummy').val();
            
            if (parseFloat(premium_amount) > parseFloat(premium_amount_dummy)) {
    
                toastr["error"]('Grand Total Must Greater Then Net Return Amount');
            }else{
                var premium_policy = $('.premium_policy').val();
                var premium_basmah = $('.premium_basmah').val();
                if(premium_policy==''){
                    premium_policy=0;
                }
                if(premium_amount==''){
                    premium_amount=0;
                }
                if(premium_basmah==''){
                    premium_basmah=0;
                }
                var premium_vat = (((parseFloat(premium_amount) + parseFloat(premium_policy)) * 5) /100).toFixed(2,2);
                $('.premium_vat').val(premium_vat);
                var total_premium_amount = parseFloat(premium_amount) + parseFloat(premium_policy) + parseFloat(premium_vat) + parseFloat(premium_basmah);
                $('.total_premium_amount').val(total_premium_amount.toFixed(2, 2));
                $('.grandTotalamnt').val(total_premium_amount.toFixed(2, 2));
                $('#total_vat_amnt').val(premium_vat);
                var paidAmount = $('#paidAmount').val();
                if(paidAmount==''){
                    paidAmount=0;
                }
                    
                var total_discount = $('#total_discount_ammount').val();
                if(total_discount==''){
                    total_discount=0;
                }
                var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
                
                
                // var hpa = parseFloat($("#hiddenpaidAmount").val(), 10);
                var t = parseFloat($("#grandTotal").val(), 10);
                var a = $("#paidAmount").val();
                // if(hpa==''){
                //     hpa=0;
                // }
                if(t==''){
                    t=0;
                }
                if(a==''){
                    a=0;
                }
                
                var nt = parseFloat(t, 10)  - parseFloat(a, 10) - parseFloat(total_discount,10);
                    
                $("#dueAmmount").val(nt.toFixed(2, 2));
                
                var gross_commission = $('.gross_commission').val();
                var gross_commission_amount = $('.gross_commission_amount').val();
                $('.gross_commission').val(gross_commission);
                if(gross_commission != '') {
                    var gross_commission_amount = ((parseFloat(premium_amount) * parseFloat(gross_commission)) /100).toFixed(2);
                    $('.gross_commission_amount').val(gross_commission_amount);
                    var gross_commission_vat = ((parseFloat(gross_commission_amount) * 5) /100).toFixed(2);
                    $('.gross_commission_vat').val(gross_commission_vat);
                    var total_gross_commission_amount = parseFloat(gross_commission_amount) + parseFloat(gross_commission_vat);
                    $('.total_gross_commission_amount').val(total_gross_commission_amount);
                    var broker_commission = $('.broker_commission').val();
                    if(broker_commission != '') {
                        var broker_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(broker_commission)) /100).toFixed(2);
                        $('.broker_commission_amount').val(broker_commission_amount);
                    }
                    var aggregator_commission = $('.aggregator_commission').val();
                    if(aggregator_commission != '') {
                        var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(aggregator_commission)) /100).toFixed(2);
                        $('.aggregator_commission_amount').val(aggregator_commission_amount);
                    }
                    var salesman_commission = $('.salesman_commission').val();
                    if(salesman_commission != '') {
                        var salesman_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(salesman_commission)) /100).toFixed(2);
                        $('.salesman_commission_amount').val(salesman_commission_amount);
                    }
                }
                
                var gross_incentive = $('.gross_incentive').val();
                
                if(premium_amount==''){
                    premium_amount=0;
                }
                if(gross_incentive==''){
                    gross_incentive=0;
                }
                if(gross_incentive != '') {
                    var gross_incentive_amount = ((parseFloat(premium_amount) * parseFloat(gross_incentive)) /100).toFixed(2);
                    $('.gross_incentive_amount').val(gross_incentive_amount);
                    var broker_incentive = $('.broker_incentive').val();
                    
                    if(broker_incentive==''){
                        broker_incentive=0;
                    }
                    if(broker_incentive != '') {
                        var broker_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(broker_incentive)) /100).toFixed(2);
                        $('.broker_incentive_amount').val(broker_incentive_amount);
                    } else {
                        var broker_incentive_amount = ((parseFloat(gross_incentive_amount) * 30) /100).toFixed(2);
                        $('.broker_incentive_amount').val(broker_incentive_amount);
                        $('.broker_incentive').val(30);
                    }
                    var aggregator_incentive = $('.aggregator_incentive').val();
                    if(aggregator_incentive==''){
                        aggregator_incentive=0;
                    }
                    if(aggregator_incentive != '') {
                        var aggregator_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(aggregator_incentive)) /100).toFixed(2);
                        $('.aggregator_incentive_amount').val(aggregator_incentive_amount);
                    } else {
                        var aggregator_incentive_amount = ((parseFloat(gross_incentive_amount) * 20) /100).toFixed(2);
                        $('.aggregator_incentive_amount').val(aggregator_incentive_amount);
                        $('.aggregator_incentive').val(20);
                    }
                    var salesman_incentive = $('.salesman_incentive').val();
                    if(salesman_incentive==''){
                        salesman_incentive=0;
                    }
                    if(salesman_incentive != '') {
                        var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(salesman_incentive)) /100).toFixed(2);
                        $('.salesman_incentive_amount').val(salesman_incentive_amount);
                    } else {
                        var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * 50) /100).toFixed(2);
                        $('.salesman_incentive_amount').val(salesman_incentive_amount);
                        $('.salesman_incentive').val(50);
                    }
                }
            }
        });
        $('.premium_policy').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var premium_policy = $('.premium_policy').val();
            var premium_basmah = $('.premium_basmah').val();
            var paidAmount = $('#paidAmount').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(premium_policy==''){
                premium_policy=0;
            }
            if(premium_basmah==''){
                premium_basmah=0;
            }
            if(paidAmount==''){
                paidAmount=0;
            }
            
            var premium_vat = (((parseFloat(premium_amount) + parseFloat(premium_policy)) * 5) /100).toFixed(2);
            $('.premium_vat').val(premium_vat);
            var total_premium_amount = parseFloat(premium_amount) + parseFloat(premium_policy) + parseFloat(premium_vat) + parseFloat(premium_basmah);
            $('.total_premium_amount').val(total_premium_amount.toFixed(2, 2));
        
            var total_discount = $('#total_discount_ammount').val();
            if(total_discount==''){
                total_discount=0;
            }
            var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
            
            $('.grandTotalamnt').val(grandtotal.toFixed(2, 2));
            $('.vat_total').val(premium_vat);
            
                
            // var hpa = parseFloat($("#hiddenpaidAmount").val(), 10);
            var t = parseFloat($("#grandTotal").val(), 10);
            var a = $("#paidAmount").val();
            // if(hpa==''){
            //     hpa=0;
            // }
            if(t==''){
                t=0;
            }
            if(a==''){
                a=0;
            }
            
            var nt = parseFloat(t, 10) - parseFloat(a, 10) - parseFloat(total_discount,10);
                
            $("#dueAmmount").val(nt.toFixed(2, 2));
           
        });
        $('.premium_basmah').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var premium_policy = $('.premium_policy').val();
            var premium_basmah = $('.premium_basmah').val();
            var paidAmount = $('#paidAmount').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(premium_policy==''){
                premium_policy=0;
            }
            if(premium_basmah==''){
                premium_basmah=0;
            }
            if(paidAmount==''){
                paidAmount=0;
            }
            
            var premium_vat = (((parseFloat(premium_amount) + parseFloat(premium_policy)) * 5) /100).toFixed(2);
            $('.premium_vat').val(premium_vat);
            var total_premium_amount = parseFloat(premium_amount) + parseFloat(premium_policy) + parseFloat(premium_vat) + parseFloat(premium_basmah);
            $('.total_premium_amount').val(total_premium_amount.toFixed(2, 2));
        
            var total_discount = $('#total_discount_ammount').val();
            if(total_discount==''){
                total_discount=0;
            }
            var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
            
            $('.grandTotalamnt').val(grandtotal.toFixed(2, 2));
            $('.vat_total').val(premium_vat.toFixed(2, 2));
            $('.vat_total').val(premium_vat.toFixed(2, 2));
            
            var t = parseFloat($("#grandTotal").val(), 10);
            var a = $("#paidAmount").val();
            
            if(t==''){
                t=0;
            }
            if(a==''){
                a=0;
            }
            
            var nt = parseFloat(t, 10) - parseFloat(a, 10) - parseFloat(total_discount,10);
                
            $("#dueAmmount").val(nt.toFixed(2, 2));
           
        });
        
        $('.gross_commission').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            // var premium_vat = (parseFloat(premium_amount) * 5) /100;
            // $('.premium_vat').val(premium_vat);
            // var total_premium_amount = parseFloat(premium_amount) + parseFloat(premium_vat);
            // $('.total_premium_amount').val(total_premium_amount);
            var gross_commission = $('.gross_commission').val();
            // $('.gross_commission').val(gross_commission);
            
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_commission==''){
                gross_commission=0;
            }
            var gross_commission_amount = $('.gross_commission_amount').val();
            if(gross_commission != '') {
                var gross_commission_amount = ((parseFloat(premium_amount) * parseFloat(gross_commission)) /100).toFixed(2);
                $('.gross_commission_amount').val(gross_commission_amount);
                var gross_commission_vat = ((parseFloat(gross_commission_amount) * 5) /100).toFixed(2);
                $('.gross_commission_vat').val(gross_commission_vat);
                var total_gross_commission_amount = parseFloat(gross_commission_amount) + parseFloat(gross_commission_vat);
                $('.total_gross_commission_amount').val(total_gross_commission_amount);
                var broker_commission = $('.broker_commission').val();
                
                if(broker_commission==''){
                    broker_commission=0;
                }
                if(broker_commission != '') {
                    var broker_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(broker_commission)) /100).toFixed(2);
                    $('.broker_commission_amount').val(broker_commission_amount);
                } else {
                    var broker_commission_amount = ((parseFloat(gross_commission_amount) * 30) /100).toFixed(2);
                    $('.broker_commission_amount').val(broker_commission_amount);
                    $('.broker_commission').val(30);
                }
                var aggregator_commission = $('.aggregator_commission').val();
                if(aggregator_commission==''){
                    aggregator_commission=0;
                }
                if(aggregator_commission != '') {
                    var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(aggregator_commission)) /100).toFixed(2);
                    $('.aggregator_commission_amount').val(aggregator_commission_amount);
                } else {
                    var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * 20) /100).toFixed(2);
                    $('.aggregator_commission_amount').val(aggregator_commission_amount);
                    $('.aggregator_commission').val(20);
                }
                var salesman_commission = $('.salesman_commission').val();
                if(salesman_commission==''){
                    salesman_commission=0;
                }
                if(salesman_commission != '') {
                    var salesman_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(salesman_commission)) /100).toFixed(2);
                    $('.salesman_commission_amount').val(salesman_commission_amount);
                } else {
                    var salesman_commission_amount = ((parseFloat(gross_commission_amount) * 50) /100).toFixed(2);
                    $('.salesman_commission_amount').val(salesman_commission_amount);
                    $('.salesman_commission').val(50);
                }
            }
        });
        $('.broker_commission').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_commission = $('.gross_commission').val();
            var broker_commission = $('.broker_commission').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_commission==''){
                gross_commission=0;
            }
            if(broker_commission==''){
                broker_commission=0;
            }
            if(gross_commission != '') {
                var total_gross_commission_amount = $('.total_gross_commission_amount').val();
                var gross_commission_amount = $('.gross_commission_amount').val();
                if(total_gross_commission_amount==''){
                    total_gross_commission_amount=0;
                }
                if(broker_commission != '') {
                    var broker_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(broker_commission)) /100).toFixed(2);
                    $('.broker_commission_amount').val(broker_commission_amount);
                } else {
                    var broker_commission_amount = ((parseFloat(gross_commission_amount) * 30) /100).toFixed(2);
                    $('.broker_commission_amount').val(broker_commission_amount);
                    $('.broker_commission').val(30);
                }
            }
        });
        $('.aggregator_commission').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_commission = $('.gross_commission').val();
            var aggregator_commission = $('.aggregator_commission').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_commission==''){
                gross_commission=0;
            }
            if(aggregator_commission==''){
                aggregator_commission=0;
            }
            if(gross_commission != '') {
                var total_gross_commission_amount = $('.total_gross_commission_amount').val();
                var gross_commission_amount = $('.gross_commission_amount').val();
                if(total_gross_commission_amount==''){
                    total_gross_commission_amount=0;
                }
                if(aggregator_commission != '') {
                    var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(aggregator_commission)) /100).toFixed(2);
                    $('.aggregator_commission_amount').val(aggregator_commission_amount);
                } else {
                    var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * 20) /100).toFixed(2);
                    $('.aggregator_commission_amount').val(aggregator_commission_amount);
                    $('.aggregator_commission').val(20);
                }
            }
        });
        $('.salesman_commission').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_commission = $('.gross_commission').val();
            var salesman_commission = $('.salesman_commission').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_commission==''){
                gross_commission=0;
            }
            if(salesman_commission==''){
                salesman_commission=0;
            }
            if(gross_commission != '') {
                var total_gross_commission_amount = $('.total_gross_commission_amount').val();
                var gross_commission_amount = $('.gross_commission_amount').val();
                if(total_gross_commission_amount==''){
                    total_gross_commission_amount=0;
                }
                if(salesman_commission != '') {
                    var salesman_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(salesman_commission)) /100).toFixed(2);
                    $('.salesman_commission_amount').val(salesman_commission_amount);
                } else {
                    var salesman_commission_amount = ((parseFloat(gross_commission_amount) * 50) /100).toFixed(2);
                    $('.salesman_commission_amount').val(salesman_commission_amount);
                    $('.salesman_commission').val(50);
                }
            }
        });
        
        $('.gross_incentive').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_incentive = $('.gross_incentive').val();
            
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_incentive==''){
                gross_incentive=0;
            }
            if(gross_incentive != '') {
                var gross_incentive_amount = ((parseFloat(premium_amount) * parseFloat(gross_incentive)) /100).toFixed(2);
                $('.gross_incentive_amount').val(gross_incentive_amount);
                var broker_incentive = $('.broker_incentive').val();
                
                if(broker_incentive==''){
                    broker_incentive=0;
                }
                if(broker_incentive != '') {
                    var broker_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(broker_incentive)) /100).toFixed(2);
                    $('.broker_incentive_amount').val(broker_incentive_amount);
                } else {
                    var broker_incentive_amount = ((parseFloat(gross_incentive_amount) * 30) /100).toFixed(2);
                    $('.broker_incentive_amount').val(broker_incentive_amount);
                    $('.broker_incentive').val(30);
                }
                var aggregator_incentive = $('.aggregator_incentive').val();
                if(aggregator_incentive==''){
                    aggregator_incentive=0;
                }
                if(aggregator_incentive != '') {
                    var aggregator_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(aggregator_incentive)) /100).toFixed(2);
                    $('.aggregator_incentive_amount').val(aggregator_incentive_amount);
                } else {
                    var aggregator_incentive_amount = ((parseFloat(gross_incentive_amount) * 20) /100).toFixed(2);
                    $('.aggregator_incentive_amount').val(aggregator_incentive_amount);
                    $('.aggregator_incentive').val(20);
                }
                var salesman_incentive = $('.salesman_incentive').val();
                if(salesman_incentive==''){
                    salesman_incentive=0;
                }
                if(salesman_incentive != '') {
                    var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(salesman_incentive)) /100).toFixed(2);
                    $('.salesman_incentive_amount').val(salesman_incentive_amount);
                } else {
                    var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * 50) /100).toFixed(2);
                    $('.salesman_incentive_amount').val(salesman_incentive_amount);
                    $('.salesman_incentive').val(50);
                }
            }
        });
        $('.broker_incentive').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_incentive = $('.gross_incentive').val();
            var broker_incentive = $('.broker_incentive').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_incentive==''){
                gross_incentive=0;
            }
            if(broker_incentive==''){
                broker_incentive=0;
            }
            if(gross_incentive != '') {
                var gross_incentive_amount = $('.gross_incentive_amount').val();
                if(gross_incentive_amount==''){
                    gross_incentive_amount=0;
                }
                if(broker_incentive != '') {
                    var broker_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(broker_incentive)) /100).toFixed(2);
                    $('.broker_incentive_amount').val(broker_incentive_amount);
                } else {
                    var broker_incentive_amount = ((parseFloat(gross_incentive_amount) * 30) /100).toFixed(2);
                    $('.broker_incentive_amount').val(broker_incentive_amount);
                    $('.broker_incentive').val(30);
                }
            }
        });
        $('.aggregator_incentive').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_incentive = $('.gross_incentive').val();
            var aggregator_incentive = $('.aggregator_incentive').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_incentive==''){
                gross_incentive=0;
            }
            if(aggregator_incentive==''){
                aggregator_incentive=0;
            }
            if(gross_incentive != '') {
                var gross_incentive_amount = $('.gross_incentive_amount').val();
                if(gross_incentive_amount==''){
                    gross_incentive_amount=0;
                }
                if(aggregator_incentive != '') {
                    var aggregator_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(aggregator_incentive)) /100).toFixed(2);
                    $('.aggregator_incentive_amount').val(aggregator_incentive_amount);
                } else {
                    var aggregator_incentive_amount = ((parseFloat(gross_incentive_amount) * 20) /100).toFixed(2);
                    $('.aggregator_incentive_amount').val(aggregator_incentive_amount);
                    $('.aggregator_incentive').val(20);
                }
            }
        });
        $('.salesman_incentive').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_incentive = $('.gross_incentive').val();
            var salesman_incentive = $('.salesman_incentive').val();
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_incentive==''){
                gross_incentive=0;
            }
            if(salesman_incentive==''){
                salesman_incentive=0;
            }
            if(gross_incentive != '') {
                var gross_incentive_amount = $('.gross_incentive_amount').val();
                if(gross_incentive_amount==''){
                    gross_incentive_amount=0;
                }
                if(salesman_incentive != '') {
                    var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(salesman_incentive)) /100).toFixed(2);
                    $('.salesman_incentive_amount').val(salesman_incentive_amount);
                } else {
                    var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * 50) /100).toFixed(2);
                    $('.salesman_incentive_amount').val(salesman_incentive_amount);
                    $('.salesman_incentive').val(50);
                }
            }
        });
        $('#total_discount_ammount').on('change', function () {
            var total_discount = $(this).val();
            var salesman_commission_amount = $('.salesman_commission_amount').val();
            if (parseFloat(salesman_commission_amount) >= parseFloat(total_discount)) {
                if(total_discount==''){
                    total_discount=0;
                }
                var total_premium_amount = $('.total_premium_amount').val();
                var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
                $('.grandTotalamnt').val(grandtotal.toFixed(2, 2));
            }else{
                toastr["error"]('Discount should be less than salesman commission');
            }
        });
        
    });
    function checkreturnamount() {
        var vatamnt = 0;
        var gt      = $("#grandTotal").val();
        var gtret   = $("#grandTotalhidden").val();
    
        if (parseFloat(gtret) < parseFloat(gt)) {

            toastr["error"]('Grand Total Must Greater Then Net Return Amount');
        } else {
            $('form#returnForm').submit();
        }
    }
</script>