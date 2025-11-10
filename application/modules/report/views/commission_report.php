<!-- commission report -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo form_open('datewise_commission_report', array('class' => 'form-inline', 'method' => 'get')) ?>
                <?php
                        $today = date('Y-m-d');
                        ?>
                <div class="form-group">
                    <label class="" for="from_date"><?php echo display('start_date') ?></label>
                    <input type="text" name="from_date" class="form-control datepicker" id="from_date"
                        placeholder="<?php echo display('start_date') ?>" value="<?php echo $today ?>">
                </div>

                <div class="form-group">
                    <label class="" for="to_date"><?php echo display('end_date') ?></label>
                    <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                        placeholder="<?php echo display('end_date') ?>" value="<?php echo $today ?>">
                </div>

                <button type="button" id="btn-filter" class="btn btn-success"><?php echo display('find') ?></button>
                <a class="btn btn-warning" href="#"
                    onclick="printDiv('purchase_div')"><?php echo display('print') ?></a>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <!--<div class="panel-heading">-->
            <!--    <div class="panel-title">-->
            <!--        <span><?php echo display('commission_report') ?> </span>-->
            <!--        <span class="padding-lefttitle">-->
            <!--            <?php if($this->permission1->method('all_report','read')->access()){ ?>-->
            <!--            <a class="btn btn-primary m-b-5 m-r-2"-->
            <!--                href="<?php echo base_url('todays_report') ?>"><?php echo display('todays_report') ?></a>-->
            <!--            <?php } ?>-->
            <!--            <?php if($this->permission1->method('todays_purchase_report','read')->access()){ ?>-->
            <!--            <a href="<?php echo base_url('purchase_report') ?>" class="btn btn-success m-b-5 m-r-2"><i-->
            <!--                    class="ti-align-justify"> </i> <?php echo display('purchase_report') ?> </a>-->
            <!--            <?php }?>-->
            <!--            <?php if($this->permission1->method('product_commission_reports_date_wise','read')->access()){ ?>-->
            <!--            <a href="<?php echo base_url('product_wise_commission_report') ?>"-->
            <!--                class="btn btn-primary m-b-5 m-r-2"><i class="ti-align-justify"> </i>-->
            <!--                <?php echo display('commission_report_product_wise') ?> </a>-->
            <!--            <?php }?>-->
            <!--            <?php if($this->permission1->method('todays_commission_report','read')->access() && $this->permission1->method('todays_purchase_report','read')->access()){ ?>-->
            <!--            <a href="<?php echo base_url('profit_report') ?>" class="btn btn-warning m-b-5 m-r-2"><i-->
            <!--                    class="ti-align-justify"> </i> <?php echo display('profit_report') ?> </a>-->
            <!--            <?php }?></span>-->
            <!--    </div>-->
            <!--</div>-->
            <div class="panel-body">
                <div id="purchase_div">
                    <div class="paddin5ps">
                        <table class="print-table" width="100%">

                            <tr>
                                <td align="left" class="print-table-tr">
                                    <img src="<?php echo base_url().$setting->logo;?>" alt="logo">
                                </td>
                                <td align="center" class="print-cominfo">
                                    <span class="company-txt">
                                        <?php echo $company_info[0]['company_name'];?>

                                    </span><br>
                                    <?php echo $company_info[0]['address'];?>
                                    <br>
                                    <?php echo $company_info[0]['email'];?>
                                    <br>
                                    <?php echo $company_info[0]['mobile'];?>
                                    <br>
                                    <strong><?php echo display('commission_report')?></strong>
                                </td>

                                <td align="right" class="print-table-tr">
                                    <date>
                                        <?php echo display('date')?>: <?php
                                                        echo date('d-M-Y');
                                                        ?>
                                    </date>

                                </td>
                            </tr>

                        </table>
                    </div>
                    <div class="table-responsive paddin5ps" style="overflow-x:scroll">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="reportlist">
                            <thead>
                                <tr>
                                    <th rowspan="2"><?php echo display('invoice_no') ?></th>
                                    <th rowspan="2"><?php echo display('sales_date') ?></th>
                                    <th rowspan="2">Document Date</th>
                                    <th rowspan="2">Insured/Client</th>
                                    <th rowspan="2">Insurer</th>
                                    <th rowspan="2">Policy Type</th>
                                    <th rowspan="2">Product</th>
                                    <th rowspan="2">Policy/ Endorsement No.</th>
                                    <th colspan="3">Premium</th>
                                    <th colspan="4">Gross Commission</th>
                                    <th rowspan="2">Broker Name</th>
                                    <th colspan="2">Broker Commission</th>
                                    <th colspan="2">Aggregator Fees</th>
                                    <th rowspan="2">Salesman Name</th>
                                    <th colspan="2">Salesman Commission</th>
                                    <th colspan="2">Premium Settlment Status</th>
                                    <th colspan="2">Broker Settlment Status</th>
                                    <th colspan="2">Salesman Settlment Status</th>
                                    <!--<th rowspan="2">Ageing</th>-->
                                    <!--<th rowspan="2">Remarks</th>-->
                                    <th rowspan="2">Type</th>
                                    <!--<th rowspan="2"><?php echo display('total_amount') ?></th>-->
                                </tr>
                                <tr>
                                    <th><p style="display: none;">Premium </p>Amount</th>
                                    <th><p style="display: none;">Premium </p>Vat</th>
                                    <th><p style="display: none;">Premium </p>Total</th>
                                    <th><p style="display: none;">Premium </p>%</th>
                                    <th><p style="display: none;">Gross Commission </p>Amount</th>
                                    <th><p style="display: none;">Gross Commission </p>Vat</th>
                                    <th><p style="display: none;">Gross Commission </p>Total</th>
                                    <th><p style="display: none;">Broker Commission </p>%</th>
                                    <th><p style="display: none;">Broker Commission </p>Amount</th>
                                    <th><p style="display: none;">Aggregator Fees </p>%</th>
                                    <th><p style="display: none;">Aggregator Fees </p>Amount</th>
                                    <th><p style="display: none;">Salesman Commission </p>%</th>
                                    <th><p style="display: none;">Salesman Commission </p>Amount</th>
                                    <th><p style="display: none;">Premium Settlment Status </p>Settled</th>
                                    <th><p style="display: none;">Premium Settlment Status </p>Outstanding</th>
                                    <th><p style="display: none;">Broker Settlment Status </p>Settled</th>
                                    <th><p style="display: none;">Broker Settlment Status </p>Outstanding</th>
                                    <th><p style="display: none;">Salesman Settlment Status </p>Settled</th>
                                    <th><p style="display: none;">Salesman Settlment Status </p>Outstanding</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                            <!--<tfoot>-->
                            <!--    <th colspan="25" class="text-right"><?php echo display('total_purchase') ?>:</th>-->
                            <!--    <th></th>-->
                            <!--</tfoot>-->
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('my-assets/js/admin_js/commission_report.js') ?>" type="text/javascript"></script>

