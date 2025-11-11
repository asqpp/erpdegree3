 <div class="row">
            <div class="col-sm-12">
               
 <!--               <?php if($this->permission1->method('broker_receipt','create')->access()){ ?>-->
 <!--                 <a href="<?php echo base_url('return_form')?>" class="btn btn-info m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_return')?> </a>-->
 <!--<?php }?>-->
              
            </div>
        </div>
  
 <div class="row">
	<div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body"> 
                <?php echo form_open('broker_receipt_search',array('class' => 'form-inline'))?>

                    <div class="form-group">
                        <label for="to_date"> Broker:</label>
                        <select name="broker_id" class="form-control" required="required">
                                <option value=""> select Broker</option>
                                <?php if ($brokers) { ?>
                                <?php foreach($brokers as $brokr){?>
                                <option value="<?php echo $brokr['broker_id']?>" <?php if($broker_id == $brokr['broker_id']) { echo 'selected'; } ?>>
                                    <?php echo $brokr['broker_name']?></option>

                                <?php }} ?>
                        </select>
                    </div>  

                    <button type="submit" class="btn btn-success"><?php echo display('search') ?></button>
               <?php echo form_close()?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            
            <div class="panel-body">
                
                <?php echo form_open('broker_receipt_payment',array('class' => 'form-inline'))?>
                    <input type="hidden" name="broker_id" value="<?= $broker_id ?>">
                    <?php if ($broker_receipts) { ?>
                        <button type="submit" class="btn btn-success">Clear Due</button>
                    <?php } ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                        	<thead>
    							<tr>
    								<td><input type="checkbox" id="selectAll" ></td>
    								<th><?php echo display('sl') ?></th>
    								<th>Invoice No</th>
    								<th>Document Date</th>
    								<th>Policy No</th>
    								<th>Customer Name</th>
    								<th>Total Amount</th>
    								<th>Received Amount</th>
    								<th>Due Amount</th>
    								<!--<th><?php echo display('action') ?></th>-->
    							</tr>
    						</thead>
						<tbody>
						<?php if ($broker_receipts) { 
						    $sl = 1;
						    foreach($broker_receipts as $receipt){ ?>
							<tr>
								<td><input type="checkbox" name="broker_pr_id[]" value="<?= $receipt['broker_pr_id']; ?>" class="checkBoxClass"></td>
								<td><?php echo $sl?></td>
								<td>
									<a href="<?php echo base_url().'invoice_edit/'.$receipt['invoice_id']; ?>">
									<?php echo $receipt['inv_id']?>
									</a>
								</td>
                                <td>
                                    <?php echo $receipt['document_date']?>            
                                </td>
                                <td>
                                    <?php echo $receipt['policy_no']?>            
                                </td>
								<td>
									<?php echo $receipt['customer_name']?>			
								</td>
								<td>
									<?php echo $receipt['broker_price']?>			
								</td>
								<td>
									<?php echo $receipt['paid_amount']?>			
								</td>
								<td>
									<?php echo $receipt['due_amount']?>			
								</td>
								<!--<td>-->
								<!--	<center>-->
        <!--                                <a href="<?php echo base_url().'broker_receipt_edit/'.$receipt['broker_pr_id']; ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('details') ?>"><i class="fa fa-window-restore" aria-hidden="true"></i></a>-->
                                        <!--<?php if($this->permission1->method('supplier_return_list','delete')->access()){ ?>-->
                                        <!--    <a href="<?php echo base_url().'return/returns/delete_retutn_purchase/'.$returns['purchase_id']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="left" onclick="return confirm('Are you sure??')" title="<?php echo display('delete') ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                        <!--<?php }?>-->
								<!--	</center>-->
								<!--</td>-->
							</tr>
						
						<?php $sl++;
							}
						} else { ?>
						    <tr><td colspan="10">No entry detected</td></tr>
						<?php } ?>
						</tbody>
                    </table>
                    <div class="text-right"><?php echo $links?></div>
                </div>
                <?php echo form_close()?>
            </div>
        </div>
    </div>
</div>