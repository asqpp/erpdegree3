 <div class="row">
            <div class="col-sm-12">
               
 <!--               <?php if($this->permission1->method('broker_commission','create')->access()){ ?>-->
 <!--                 <a href="<?php echo base_url('return_form')?>" class="btn btn-info m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_return')?> </a>-->
 <!--<?php }?>-->
              
            </div>
        </div>
  
 <div class="row">
			<div class="col-sm-12">
		        <div class="panel panel-default">
		            <div class="panel-body"> 
		               <!-- <?php echo form_open('supplier_return_search',array('class' => 'form-inline'))?>-->
		               <!-- <?php  $today = date('Y-m-d'); ?>-->
		               <!--     <div class="form-group">-->
		               <!--         <label class="" for="from_date"><?php echo display('start_date') ?></label>-->
		               <!--         <input type="text" name="from_date" class="form-control datepicker" id="from_date" value="<?php echo $today?>" placeholder="<?php echo display('start_date') ?>" >-->
		               <!--     </div> -->

		               <!--     <div class="form-group">-->
		               <!--         <label class="" for="to_date"><?php echo display('end_date') ?></label>-->
		               <!--         <input type="text" name="to_date" class="form-control datepicker" id="to_date" placeholder="<?php echo display('end_date') ?>" value="<?php echo $today?>">-->
		               <!--     </div>  -->

		               <!--     <button type="submit" class="btn btn-success"><?php echo display('search') ?></button>-->
		                  
		               <!--<?php echo form_close()?>-->
		            </div>
		        </div>
		    </div>
	    </div>

		<div class="row">
		    <div class="col-sm-12">
		        <div class="panel panel-bd lobidrag">
		            
		            <div class="panel-body">
		                <div class="table-responsive">
		                    <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
		                    	<thead>
									<tr>
										<th><?php echo display('sl') ?></th>
										<th>Invoice No</th>
										<th>Document Date</th>
										<th>Policy No</th>
										<th>Customer Name</th>
										<th>Total Amount</th>
										<th>Paid Amount</th>
										<th>Due Amount</th>
										<th><?php echo display('action') ?></th>
									</tr>
								</thead>
								<tbody>
								<?php if ($broker_commissions) { 
								    $sl = 1;
								    foreach($broker_commissions as $commission){ ?>
									<tr>
										<td><?php echo $sl?></td>
										<td>
											<a href="<?php echo base_url().'invoice_edit/'.$commission['invoice_id']; ?>">
											<?php echo $commission['inv_id']?>
											</a>
										</td>
                                        <td>
                                            <?php echo $commission['document_date']?>            
                                        </td>
                                        <td>
                                            <?php echo $commission['policy_no']?>            
                                        </td>
										<td>
											<?php echo $commission['customer_name']?>			
										</td>
										<td>
											<?php echo $commission['broker_price']?>			
										</td>
										<td>
											<?php echo $commission['paid_amount']?>			
										</td>
										<td>
											<?php echo $commission['due_amount']?>			
										</td>
										<td>
											<center>
                                                <a href="<?php echo base_url().'broker_commission_edit/'.$commission['broker_pr_id']; ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('details') ?>"><i class="fa fa-window-restore" aria-hidden="true"></i></a>
                                                <!--<?php if($this->permission1->method('supplier_return_list','delete')->access()){ ?>-->
                                                <!--    <a href="<?php echo base_url().'return/returns/delete_retutn_purchase/'.$returns['purchase_id']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="left" onclick="return confirm('Are you sure??')" title="<?php echo display('delete') ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                <!--<?php }?>-->
											</center>
										</td>
									</tr>
								
								<?php $sl++;
									}
								} else { ?>
								    <tr><td colspan="7">No entry detected</td></tr>
								<?php } ?>
								</tbody>
		                    </table>
		                    <div class="text-right"><?php echo $links?></div>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
