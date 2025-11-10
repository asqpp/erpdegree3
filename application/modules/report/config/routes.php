<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['stock']          = "report/report/cloudsubset_stock_report";
$route['reports/(:num)'] = 'report/report/cloudsubset_purchase_edit_form/$1';
$route['closing_form']   = "report/report/cloudsubset_cash_closing";
$route['closing_report'] = "report/report/cloudsubset_closing_report";
$route['closing_report_search'] = "report/report/cloudsubset_closing_report_search";
$route['todays_report']  = "report/report/cloudsubset_todays_report";
$route['todays_customer_received']  = "report/report/cloudsubset_todays_customer_received";
$route['todays_customerwise_received']  = "report/report/cloudsubset_customerwise_received";
$route['sales_report']  = "report/report/cloudsubset_todays_sales_report";
$route['datewise_sales_report']  = "report/report/cloudsubset_datewise_sales_report";
$route['userwise_sales_report']  = "report/report/cloudsubset_userwise_sales_report";
$route['invoice_wise_due_report']= "report/report/cloudsubset_invoice_wise_due_report";
$route['shipping_cost_report']= "report/report/cloudsubset_shippingcost_report";
$route['purchase_report']     = "report/report/cloudsubset_purchase_report";
$route['purchase_report_categorywise']= "report/report/cloudsubset_purchase_report_category_wise";
$route['product_wise_sales_report']= "report/report/cloudsubset_sale_report_productwise";
$route['category_sales_report']= "report/report/cloudsubset_categorywise_sales_report";
$route['sales_return']         = "report/report/cloudsubset_sales_return";
$route['supplier_returns']      = "report/report/cloudsubset_supplier_return";
$route['tax_report']           = "report/report/cloudsubset_tax_report";
$route['profit_report']        = "report/report/cloudsubset_profit_report";
$route['commission_report']  = "report/report/cloudsubset_todays_commission_report";
$route['commission_report_search'] = "report/report/cloudsubset_commission_report_search";

$route['aggregator_report']  = "report/report/cloudsubset_todays_aggregator_report";
$route['aggregator_report_search'] = "report/report/cloudsubset_aggregator_report_search";
$route['broker_report']  = "report/report/cloudsubset_todays_broker_report";
$route['broker_report_search'] = "report/report/cloudsubset_broker_report_search";
$route['referral_report']  = "report/report/cloudsubset_todays_referral_report";
$route['referral_report_search'] = "report/report/cloudsubset_referral_report_search";