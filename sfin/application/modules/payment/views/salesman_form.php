         <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                     <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Commission for salesman</h4>
                        </div>
                    </div>
                    <div class="panel-body"> 
                        <?php echo form_open('salesman_commission_search',array('class' => 'form-inline'))?>

                            <div class="form-group">
                                <label for="to_date"> Salesman:</label>
                                <select name="salesman_id" class="form-control" required="required">
                                        <option value=""> select Salesman</option>
                                        <?php if ($salesman) { ?>
                                        <?php foreach($salesman as $salesmans){?>
                                        <option value="<?php echo $salesmans['salesman_id']?>">
                                            <?php echo $salesmans['salesman_name']?></option>

                                        <?php }} ?>
                                </select>
                            </div>  

                            <button type="submit" class="btn btn-success"><?php echo display('search') ?></button>
                       <?php echo form_close()?>
                    </div>
                </div>
            </div>
        </div>
