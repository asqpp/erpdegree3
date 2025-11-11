<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('invoice_edit') ?></h4>
                </div>
            </div>
            <?php echo form_open('payment/payment/cloudsubset_update_supplier_payment/'.$supplier_payment->supplier_pr_id, array('class' => 'form-vertical', 'id' => 'update_supplier_payment')) ?>
            <div class="panel-body">
                <input class="form-control" type="hidden" size="100" name="supplier_pr_id" required readonly="readonly" value="<?php echo html_escape($supplier_payment->supplier_pr_id); ?>" tabindex="4" />
                <input class="form-control" type="hidden" size="100" name="supplier_id" required readonly="readonly" value="<?php echo html_escape($supplier_payment->supplier_id); ?>" tabindex="4" />
                         
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Invoice No :</label>
                         <div class="col-sm-8">
                             
                            <input class="form-control" type="text" size="100" name="invoice_id" required readonly="readonly" value="<?php echo html_escape($supplier_payment->invoice_id); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Product :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="product_name" required readonly="readonly" value="<?php echo html_escape($supplier_payment->product_name); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Total Amount :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="supplier_price" id="supplier_price" required readonly="readonly" value="<?php echo html_escape($supplier_payment->supplier_price); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Paid Amount :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="paid_amount_old" id="paid_amount_old" required readonly="readonly" value="<?php echo html_escape($supplier_payment->paid_amount); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Due Amount :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="due_amount_old" id="due_amount_old" required readonly="readonly" value="<?php echo html_escape($supplier_payment->due_amount); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Amount to pay :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="paid_amount" id="paid_amount" required value="0.00" tabindex="4" />
                         </div>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Paid Amount(New) :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="paid_amount_new" id="paid_amount_new" required readonly="readonly" value="<?php echo html_escape($supplier_payment->paid_amount); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="invoice_id" class="col-sm-4 col-form-label">Due Amount(New) :</label>
                         <div class="col-sm-8">
                            <input class="form-control" type="text" size="100" name="due_amount_new" id="due_amount_new" required readonly="readonly" value="<?php echo html_escape($supplier_payment->due_amount); ?>" tabindex="4" />
                         </div>
                      </div>
                    </div>
                </div>
                <br>
                <?php if ($supplier_payment_dates) { ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo display('sl') ?></th>
                                        <th>Paid Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($supplier_payment_dates) { 
                                    $sl = 1;
                                    foreach($supplier_payment_dates as $row){ ?>
                                    <tr>
                                        <td><?php echo $sl?></td>
                                        <td>
                                            <?php echo $row['Debit']?>            
                                        </td>
                                        <td>
                                            <?php echo $row['CreateDate']?>            
                                        </td>
                                    </tr>
                                
                                <?php $sl++;
                                    }
                                } else { ?>
                                    <tr><td colspan="3">No entry detected</td></tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <input type="submit" id="add_commission" class="btn btn-success" name="add-commission"
                            value="<?php echo display('submit') ?>" tabindex="17" />

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#paid_amount').on('change', function () {
            var paid_amount = $(this).val();
            var supplier_price = $('#supplier_price').val();
            var paid_amount_old = $('#paid_amount_old').val();
            if(paid_amount==''){
                paid_amount=0;
            }
            var paid_amount_new = (parseFloat(paid_amount_old) + parseFloat(paid_amount)).toFixed(2,2);
            var due_amount_new = (parseFloat(supplier_price) - parseFloat(paid_amount_old) - parseFloat(paid_amount)).toFixed(2,2);
            
            if (parseFloat(paid_amount_new) > parseFloat(supplier_price)) {
    
                toastr["error"]('Paying amount is greater than Total');
            }else{
                $('#due_amount_new').val(due_amount_new);
                $('#paid_amount_new').val(paid_amount_new);
            }
        });
    });
</script>