         <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                     <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Commission for Supplier</h4>
                        </div>
                    </div>
                    <div class="panel-body"> 
                        <?php echo form_open('supplier_payment_search',array('class' => 'form-inline'))?>

                            <div class="form-group">
                                <label for="to_date"> Supplier:</label>
                                <select name="supplier_id" class="form-control" required="required">
                                        <option value=""> select Supplier</option>
                                        <?php if ($supplier) { ?>
                                        <?php foreach($supplier as $suppliers){?>
                                        <option value="<?php echo $suppliers['supplier_id']?>">
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