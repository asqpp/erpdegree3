  <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('salesman_advance') ?> </h4>
                        </div>
                    </div>
                    <?php echo form_open('salesman/salesman/insert_salesman_advance', array('class' => 'form-vertical', 'id' => 'insert_salesman_adavance')) ?>
                    <div class="panel-body">

                        <div class="form-group row">
                            <label for="salesman_name" class="col-sm-3 col-form-label"><?php echo display('salesman_name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                            <select name="salesman_id" class="form-control"  required="">
                            <option value=""><?php echo display('salesman_name') ?></option>
                                <?php foreach($salesman_list as $salesmans){?>
                            <option value="<?php echo html_escape($salesmans['salesman_id'])?>"><?php echo html_escape($salesmans['salesman_name'])?></option>
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
                                <input type="submit" id="add-salesman-advance" class="btn btn-primary btn-large" name="add-salesman-advance" value="<?php echo display('save') ?>" />
                              
                            </div>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>

