<script>
    "use strict"; 
    function addaccountReceiptPayment() {
        
        var newdiv = '<input class="form-control invoi_id" type="text" size="100" name="invoi_id" required value="" tabindex="4" />';
        $(".addinvoice").html(newdiv);
    }
    "use strict"; 
    function calculationReceipt(sl) {
       
        var gr_tot = 0;
        $(".paid_amount").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
    
        $("#grandTotal").val(gr_tot.toFixed(2,2));
    }
    "use strict"; 
    function deleteRowReceipt(e) {
        var t = $("#receiptpaymenttable > tbody > tr").length;
        if (1 == t) alert("There only one row you can't delete.");
        else {
            var a = e.parentNode.parentNode;
            a.parentNode.removeChild(a)
        }
        calculationReceipt()
    }
</script>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>
                        <?php echo display('credit_voucher')?>
                    </h4>
                </div>
            </div>
            <div class="panel-body">

                <?php echo  form_open_multipart('broker_receipt_payments_update') ?>
                    
    			    <input class="form-control broker_ids" type="hidden" size="100" name="broker_ids" value="<?php echo html_escape($broker_receipts[0]->broker_id); ?>" tabindex="4" />
    								    
                    <div class="form-group row">
                        <label for="vo_no" class="col-sm-2 col-form-label"><?php echo display('voucher_type')?></label>
                        <div class="col-sm-4">
                            <input type="text" name="txtVNo" id="txtVNo" value="Credit" class="form-control" readonly />
                        </div>
                    </div>
    
                    <div class="form-group row">
                        <label for="ac" class="col-sm-2 col-form-label"><?php echo display('debit_account_head')?>*</label>
                        <div class="col-sm-4">
                            <select name="cmbDebit" id="cmbDebit" class="form-control" required>
                                <option value="" data-isbank="">Select One</option>
                                <?php foreach ($crcc as $cracc) { ?>
                                <option value="<?php echo $cracc->HeadCode?>"
                                    data-isbank="<?php echo $cracc->isBankNature;?>"><?php echo $cracc->HeadName?></option>
                                <?php  } ?>
    
                            </select>
                        </div>
                    </div>
                    <div id="isbanknature" style="display:none">
                        <div class="form-group row">
                            <label for="checkno" class="col-sm-2 col-form-label"><?php echo "Check No";?></label>
                            <div class="col-sm-4">
                                <input type="text" name="checkno" id="checkno" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="CheckDate" class="col-sm-2 col-form-label"><?php echo "Check Date";?></label>
                            <div class="col-sm-4">
                                <input type="text" name="chequeDate" id="chequeDate"
                                    class="form-control datepicker financialyear" value="<?php  echo date('Y-m-d');?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ishonours" class="col-sm-2 col-form-label"><?php echo "Is Honours"?></label>
                            <div class="col-sm-4">
                                <input type="checkbox" value="1" name="ishonours" id="ishonours" size="28">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date" class="col-sm-2 col-form-label"><?php echo display('date')?></label>
                        <div class="col-sm-4">
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker"
                                value="<?php echo  date('Y-m-d')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txtRemarks" class="col-sm-2 col-form-label"><?php echo display('remark')?></label>
                        <div class="col-sm-4">
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="table-responsive">
                        
                            <table class="table table-bordered table-striped table-hover" id="receiptpaymenttable">
                        	<thead>
    							<tr>
    								<th><?php echo display('sl') ?></th>
    								<th>Invoice No</th>
    								<th>Product</th>
    								<th>Total Amount</th>
    								<th>Received Amount</th>
    								<th>Due Amount</th>
    								<th>Amount to pay</th>
    								<th>Received Amount(New)</th>
    								<th>Due Amount(New)</th>
                                    <th><?php echo display('action')?></th>
    							</tr>
    						</thead>
    						<tbody id="receiptvoucher">
    						<?php if ($broker_receipts) {
    						    $sl = 1;
    						    $total = 0;
    						    foreach($broker_receipts as $broker_receipt){ ?>
    							<tr>
    								<td><?php echo $sl?>
        								<input class="form-control" type="hidden" size="100" name="broker_pr_id[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->broker_pr_id); ?>" tabindex="4" />
                                        <input class="form-control" type="hidden" size="100" name="broker_id[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->broker_id); ?>" tabindex="4" />
    								</td>
    								<td><input class="form-control invoice_id" type="text" size="100" name="invoice_id[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->inv_id); ?>" tabindex="4" /></td>
                                    <td><input class="form-control" type="text" size="100" name="product_name[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->product_name); ?>" tabindex="4" /></td>
                                    <td><input class="form-control broker_price" type="text" size="100" name="broker_price[]"  required readonly="readonly" value="<?php echo html_escape($broker_receipt->broker_price); ?>" tabindex="4" /></td>
                                    <td><input class="form-control paid_amount_old" type="text" size="100" name="paid_amount_old[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->paid_amount); ?>" tabindex="4" /></td>
                                    <td><input class="form-control due_amount_old" type="text" size="100" name="due_amount_old[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->due_amount); ?>" tabindex="4" /></td>
                                    <td><input class="form-control paid_amount" type="text" size="100" name="paid_amount[]" required value="<?php echo html_escape($broker_receipt->due_amount); ?>" tabindex="4" /></td>
                                    <td><input class="form-control paid_amount_new" type="text" size="100" name="paid_amount_new[]" required readonly="readonly" value="<?php echo html_escape($broker_receipt->paid_amount + $broker_receipt->due_amount); ?>" tabindex="4" /></td>
                                    <td><input class="form-control due_amount_new" type="text" size="100" name="due_amount_new[]" required readonly="readonly" value="0.00" tabindex="4" /></td>
    								
                                    <td>
                                        <button class="btn btn-danger red text-right" type="button"
                                            value="<?php echo display('delete')?>" onclick="deleteRowReceipt(this)"><i
                                                class="fa fa-trash-o"></i></button>
                                    </td>
    							</tr>
    						
    						<?php $sl++;
    						    $total = $total + $broker_receipt->due_amount;
    							}
    						} else { ?>
    						    <tr><td colspan="10">No entry detected</td></tr>
    						<?php } ?>
    						</tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more"
                                            onClick="addaccountReceiptPayment();"
                                            value="<?php echo display('add_more') ?>" />
                                    </td>
                                    <td class="addinvoice"></td>
                                    <td colspan="4" class="text-right"><label for="reason"
                                            class="  col-form-label"><?php echo display('total') ?></label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right "
                                            name="grand_total" value="<?= $total ?>" readonly="readonly" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <div class="form-group form-group-margin row">
    
                        <div class="col-sm-12 text-right">
    
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save"
                                value="<?php echo display('save') ?>" tabindex="9" />
                            <input type="hidden" name="" id="base_url" value="<?php echo base_url();?>">
                            <input type="hidden" name="" id="headoption"
                                value="<option value=''> Please select</option><?php foreach ($acc as $acc2) {?><option value='<?php echo $acc2->HeadCode;?>'><?php echo $acc2->HeadName;?></option><?php }?>">
    
                        </div>
                    </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/dist/account.js') ?>" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $(document).on("change", ".invoi_id", function (e) {
            e.preventDefault();
            var invoice_id = $(this).val();
            var broker_id = $('.broker_ids').val();
            var base_url = $("#base_url").val();
            var row = $("#receiptvoucher tr").length;
            var count = row + 1;
            $.ajax({
                url : base_url + "receipt/receipt/cloudsubset_broker_receipt_detail/" + invoice_id +"/" + broker_id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    var datas = '<tr><td>'+count+data+'</tr>';
                    $("#receiptvoucher").append(datas);
                    var gr_tot = 0;
                    $(".paid_amount").each(function() {
                        isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
                    });
                
                    $(".addinvoice").html('');
                    $("#grandTotal").val(gr_tot.toFixed(2,2));
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });
        });
        
        
        $(document).on("change", ".paid_amount", function (e) {
            e.preventDefault();
            var paid_amount = $(this).val();
            var broker_price = $(this).parent().parent().find('.broker_price').val();
            var paid_amount_old = $(this).parent().parent().find('.paid_amount_old').val();
            if(paid_amount==''){
                paid_amount=0;
            }
            var paid_amount_new = (parseFloat(paid_amount_old) + parseFloat(paid_amount)).toFixed(2,2);
            var due_amount_new = (parseFloat(broker_price) - parseFloat(paid_amount_old) - parseFloat(paid_amount)).toFixed(2,2);
            
            if (parseFloat(paid_amount_new) > parseFloat(broker_price)) {
    
                toastr["error"]('Paying amount is greater than Total');
            }else{
                $(this).parent().parent().find('.due_amount_new').val(due_amount_new);
                $(this).parent().parent().find('.paid_amount_new').val(paid_amount_new);
                
                
                var gr_tot = 0;
                $(".paid_amount").each(function() {
                    isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
                });
            
                $("#grandTotal").val(gr_tot.toFixed(2,2));
            }
        });
    });
</script>