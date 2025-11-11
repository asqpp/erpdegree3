 <div class="row">
            <div class="col-sm-12">
               
 <!--               <?php if($this->permission1->method('supplier_payment','create')->access()){ ?>-->
 <!--                 <a href="<?php echo base_url('return_form')?>" class="btn btn-info m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_return')?> </a>-->
 <!--<?php }?>-->
              
            </div>
        </div>
  
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body"> 
                       
                        <?php echo form_open('supplier_payment_search',array('class' => 'form-inline'))?>

                            <div class="form-group">
                                <label for="to_date"> Supplier:</label>
                                <select name="supplier_id" class="form-control" required="required">
                                        <option value=""> select Supplier</option>
                                        <?php if ($supplier) { ?>
                                        <?php foreach($supplier as $suppliers){?>
                                        <option value="<?php echo $suppliers['supplier_id']?>"  <?php if($supplier_id == $suppliers['supplier_id']) { echo 'selected'; } ?>>
                                            <?php echo $suppliers['supplier_name']?></option>

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
                        <?php echo form_open('supplier_payment_clear',array('class' => 'form-inline'))?>
                            <input type="hidden" name="supplier_id" value="<?= $supplier_id ?>">
                            <?php if ($supplier_payments) { ?>
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
                                            <th>Paid Amount</th>
                                            <th>Due Amount</th>
                                            <th><?php echo display('action') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($supplier_payments) { 
                                        $sl = 1;
                                        foreach($supplier_payments as $commission){ ?>
                                        <tr>
    								        <td><input type="checkbox" name="supplier_pr_id[]" value="<?= $commission['supplier_pr_id']; ?>" class="checkBoxClass"></td>
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
                                                <?php echo $commission['supplier_price']?>            
                                            </td>
                                            <td>
                                                <?php echo $commission['paid_amount']?>         
                                            </td>
                                            <td>
                                                <?php echo $commission['due_amount']?>          
                                            </td>
                                            <td>
                                                <center>
                                                    <a href="<?php echo base_url().'supplier_payment_edit/'.$commission['supplier_pr_id']; ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('details') ?>"><i class="fa fa-window-restore" aria-hidden="true"></i></a>
                                                    <!--<?php if($this->permission1->method('supplier_return_list','delete')->access()){ ?>-->
                                                    <!--    <a href="<?php echo base_url().'return/returns/delete_retutn_purchase/'.$returns['purchase_id']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="left" onclick="return confirm('Are you sure??')" title="<?php echo display('delete') ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                    <!--<?php }?>-->
                                                </center>
                                            </td>
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
