<script src="<?php echo base_url() ?>my-assets/js/admin_js/invoice.js" type="text/javascript"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('invoice_edit') ?></h4>
                </div>
            </div>
            <?php echo form_open('invoice/invoice/cloudsubset_update_invoice', array('class' => 'form-vertical', 'id' => 'update_invoice')) ?>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="invoice_type" class="col-sm-4 col-form-label">Invoice Type
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select name="invoice_type" class="form-control" required="">
                                        <option value=""> Select Type</option>
                                        <option value="1" <?php if($salesmans['invoice_type']==1){echo 'selected';}?>>New</option>
                                        <option value="2" <?php if($salesmans['invoice_type']==2){echo 'selected';}?>>Renewal</option>
                                        <option value="3" <?php if($salesmans['invoice_type']==3){echo 'selected';}?>>Endorsement</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="product_name" class="col-sm-3 col-form-label"><?php echo display('date') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" tabindex="2" class="form-control datepicker" name="invoice_date" value="<?php echo $date?>" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="invoice_no" class="col-sm-3 col-form-label"><?php echo display('invoice_no'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="invoice_no" id="invoice_no" required value="<?php echo html_escape($invoice); ?>" readonly />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="document_date" class="col-sm-4 col-form-label">Document Date: <i class="text-danger">*</i></label>
                         <div class="col-sm-8">
                            <input class="datepicker form-control" type="text" size="50" name="document_date" id="document_date" required value="<?php echo html_escape($document_date); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                </div>
                    
                <div class="row">
                    
                    <div class="col-sm-8" id="payment_from_1">
                        <div class="form-group row">
                            <label for="customer_name" class="col-sm-3 col-form-label">Insured/Client <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" size="100" name="customer_name" class=" form-control"
                                    placeholder='<?php echo display('customer_name').'/'.display('phone') ?>'
                                    id="customer_name" tabindex="1" onkeyup="customer_autocomplete()"
                                    value="<?php echo $customer_name?>" />

                                <input id="autocomplete_customer_id" class="customer_hidden_value abc" type="hidden"
                                    name="customer_id" value="<?php echo $customer_id?>">
                            </div>
                            <?php if($this->permission1->method('add_customer','create')->access()){ ?>
                            <div class=" col-sm-3">
                                <a href="#" class="client-add-btn btn btn-success" aria-hidden="true"
                                    data-toggle="modal" data-target="#cust_info"><i class="ti-plus m-r-2"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="salesman_id" class="col-sm-4 col-form-label"><?php echo display('salesman') ?>
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select name="salesman_id" class="form-control" required="">
                                        <option value=""> select Salesman</option>
                                        <?php if ($salesman) { ?>
                                        <?php foreach($salesman as $salesmans){?>
                                        <option value="<?php echo $salesmans['salesman_id']?>"
                                        <?php if($salesman_id==$salesmans['salesman_id']){echo 'selected';}?>
                                        >
                                            <?php echo $salesmans['salesman_name']?></option>

                                        <?php }} ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="broker_id" class="col-sm-4 col-form-label"><?php echo display('broker') ?>
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select name="broker_id" class="form-control" required="">
                                        <option value=""> select Broker</option>
                                        <?php if ($broker) { ?>
                                        <?php foreach($broker as $brokers){?>
                                        <option value="<?php echo $brokers['broker_id']?>"
                                        <?php if($broker_id==$brokers['broker_id']){echo 'selected';}?>
                                        >
                                            <?php echo $brokers['broker_name']?></option>

                                        <?php }} ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_id" class="col-sm-4 col-form-label"><?php echo display('supplier') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select name="supplier_id" class="form-control" required="">
                                        <option value=""> select Supplier</option>
                                        <?php if ($supplier) { ?>
                                        <?php foreach($supplier as $suppliers){?>
                                        <option value="<?php echo $suppliers['supplier_id']?>"
                                        <?php if($supplier_id==$suppliers['supplier_id']){echo 'selected';}?>
                                        >
                                            <?php echo $suppliers['supplier_name']?></option>

                                        <?php }} ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="policy_type" class="col-sm-4 col-form-label">Policy Type :<i class="text-danger">*</i></label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="policy_type" id="policy_type" required value="<?php echo html_escape($policy_type); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="policy_no" class="col-sm-4 col-form-label">Policy No :<i
                            class="text-danger">*</i></label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="policy_no" id="policy_no" required value="<?php echo html_escape($policy_no); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="endorsement_no" class="col-sm-4 col-form-label">Endorsement No:<i class="text-danger">*</i></label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="endorsement_no" id="endorsement_no" value="<?php echo html_escape($endorsement_no); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="debit_note_no" class="col-sm-4 col-form-label">Debit Note No:</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="debit_note_no" id="debit_note_no"  value="<?php echo html_escape($debit_note_no); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <!--<div class="col-sm-6">-->
                    <!--  <div class="form-group row">-->
                    <!--     <label for="credit_note_no" class="col-sm-4 col-form-label">Credit Note No:</label>-->
                    <!--     <div class="col-sm-8">-->
                    <!--        <input class="form-control" type="text" size="100" name="credit_note_no" id="credit_note_no" value="<?php echo html_escape($credit_note_no); ?>" tabindex="4" />-->
                    <!--     </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="credit_note_no" class="col-sm-4 col-form-label">Commission Credit Note No:</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="commission_credit_note_no" id="commission_credit_note_no" value="<?php echo html_escape($commission_credit_note_no); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="credit_note_no" class="col-sm-4 col-form-label">Incentive Credit Note No:</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="incentive_credit_note_no" id="incentive_credit_note_no" value="<?php echo html_escape($incentive_credit_note_no); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                </div>
                    
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="policy_from" class="col-sm-4 col-form-label">Policy period From:</label>
                         <div class="col-sm-8">
                            <input class="datepicker form-control" type="text" size="50" name="policy_from" id="policy_from" required value="<?php echo html_escape($policy_from); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="policy_to" class="col-sm-4 col-form-label">Policy period To:</label>
                         <div class="col-sm-8">
                            <input class="datepicker form-control" type="text" size="50" name="policy_to" id="policy_to"  value="<?php echo html_escape($policy_to); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="product_name" class="col-sm-4 col-form-label">Product</label>
                            <div class="col-sm-8">
                                <input type="text" name="product_name"
                                    onkeypress="invoice_productList(<?php echo $invoice_all_data[0]['sl']?>);"
                                    value="<?php echo $invoice_all_data[0]['product_name']?>"
                                    class="form-control productSelection" required
                                    placeholder='<?php echo display('product_name') ?>'
                                    id="product_name_<?php echo $invoice_all_data[0]['sl']?>" tabindex="3">

                                <input type="hidden"
                                    class="product_id_<?php echo $invoice_all_data[0]['sl']?> autocomplete_hidden_value"
                                    name="product_id" value="<?php echo $invoice_all_data[0]['product_id']?>"
                                    id="SchoolHiddenId" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="sum_insured" class="col-sm-4 col-form-label">Sum Insured</label>
                            <div class="col-sm-8">
                                <input type="text" name="sum_insured" id="sum_insured_1" class="form-control text-left sum_insured" value="<?php echo html_escape($sum_insured); ?>" tabindex="6" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="interest" class="col-sm-4 col-form-label">Interest:<i class="text-danger">*</i></label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="interest" id="interest" value="<?php echo html_escape($interest); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="narration" class="col-sm-4 col-form-label">Narration:<i class="text-danger">*</i></label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="narration" id="narration" value="<?php echo html_escape($narration); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="attachment" class="col-sm-4 col-form-label">Attachment:</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="file" size="100" name="attachment" id="attachment" value="<?php echo html_escape($attachment); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-sm-6" id="bank_div">
                        <div class="form-group row">
                            <label for="bank" class="col-sm-3 col-form-label"><?php
                                    echo display('bank');
                                    ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select name="bank_id" class="form-control bankpayment" id="bank_id">
                                    <option value="">Select Location</option>
                                    <?php foreach($bank_list as $bank){?>
                                    <option value="<?php echo html_escape($bank['bank_id'])?>">
                                        <?php echo html_escape($bank['bank_name']);?></option>
                                    <?php }?>
                                </select>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6" id="bank_div">
                        <div class="form-group row">
                            <label for="bank" class="col-sm-3 col-form-label"><?php
                                    echo display('bank');
                                    ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select name="bank_id" class="form-control bankpayment" id="bank_id">
                                    <option value="">Select Location</option>
                                    <?php foreach($bank_list as $bank){?>
                                    <option value="<?php echo html_escape($bank['bank_id'])?>"
                                        <?php if($bank['bank_id'] == $bank_id){echo 'selected';}?>>
                                        <?php echo html_escape($bank['bank_name']);?></option>
                                    <?php }?>
                                </select>
                                <input type="hidden" id="editpayment_type" value="<?php echo $paytype;?>" name="">
                            </div>

                        </div>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="normalinvoice">
                        <thead>
                                <tr>
                                    <th colspan="5" class="text-center">Premium</th>
                                    <th colspan="4" class="text-center">Gross Commission</th>
                                    <th colspan="2" class="text-center">Broker Commission</th>
                                    <th colspan="2" class="text-center">Aggregator Fees</th>
                                    <th colspan="2" class="text-center">Salesman Commission</th>
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
                                </td>
                                <td>
                                   <input type="text" name="premium_policy" id="premium_policy_1" class="form-control text-left premium_policy" tabindex="6" value="<?php echo $details['premium_policy']?>" />
                                </td>
                                <td>
                                   <input type="text" name="premium_vat" id="premium_vat_1" class="form-control text-left premium_vat" tabindex="6" value="<?php echo $details['premium_vat']?>" />
                                </td>
                                <td>
                                   <input type="text" name="premium_basmah" id="premium_basmah_1" class="form-control text-left premium_basmah" tabindex="6" value="<?php echo $details['premium_basmah']?>" />
                                </td>
                                <td>
                                   <input type="text" name="total_premium_amount" id="total_premium_amount_1" class="form-control text-left total_premium_amount" tabindex="6" value="<?php echo $details['total_premium_amount']?>" />
                                </td>
                                <td>
                                   <input type="text" name="gross_commission" id="gross_commission_1" class="form-control text-left gross_commission" tabindex="6" value="<?php echo $details['gross_commission']?>" />
                                </td>
                                <td>
                                   <input type="text" name="gross_commission_amount" id="gross_commission_amount_1" class="form-control text-left gross_commission_amount" tabindex="6" value="<?php echo $details['gross_commission_amount']?>" />
                                </td>
                                <td>
                                   <input type="text" name="gross_commission_vat" id="gross_commission_vat_1" class="form-control text-left gross_commission_vat" tabindex="6" value="<?php echo $details['gross_commission_vat']?>" />
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
                                <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                <th class="text-center" style="min-width: 90px;">VAT</th>
                                <th class="text-center" style="min-width: 90px;">Total</th>
                                <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                                <td class="text-center" style="min-width: 65px;"><strong>%</strong></td>
                                <td class="text-center" style="min-width: 90px;"><strong>Amount</strong></td>
                             </tr>
                             <tr>
                                <td colspan="5"></td>
                                <td>
                                   <input type="text" name="gross_incentive" id="gross_incentive_1" class="form-control text-left gross_incentive" tabindex="6" value="<?php echo $details['gross_incentive']?>" />
                                </td>
                                <td>
                                   <input type="text" name="gross_incentive_amount" id="gross_incentive_amount_1" class="form-control text-left gross_incentive_amount" tabindex="6" value="<?php echo $details['gross_incentive_amount']?>" />
                                </td>
                                <td>
                                   <input type="text" name="gross_incentive_vat" id="gross_incentive_vat_1" class="form-control text-left gross_incentive_vat" tabindex="6" value="<?php echo $details['gross_incentive_vat']?>" />
                                </td>
                                <td>
                                   <input type="text" name="total_gross_incentive_amount" id="total_gross_incentive_amount_1" class="form-control text-left total_gross_incentive_amount" tabindex="6" value="<?php echo $details['total_gross_incentive_amount']?>" />
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
                                            class="  col-form-label"><?php echo display('invoice_details') ?></label>
                                    </center>
                                    <textarea name="inva_details" id="details" class="form-control"
                                        placeholder="<?php echo display('invoice_details') ?>"><?php echo $invoice_details;?></textarea>
                                </td>
                                <td class="text-right" colspan="2"><b><?php echo display('total_discount') ?>:</b></td>
                                <td class="text-right" colspan="2">
                                    <input type="text" id="total_discount_ammount" class="form-control text-right"
                                        name="total_discount" value="<?php echo $total_discount;?>"/>
                                </td>
                            </tr>
                            <!--<tr>-->
                            <!--    <td class="text-right" colspan="2"><b><?php echo display('ttl_val') ?>:</b></td>-->
                            <!--    <td class="text-right" colspan="2">-->
                            <!--        <input type="text" id="total_vat_amnt" class="form-control text-right"-->
                            <!--            value="<?php echo $total_vat_amnt;?>" name="total_vat_amnt" value="0.00"-->
                            <!--            readonly="readonly" />-->
                            <!--    </td>-->
                            <!--</tr>-->
                            <tr>
                                <td colspan="2" class="text-right"><b><?php echo display('grand_total') ?>:</b></td>
                                <td colspan="2" class="text-right">
                                    <input type="text" id="grandTotal" class="form-control grandTotalamnt text-right"
                                        name="grand_total_price" value="<?php echo $total_amount?>"
                                        readonly="readonly" />
                                </td>
                            </tr>
                            <!--<tr>-->
                            <!--    <td colspan="16" class="text-right"><b><?php echo display('previous'); ?>:</b></td>-->
                            <!--    <td colspan="2" class="text-right">-->
                            <!--        <input type="text" id="previous" class="form-control text-right" name="previous"-->
                            <!--            value="<?php echo $prev_due?>" readonly="readonly" />-->
                            <!--    </td>-->
                            <!--</tr>-->
                            <!--<tr>-->
                            <!--    <td colspan="16" class="text-right"><b><?php echo display('net_total'); ?>:</b></td>-->
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
                                        tabindex="13" value="<?php echo $paid_amount;?>" />
                                </td>
                            </tr>
                            <tr>


                                <td class="text-right" colspan="13">
                                    <input type="hidden" name="baseUrl" class="baseUrl"
                                        value="<?php echo base_url(); ?>" />
                                    <input type="hidden" name="invoice_id" id="invoice_id"
                                        value="<?php echo $invoice?>" />
                                    <input type="hidden" name="invoice" id="invoice" value="<?php echo $invoice?>" />
                                    <input type="hidden" name="dbinv_id" id="invoice" value="<?php echo $dbinv_id?>" />
                                    <b><?php echo display('due') ?>:</b>
                                </td>
                                <td class="text-right" colspan="2">
                                    <input type="text" id="dueAmmount" class="form-control text-right" name="due_amount"
                                        value="<?php echo $due_amount?>" readonly="readonly" />
                                </td>
                            </tr>
                             <tr>
                                <td class="text-right" colspan="13"><b>Salesman Commission Paid Amount:</b></td>
                                <td colspan="2" class="text-right">
                                   <input type="hidden" name="baseUrl" class="baseUrl"
                                      value="<?php echo base_url(); ?>" />
                                   <input type="text" id="salesmanpaidAmount" onkeyup="invoice_commissionpaidamount();" class="form-control text-right" name="salesman_commission_paid_amount" placeholder="0.00" tabindex="15" value="0.00" />
                                </td>
                             </tr>
                             <tr>
                                <td class="text-right" colspan="13"><b>Salesman Commission Due Amount:</b></td>
                                <td colspan="2" class="text-right">
                                   <input type="text" id="salesmandueAmmount" class="form-control text-right" name="salesman_commission_due_amount" value="0.00" readonly="readonly" />
                                </td>
                             </tr>
                            <!--<tr>-->

                            <!--    <td class="text-right" colspan="16"><b><?php echo display('change') ?>:</b></td>-->
                            <!--    <td class="text-right" colspan="2">-->
                            <!--        <input type="text" id="change" class="form-control text-right" name="change"-->
                            <!--            value="0" readonly="readonly" />-->
                            <!--    </td>-->
                            <!--</tr>-->
                        </tfoot>
                    </table>

                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"><?php echo $paid_amount;?></p>
                    <p hidden id="change-amount"></p>
                    <div class="col-sm-6 table-bordered p-20">
                        <div id="adddiscount" class="display-none">

                            <input type="hidden" id="invoice_edit_page" value="1">
                            <input type="hidden" id="is_credit_edit" value="<?php echo $is_credit?>">

                            <div class="" id="add_new_payment">

                            <?php if ($is_credit != 1) { 
                                    
                                foreach($multi_paytype as $all_paytype){?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-6">
                                        <label for="payments"
                                            class="col-form-label pb-2"><?php echo display('payment_type');?></label>

                                        <?php 
                                        echo form_dropdown('multipaytype[]',$all_pmethod,(!empty($all_paytype)?$all_paytype->COAID:null),'onchange = "check_creditsale()" class="card_typesl postform resizeselect form-control "') ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="4digit"
                                            class="col-form-label pb-2"><?php echo display('paid_amount');?></label>

                                        <input type="text" id="pamount_by_method"
                                            class="form-control number pay firstpay" name="pamount_by_method[]"
                                            value="<?php echo $all_paytype->Debit?>" onkeyup="changedueamount()"
                                            placeholder="0" />

                                    </div>
                                </div>
                                <?php } }else {?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-6">
                                        <label for="payments"
                                            class="col-form-label pb-2"><?php echo display('payment_type');?></label>

                                        <?php 
                                        echo form_dropdown('multipaytype[]',$all_pmethodwith_cr,0,'onchange = "check_creditsale()" class="card_typesl postform resizeselect form-control "') ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="4digit"
                                            class="col-form-label pb-2"><?php echo display('paid_amount');?></label>

                                        <input type="text" id="pamount_by_method"
                                            class="form-control number pay firstpay" name="pamount_by_method[]"
                                            value="<?php echo $paid_amount?>" onkeyup="changedueamount()"
                                            placeholder="0" />

                                    </div>
                                </div>
                                <?php }?>


                            </div>
                            <div class="form-group text-right">
                                <div class="col-sm-12 pr-0">

                                    <button <?php if(empty($multi_paytype)){echo 'disabled';}?> type="button"
                                        id="add_new_payment_type"
                                        class="btn btn-success w-md m-b-5"><?php echo display('new_p_method');?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <input type="submit" id="add_invoice" class="btn btn-success" name="add-invoice"
                            value="<?php echo display('submit') ?>" tabindex="17" />

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>

    <div class="modal fade" id="printconfirmodal" tabindex="-1" role="dialog" aria-labelledby="printconfirmodal"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo display('print') ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('invoice_print', array('class' => 'form-vertical', 'id' => '', 'name' => '')) ?>
                    <div id="outputs" class="hide alert alert-danger"></div>
                    <h3> <?php echo display('successfully_inserted') ?></h3>
                    <h4><?php echo display('do_you_want_to_print') ?> ??</h4>
                    <input type="hidden" name="invoice_id" id="inv_id">
                </div>
                <div class="modal-footer">
                    <a href="<?php echo base_url('invoice_list')?>"
                        class="btn btn-default"><?php echo display('no') ?></a>

                    <button type="submit" class="btn btn-primary" id="yes"><?php echo display('yes') ?></button>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function () {
        $('.premium_amount').on('change', function () {
            var premium_amount = $(this).val();
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
            
        
            var total_discount = $('#total_discount_ammount').val();
            if(total_discount==''){
                total_discount=0;
            }
            var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
            
            $('.grandTotalamnt').val(grandtotal.toFixed(2, 2));
            $('.vat_total').val(premium_vat);
            var paidAmount = $('#paidAmount').val();
            if(paidAmount==''){
                paidAmount=0;
            }
            
            
            var hpa = parseFloat($("#hiddenpaidAmount").val(), 10);
            var t = parseFloat($("#grandTotal").val(), 10);
            var a = $("#paidAmount").val();
            if(hpa==''){
                hpa=0;
            }
            if(t==''){
                t=0;
            }
            if(a==''){
                a=0;
            }
            
            var nt = parseFloat(t, 10) - parseFloat(hpa, 10) - parseFloat(a, 10) - parseFloat(total_discount,10);
                
            $("#dueAmmount").val(nt.toFixed(2, 2));
           
            
            var gross_commission = $('.gross_commission').val();
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
                } else {
                    var broker_commission_amount = ((parseFloat(gross_commission_amount) * 30) /100).toFixed(2);
                    $('.broker_commission_amount').val(broker_commission_amount);
                    $('.broker_commission').val(30);
                }
                var aggregator_commission = $('.aggregator_commission').val();
                if(aggregator_commission != '') {
                    var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * parseFloat(aggregator_commission)) /100).toFixed(2);
                    $('.aggregator_commission_amount').val(aggregator_commission_amount);
                } else {
                    var aggregator_commission_amount = ((parseFloat(gross_commission_amount) * 20) /100).toFixed(2);
                    $('.aggregator_commission_amount').val(aggregator_commission_amount);
                    $('.aggregator_commission').val(20);
                }
                var salesman_commission = $('.salesman_commission').val();
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
            $('.vat_total').val(premium_vat.toFixed(2, 2));
            $('.vat_total').val(premium_vat.toFixed(2, 2));
            
                
            var hpa = parseFloat($("#hiddenpaidAmount").val(), 10);
            var t = parseFloat($("#grandTotal").val(), 10);
            var a = $("#paidAmount").val();
            if(hpa==''){
                hpa=0;
            }
            if(t==''){
                t=0;
            }
            if(a==''){
                a=0;
            }
            
            var nt = parseFloat(t, 10) - parseFloat(hpa, 10) - parseFloat(a, 10) - parseFloat(total_discount,10);
                
            $("#dueAmmount").val(nt.toFixed(2, 2));
           
        });
        $('.premium_vat').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var premium_policy = $('.premium_policy').val();
            var premium_basmah = $('.premium_basmah').val();
            var premium_vat = $('.premium_vat').val();
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
            if(premium_vat==''){
                premium_vat=0;
            }
            
            // var premium_vat = (((parseFloat(premium_amount) + parseFloat(premium_policy)) * 5) /100).toFixed(2);
            // $('.premium_vat').val(premium_vat);
            var total_premium_amount = parseFloat(premium_amount) + parseFloat(premium_policy) + parseFloat(premium_vat) + parseFloat(premium_basmah);
            $('.total_premium_amount').val(total_premium_amount.toFixed(2, 2));
        
            var total_discount = $('#total_discount_ammount').val();
            if(total_discount==''){
                total_discount=0;
            }
            var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
            
            $('.grandTotalamnt').val(grandtotal.toFixed(2, 2));
            $('.vat_total').val(premium_vat);
            
                
            var hpa = parseFloat($("#hiddenpaidAmount").val(), 10);
            var t = parseFloat($("#grandTotal").val(), 10);
            var a = $("#paidAmount").val();
            if(hpa==''){
                hpa=0;
            }
            if(t==''){
                t=0;
            }
            if(a==''){
                a=0;
            }
            
            var nt = parseFloat(t, 10) - parseFloat(hpa, 10) - parseFloat(a, 10) - parseFloat(total_discount,10);
                
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
            
            var hpa = parseFloat($("#hiddenpaidAmount").val(), 10);
            var t = parseFloat($("#grandTotal").val(), 10);
            var a = $("#paidAmount").val();
            if(hpa==''){
                hpa=0;
            }
            if(t==''){
                t=0;
            }
            if(a==''){
                a=0;
            }
            
            var nt = parseFloat(t, 10) - parseFloat(hpa, 10) - parseFloat(a, 10) - parseFloat(total_discount,10);
                
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
                    var nt = (parseFloat(salesman_commission_amount, 10));
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
                } else {
                    var salesman_commission_amount = ((parseFloat(gross_commission_amount) * 50) /100).toFixed(2);
                    $('.salesman_commission_amount').val(salesman_commission_amount);
                    $('.salesman_commission').val(50);
                }
            }
        });
        $('.gross_commission_vat').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            // var premium_vat = (parseFloat(premium_amount) * 5) /100;
            // $('.premium_vat').val(premium_vat);
            // var total_premium_amount = parseFloat(premium_amount) + parseFloat(premium_vat);
            // $('.total_premium_amount').val(total_premium_amount);
            var gross_commission = $('.gross_commission').val();
            var gross_commission_vat = $('.gross_commission_vat').val();
            // $('.gross_commission').val(gross_commission);
            
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_commission==''){
                gross_commission=0;
            }
            if(gross_commission_vat==''){
                gross_commission_vat=0;
            }
            if(gross_commission != '') {
                var gross_commission_amount = ((parseFloat(premium_amount) * parseFloat(gross_commission)) /100).toFixed(2);
                $('.gross_commission_amount').val(gross_commission_amount);
                // var gross_commission_vat = ((parseFloat(gross_commission_amount) * 5) /100).toFixed(2);
                // $('.gross_commission_vat').val(gross_commission_vat);
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
                    var nt = (parseFloat(salesman_commission_amount, 10));
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
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
                    var nt = (parseFloat(salesman_commission_amount, 10));
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
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
            var salesman_commission_amount = $('.salesman_commission_amount').val();
            
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_incentive==''){
                gross_incentive=0;
            }
            if(gross_incentive != '') {
                var gross_incentive_amount = ((parseFloat(premium_amount) * parseFloat(gross_incentive)) /100).toFixed(2);
                $('.gross_incentive_amount').val(gross_incentive_amount);
                var gross_incentive_vat = ((parseFloat(gross_incentive_amount) * 5) /100).toFixed(2);
                $('.gross_incentive_vat').val(gross_incentive_vat);
                var total_gross_incentive_amount = parseFloat(gross_incentive_amount) + parseFloat(gross_incentive_vat);
                $('.total_gross_incentive_amount').val(total_gross_incentive_amount);
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
                    var nt = (parseFloat(salesman_commission_amount, 10) + parseFloat(salesman_incentive_amount, 10));
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
                } else {
                    var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * 50) /100).toFixed(2);
                    $('.salesman_incentive_amount').val(salesman_incentive_amount);
                    $('.salesman_incentive').val(50);
                }
            }
        });
        $('.gross_incentive_vat').on('change', function () {
            var premium_amount = $('.premium_amount').val();
            var gross_incentive = $('.gross_incentive').val();
            var gross_incentive_vat = $('.gross_incentive_vat').val();
            var salesman_commission_amount = $('.salesman_commission_amount').val();
            
            if(premium_amount==''){
                premium_amount=0;
            }
            if(gross_incentive==''){
                gross_incentive=0;
            }
            if(gross_incentive != '') {
                var gross_incentive_amount = ((parseFloat(premium_amount) * parseFloat(gross_incentive)) /100).toFixed(2);
                $('.gross_incentive_amount').val(gross_incentive_amount);
                // var gross_incentive_vat = ((parseFloat(gross_incentive_amount) * 5) /100).toFixed(2);
                // $('.gross_incentive_vat').val(gross_incentive_vat);
                var total_gross_incentive_amount = parseFloat(gross_incentive_amount) + parseFloat(gross_incentive_vat);
                $('.total_gross_incentive_amount').val(total_gross_incentive_amount);
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
                    var nt = (parseFloat(salesman_commission_amount, 10) + parseFloat(salesman_incentive_amount, 10));
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
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
                var total_gross_incentive_amount = $('.total_gross_incentive_amount').val();
                var gross_incentive_amount = $('.gross_incentive_amount').val();
                if(total_gross_incentive_amount==''){
                    total_gross_incentive_amount=0;
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
                var total_gross_incentive_amount = $('.total_gross_incentive_amount').val();
                var gross_incentive_amount = $('.gross_incentive_amount').val();
                if(total_gross_incentive_amount==''){
                    total_gross_incentive_amount=0;
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
                var total_gross_incentive_amount = $('.total_gross_incentive_amount').val();
                var gross_incentive_amount = $('.gross_incentive_amount').val();
                if(total_gross_incentive_amount==''){
                    total_gross_incentive_amount=0;
                }
                if(salesman_incentive != '') {
                    var salesman_incentive_amount = ((parseFloat(gross_incentive_amount) * parseFloat(salesman_incentive)) /100).toFixed(2);
                    $('.salesman_incentive_amount').val(salesman_incentive_amount);
                    var nt = (parseFloat(salesman_incentive_amount, 10));
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
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
            var salesman_incentive_amount = $('.salesman_incentive_amount').val();
            if (parseFloat(salesman_commission_amount) >= parseFloat(total_discount)) {
                if(total_discount==''){
                    total_discount=0;
                }
                var total_premium_amount = $('.total_premium_amount').val();
                var grandtotal = parseFloat(total_premium_amount) - parseFloat(total_discount);
                $('.grandTotalamnt').val(grandtotal.toFixed(2, 2));
                
                    var nt = (parseFloat(salesman_commission_amount, 10) + parseFloat(salesman_incentive_amount, 10)) - parseFloat(total_discount);
                    $("#salesmandueAmmount").val(nt.toFixed(2,2));
            }else{
                toastr["error"]('Discount should be less than salesman commission');
            }
        });
        
    });
</script>
<style>
    .form-control,select,.select2-selection__rendered {
        font-size: 11px;
    }
</style>