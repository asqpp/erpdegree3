  <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('broker_advance') ?> </h4>
                        </div>
                    </div>
                    <?php echo form_open('broker/broker/insert_broker_advance', array('class' => 'form-vertical', 'id' => 'insert_broker_adavance')) ?>
                    <div class="panel-body">

                        <div class="form-group row">
                            <label for="broker_name" class="col-sm-3 col-form-label"><?php echo display('broker_name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                            <select name="broker_id" class="form-control"  required="">
                            <option value=""><?php echo display('broker_name') ?></option>
                                <?php foreach($broker_list as $brokers){?>
                            <option value="<?php echo html_escape($brokers['broker_id'])?>"><?php echo html_escape($brokers['broker_name'])?></option>
                                <?php }?>   
                            </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="advance_type" class="col-sm-3 col-form-label"><?php echo display('advance_type') ?><i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <select name="type" class="form-control" required="">
                                   <option value=""> <?php echo display('advance_type') ?></option>
                                   <option value="1"> <?php echo display('payment') ?> </option>
                                   <option value="2"> <?php echo display('receive') ?></option>
                               </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="amount" class="col-sm-3 col-form-label"><?php echo display('amount') ?><i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input class="form-control" name ="amount" id="amount" type="number" placeholder="<?php echo display('amount') ?>" required min="0" tabindex="3">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="submit" id="add-broker-advance" class="btn btn-primary btn-large" name="add-broker-advance" value="<?php echo display('save') ?>" />
                              
                            </div>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>

