 <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo $title ?> </h4>
                        </div>
                    </div>
                   
                    <div class="panel-body">
                            	<?php echo form_open('','class="" id="broker_form"')?>
                            	
                            	<input type="hidden" name="broker_id" id="broker_id" value="<?php echo $broker->broker_id?>">
                                <div class="form-group row">
                                    <label for="broker_name" class="col-sm-2 text-right col-form-label"><?php echo display('broker_name')?> <i class="text-danger"> * </i>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input type="text" name="broker_name" class="form-control" id="broker_name" placeholder="<?php echo display('broker_name')?>" value="<?php echo $broker->broker_name?>">
                                            <input type="hidden" name="old_name" value="<?php echo $broker->broker_name?>">
    
                                        </div>
                                       
                                    </div>
                                     <label for="broker_mobile" class="col-sm-2 text-right col-form-label"><?php echo display('mobile_no')?> <i class="text-danger">  </i>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input type="number" name="broker_mobile" class="form-control input-mask-trigger text-left" id="broker_mobile" placeholder="<?php echo display('mobile_no')?>" value="<?php echo $broker->mobile?>" data-inputmask="'alias': 'decimal', 'groupSeparator': '', 'autoGroup': true" im-insert="true">
    
                                        </div>
                                       
                                    </div>
                                </div>
                                 <div class="form-group row">
                                    <label for="broker_email" class="col-sm-2 text-right col-form-label"><?php echo display('email_address')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input type="email" class="form-control input-mask-trigger" name="broker_email" id="email" data-inputmask="'alias': 'email'" im-insert="true" placeholder="<?php echo display('email')?>" value="<?php echo $broker->emailnumber?>">
    
                                        </div>
                                       
                                    </div>
                                      <label for="email_address" class="col-sm-2 text-right col-form-label"><?php echo display('vat_no')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input type="email" class="form-control" name="email_address" id="email_address" placeholder="<?php echo display('vat_no')?>" value="<?php echo $broker->email_address?>">
    
                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phone" class="col-sm-2 text-right col-form-label"><?php echo display('phone')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                            
                                          <input class="form-control input-mask-trigger text-left" id="phone" type="number" name="phone" placeholder="<?php echo display('phone')?>" data-inputmask="'alias': 'decimal', 'groupSeparator': '', 'autoGroup': true" im-insert="true" value="<?php echo $broker->phone?>">
    
                                        </div>
                                       
                                    </div>

                                     <label for="contact" class="col-sm-2 text-right col-form-label"><?php echo display('contact')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                            
                                          <input class="form-control"  id="contact" type="text" name="contact" placeholder="<?php echo display('contact')?>" value="<?php echo $broker->contact?>">
    
                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address1" class="col-sm-2 text-right col-form-label"><?php echo display('address1')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                            
                                           <textarea name="broker_address" id="broker_address" class="form-control" placeholder="<?php echo display('address1')?>"><?php echo $broker->address?></textarea>
    
                                        </div>
                                      
                                    </div>

                                          <label for="address2" class="col-sm-2 text-right col-form-label"><?php echo display('address2')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                            
                                           <textarea name="address2" id="address2" class="form-control" placeholder="<?php echo display('address2')?>"><?php echo $broker->address2?></textarea>
    
                                        </div>
                                      
                                    </div>
                                </div>
                                <div class="form-group row"> 
                                    <label for="fax" class="col-sm-2 text-right col-form-label"><?php echo display('fax')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                            
                                            <input type="text" name="fax" class="form-control" id="fax" placeholder="<?php echo display('fax')?>" value="<?php echo $broker->fax?>">
    
                                        </div>
                                       
                                    </div>
                                    <label for="city" class="col-sm-2 text-right col-form-label"><?php echo display('city')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                            
                                            <input type="text" name="city" class="form-control" id="city" placeholder="<?php echo display('city')?>" value="<?php echo $broker->city?>">
    
                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="state" class="col-sm-2 text-right col-form-label"><?php echo display('state')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input type="text" name="state" class="form-control" id="state" placeholder="<?php echo display('state')?>"  value="<?php echo $broker->state?>">
    
                                        </div>
                                       
                                    </div>
                                    <label for="zip" class="col-sm-2 text-right col-form-label"><?php echo display('zip')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input name="zip" type="text" class="form-control" id="zip" placeholder="<?php echo display('zip')?>" value="<?php echo $broker->zip?>">
    
                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="country" class="col-sm-2 text-right col-form-label"><?php echo display('country')?>:</label>
                                    <div class="col-sm-4">
                                        <div class="">
                                           
                                            <input name="country" type="text" class="form-control " placeholder="<?php echo display('country')?>" value="<?php echo $broker->country?>" id="country" >
    
                                        </div>
                                       
                                    </div>
                                    <?php if(empty($broker->broker_id)){?>

                                     
                                <?php }?>
                                    
                                </div>

                              

                         <div class="form-group row">
                                   <div class="col-sm-6 text-right">
                                   </div>
                                     <div class="col-sm-6 text-right">
                                        <div class="">
                                           
                                            <button type="button" onclick="broker_form()" class="btn btn-success">
                                            	<?php echo (empty($broker->broker_id)?display('save'):display('update')) ?></button>
    
                                        </div>
                                       
                                    </div>
                                </div>
                               

                                <?php echo form_close();?>
                            </div>
    
                        </div>
                    </div>
                </div>
               
