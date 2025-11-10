         <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                     <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Commission for Customer</h4>
                        </div>
                    </div>
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