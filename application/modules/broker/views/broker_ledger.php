
             <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body"> 
                        <?php echo form_open('broker_ledgerdata', array('class' => '', 'id' => 'validate')) ?>
                        <?php $today = date('Y-m-d'); ?>
                       <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="broker_name" class="col-sm-4 col-form-label"><?php echo display('broker') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select name="broker_id"  class="form-control" required="">
                                    <option value=""></option>
                                   <?php foreach($broker as $brokers){?>
                                    <option value="<?php echo html_escape($brokers['broker_id'])?>"  <?php if($brokers['broker_id'] == $broker_id){echo 'selected';}?>><?php echo html_escape($brokers['broker_name'])?></option>
                                   <?php }?>
                                </select>
                            </div>
                            </div>
                            </div> 
                            <div class="col-sm-5">
                        <div class="form-group row">
                                <label for="from_date " class="col-sm-2 col-form-label"> <?php echo display('from') ?></label>
                                <div class="col-sm-4">
                                    <input type="text" name="from_date"  value="<?php echo (!empty($start)?$start:$today); ?>" class="datepicker form-control" id="from_date"/>
                                </div>
                                 <label for="to_date" class="col-sm-2 col-form-label"> <?php echo display('to') ?></label>
                                <div class="col-sm-4">
                                    <input type="text" name="to_date" value="<?php echo (!empty($end)?$end:$today); ?>" class="datepicker form-control" id="to_date"/>
                                </div>
                          
                        </div>
                    </div>

                       <div class="col-sm-3">
                           
                                <button type="submit" class="btn btn-success "><i class="fa fa-search-plus" aria-hidden="true"></i> <?php echo display('search') ?></button>
                                <button type="button" class="btn btn-warning"  onclick="printDiv('printableArea')"><?php echo display('print') ?></button>
                        
                        
                    </div>
                        <?php echo form_close() ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- broker ledger -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <span><?php echo display('broker_ledger') ?></span>
                            <span class="padding-lefttitle">      <?php if($this->permission1->method('add_broker','create')->access()){ ?>
                    <a href="<?php echo base_url('add_broker') ?>" class="btn btn-info m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_broker') ?> </a>
                   <?php }?>
                <?php if($this->permission1->method('manage_broker','read')->access()){ ?>
                    <a href="<?php echo base_url('broker_list') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-align-justify"> </i>  <?php echo display('manage_broker') ?> </a>
                 <?php }?></span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="printableArea">

                            <?php if ($broker_name) { ?>
                                <div class="text-center">
                                    <h3> <?php echo $broker_name;?> </h3>
                                    <h4><?php echo display('address') ?> : <?php echo $address?> </h4>
                                    <h4>Period: <?php echo date("d/m/Y", strtotime($start)); ?> to <?php echo date("d/m/Y", strtotime($end)); ?> </h4>
                                </div>
                            <?php } ?>

                            <table class="print-table print-font-size" width="100%">
                                <tr>
                                    <?php $CurBalance =$pre_balance;?>
                                    <td align="right" class="print-table-tr">
                                        <date> <?php echo display('date')?>: <?php echo date('d-M-Y'); ?> </date><br>
                                        <span style="margin-left: 10px; margin-top: 15px;font-weight: 600;">
                                            <?php echo display('opening_balance')?> :
                                            <?php echo $currency. ' '.  number_format($pre_balance,2,'.',','); ?>
                                            <!--<br /> <?php echo display('closing_balance')?> :-->
                                            <!--<?php echo $currency. ' '.  number_format($CurBalance,2,'.',','); ?>-->
                                        </span>
                                    </td>
                                </tr>
        
                            </table>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?php echo display('date') ?></th>
                                            <th class="text-center"><?php echo display('description') ?></th>
                                            <th class="text-center"><?php echo display('voucher_no') ?></th>
                                            <th class="text-right"><?php echo display('debit') ?></th>
                                            <th class="text-right"><?php echo display('credit') ?></th>
                                            <th class="text-right"><?php echo display('balance') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($ledgers) {
                                            $sl = 0;
                                            $debit = $credit = $balance = 0;
                                            foreach ($ledgers as $ledger) {
                                                $sl++;
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo html_escape($ledger['VDate']); ?></td>
                                                    <td><?php echo html_escape($ledger['Narration']); ?> <?php echo html_escape($ledger['customer_name']); ?></td>
                                                    <td>
                                                 
                                                    <?php echo html_escape($ledger['VNo']); ?>
                                                </td>
                                                   
                                                    <td align="right">
                                                        <?php
                                                        
                                                            // echo (($position == 0) ? "$currency " : " $currency");
                                                            echo html_escape(number_format($ledger['Debit'], 2, '.', ','));
                                                            $debit += $ledger['Debit'];
                                                       
                                                        ?>
                                                    </td>
                                                    <td align="right">
                                                        <?php
                                                        
                                                            // echo (($position == 0) ? "$currency " : " $currency");
                                                            echo html_escape(number_format($ledger['Credit'], 2, '.', ','));
                                                            $credit += $ledger['Credit'];
                                                      
                                                        ?>
                                                    </td>
                                                    <td align='right'>
                                                        <?php
                                                        if($ledger['Debit'] > 0) {
                                                            $balance += $ledger['Debit'];
                                                        }
                                                        if($ledger['Credit'] > 0) {
                                                            $balance -= $ledger['Credit'];
                                                        }         
                                                        // echo (($position == 0) ? "$currency " : " $currency");
                                                        echo html_escape(number_format($balance, 2, '.', ','));
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }else{
                                        ?>
                                        <tr><td colspan="6"><center>No Record Found</center></td></tr>
                                        
                                        <?php }?>
                                    
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" align="right"><b><?php echo display('grand_total') ?>:</b></td>
                                            <td align="right"><b><?php
                                                    // echo (($position == 0) ? "$currency " : "$currency");
                                                    echo html_escape(number_format((@$debit), 2, '.', ','));
                                                    ?></b>
                                            </td>
                                            <td align="right"><b><?php
                                                    // echo (($position == 0) ? "$currency " : "$currency");
                                                    echo html_escape(number_format((@$credit), 2, '.', ','));
                                                    ?></b>
                                            </td>
                                            <td align="right"><b><?php
                                                    // echo (($position == 0) ? "$currency " : "$currency");
                                                    echo html_escape(number_format((@$balance), 2, '.', ','));
                                                    ?></b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                            </div>
                        </div>
                        <div class="text-right"><?php echo $links ?></div>
                    </div>
                </div>
            </div>
        </div>