<!-- Sales report -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo form_open('datewise_sales_report', array('class' => 'form-inline', 'method' => 'get')) ?>
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
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('sales_report') ?> </span>
                    <span class="padding-lefttitle">
                        <?php if($this->permission1->method('all_report','read')->access()){ ?>
                        <a class="btn btn-primary m-b-5 m-r-2"
                            href="<?php echo base_url('todays_report') ?>"><?php echo display('todays_report') ?></a>
                        <?php } ?>
                        <?php if($this->permission1->method('todays_purchase_report','read')->access()){ ?>
                        <a href="<?php echo base_url('purchase_report') ?>" class="btn btn-success m-b-5 m-r-2"><i
                                class="ti-align-justify"> </i> <?php echo display('purchase_report') ?> </a>
                        <?php }?>
                        <?php if($this->permission1->method('product_sales_reports_date_wise','read')->access()){ ?>
                        <a href="<?php echo base_url('product_wise_sales_report') ?>"
                            class="btn btn-primary m-b-5 m-r-2"><i class="ti-align-justify"> </i>
                            <?php echo display('sales_report_product_wise') ?> </a>
                        <?php }?>
                        <?php if($this->permission1->method('todays_sales_report','read')->access() && $this->permission1->method('todays_purchase_report','read')->access()){ ?>
                        <a href="<?php echo base_url('profit_report') ?>" class="btn btn-warning m-b-5 m-r-2"><i
                                class="ti-align-justify"> </i> <?php echo display('profit_report') ?> </a>
                        <?php }?></span>
                </div>
            </div>
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
                                    <strong><?php echo display('sales_report')?></strong>
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
                        <table class="table table-bordered" cellspacing="0" width="100%" id="reportlist">
                            <thead>
                                <tr>
                                    <th rowspan="2">S. N.</th>
                                    <th rowspan="2">IC Invoice No.</th>
                                    <th rowspan="2"><?php echo display('sales_date') ?></th>
                                    <th rowspan="2">Invoice Date</th>
                                    <th rowspan="2">Insured/Client</th>
                                    <th rowspan="2">Insurer</th>
                                    <th rowspan="2">Policy Type</th>
                                    <th rowspan="2">Product</th>
                                    <th rowspan="2">Policy No.</th>
                                    <th rowspan="2">Endorsement No.</th>
                                    <th rowspan="2">Policy Period</th>
                                    <th colspan="3">Premium</th>
                                    <th rowspan="2">Gross Brokerage %</th>
                                    <th colspan="3">Brokerage</th>
                                    <th rowspan="2">Broker Name</th>
                                    <th colspan="2">Broker Share</th>
                                    <th colspan="2">Aggregator Share</th>
                                    <th colspan="2">Aggregator Fees</th>
                                    <th rowspan="2">Referral Name</th>
                                    <th colspan="2">Referral Fees</th>
                                    <th rowspan="2">Aggregator Fees + Referral Fees</th>
                                    <th rowspan="2">Discount</th>
                                    <th rowspan="2">Debit Note No.</th>
                                    <th rowspan="2">Commission Credit Note No.</th>
                                    <th rowspan="2">Incentive Credit Note No.</th>
                                    <th rowspan="2">Premium Paid Amount</th>
                                    <th rowspan="2">Premium Outstanding  Amount</th>
                                    <th rowspan="2">Broker Paid Amount</th>
                                    <th rowspan="2">Broker Outstanding Amount</th>
                                    <th rowspan="2">Referral Paid Amount</th>
                                    <th rowspan="2">Referral Outstanding Amount</th>
                                    <!--<th rowspan="2">Ageing</th>-->
                                    <!--<th rowspan="2">Remarks</th>-->
                                    <th rowspan="2">Premium Paid Aeiging</th>
                                    <th rowspan="2">Invoice Aeiging</th>
                                    <th rowspan="2">Type</th>
                                    <th rowspan="2"><?php echo display('total_amount') ?></th>
                                </tr>
                                <tr>
                                    <th><p style="display: none;">Premium </p>Amount</th>
                                    <th><p style="display: none;">Premium </p>Vat</th>
                                    <th><p style="display: none;">Premium </p>Total</th>
                                    <th><p style="display: none;">Brokerage </p>Amount</th>
                                    <th><p style="display: none;">Brokerage </p>Vat</th>
                                    <th><p style="display: none;">Brokerage </p>Total</th>
                                    <th><p style="display: none;">Broker Share </p>%</th>
                                    <th><p style="display: none;">Broker Share </p>Amount</th>
                                    <th><p style="display: none;">Aggregator Share </p>%</th>
                                    <th><p style="display: none;">Aggregator Share </p>Amount</th>
                                    <th><p style="display: none;">Aggregator Fees </p>%</th>
                                    <th><p style="display: none;">Aggregator Fees </p>Amount</th>
                                    <th><p style="display: none;">Referral Fee </p>%</th>
                                    <th><p style="display: none;">Referral Fee </p>Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                            <tfoot>
                                <th colspan="25" class="text-right"><?php echo display('total_purchase') ?>:</th>
                                <th></th>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('my-assets/js/admin_js/sales_report.js') ?>" type="text/javascript"></script>

<style>
    .yellowRow{
        background-color: #f0e7b4;
    }
</style>