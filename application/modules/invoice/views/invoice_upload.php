<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Upload Invoice</h4>
                </div>
            </div>
            <?php echo form_open_multipart('invoice/invoice/upload_invoice', array('class' => 'form-vertical', 'id' => 'upload_invoice')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_id" class="col-sm-4 col-form-label"><?php echo display('supplier') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select name="supplier_id" class="form-control" required="">
                                        <option value=""> Select Supplier</option>
                                        <?php if ($supplier) { ?>
                                        <?php foreach($supplier as $suppliers){?>
                                        <option value="<?php echo $suppliers['supplier_id']?>"
                                        <?php if($supplier_pr[0]['supplier_id']==$suppliers['supplier_id']){echo 'selected';}?>
                                        >
                                            <?php echo $suppliers['supplier_name']?></option>

                                        <?php }} ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Invoice :</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                              <input class="form-control" type="file" id="invoice" name="invoice" placeholder="Select a Invoice file" required=""> 
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Policy File :</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                              <input class="form-control" type="file" id="pdf" name="pdf" placeholder="Select a PDF file"> 
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Commission :</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                              <input class="form-control" type="file" id="commission" name="commission" placeholder="Select a commission file"> 
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Incentive :</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                              <input class="form-control" type="file" id="incentive" name="incentive" placeholder="Select a incentive file"> 
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                </div>
                <br>
                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <input type="submit" id="upload_invoice_submit" class="btn btn-success" name="upload_invoice_submit"
                            value="<?php echo display('submit') ?>" tabindex="17" />

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>