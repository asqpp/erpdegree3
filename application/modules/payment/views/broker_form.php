         <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                     <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Commission for Broker</h4>
                        </div>
                    </div>
                    <div class="panel-body"> 
                        <?php echo form_open('broker_commission_search',array('class' => 'form-inline'))?>

                            <div class="form-group">
                                <label for="to_date"> Broker:</label>
                                <select name="broker_id" class="form-control" required="required">
                                        <option value=""> select Broker</option>
                                        <?php if ($broker) { ?>
                                        <?php foreach($broker as $brokers){?>
                                        <option value="<?php echo $brokers['broker_id']?>">
                                            <?php echo $brokers['broker_name']?></option>

                                        <?php }} ?>
                                </select>
                            </div>  

                            <button type="submit" class="btn btn-success"><?php echo display('search') ?></button>
                       <?php echo form_close()?>
                    </div>
                </div>
            </div>
        </div>
