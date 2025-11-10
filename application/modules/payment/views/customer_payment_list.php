 <div class="row">
            <div class="col-sm-12">
               
 <!--               <?php if($this->permission1->method('customer_payment','create')->access()){ ?>-->
 <!--                 <a href="<?php echo base_url('return_form')?>" class="btn btn-info m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_return')?> </a>-->
 <!--<?php }?>-->
              
            </div>
        </div>
  
 <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body"> 
                        <?php echo form_open('customer_payment_search',array('class' => 'form-inline'))?>

                            <div class="form-group">
                                <label for="to_date"> Customer:</label>
                                
                                <input type="text" size="100" name="customer_name" class=" form-control" placeholder="Customer Name" id="customer_name" tabindex="1" onkeyup="customer_autocomplete()" value="">

                                <input id="autocomplete_customer_id" class="customer_hidden_value abc" type="hidden" name="customer_id" value="">
                            
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
                                <?php if ($customer_payments) { 
                                    $sl = 1;
                                    foreach($customer_payments as $commission){ ?>
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
                                            <?php echo $commission['customer_price']?>            
                                        </td>
                                        <td>
                                            <?php echo $commission['paid_amount']?>         
                                        </td>
                                        <td>
                                            <?php echo $commission['due_amount']?>          
                                        </td>
                                        <td>
                                            <center>
                                                <a href="<?php echo base_url().'customer_payment_edit/'.$commission['customer_pr_id']; ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('details') ?>"><i class="fa fa-window-restore" aria-hidden="true"></i></a>
                                                <!--<?php if($this->permission1->method('customer_return_list','delete')->access()){ ?>-->
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

        
<script type="text/javascript">

function customer_autocomplete(sl) {

    // var customer_id = $('#customer_id').val();
    // Auto complete
    var options = {
        minLength: 0,
        source: function( request, response ) {
            var customer_name = $('#customer_name').val();
         
        $.ajax( {
          url: "<?php echo base_url('payment/customer_autocomplete')?>",
          method: 'post',
          dataType: "json",
          data: {
            term: request.term,
            customer_id:customer_name,
          },
          success: function( data ) {
              
            response( data );

          }
        });
      },
       focus: function( event, ui ) {
           $(this).val(ui.item.label);
           return false;
       },
       select: function( event, ui ) {
            $(this).parent().parent().find("#autocomplete_customer_id").val(ui.item.value); 
            var customer_id          = ui.item.value;

            $(this).unbind("change");
            return false;
       }
   }

   $('body').on('keydown.autocomplete', '#customer_name', function() {
       $(this).autocomplete(options);
   });

}

</script>