<?php
defined('BASEPATH') OR exit('No direct script access allowed');



$route['add_customer']         = "customer/customer/cloudsubset_form";
$route['customer_list']        = "customer/customer/index";
$route['edit_customer/(:num)'] = 'customer/customer/cloudsubset_form/$1';
$route['credit_customer']      = "customer/customer/cloudsubset_credit_customer";
$route['paid_customer']        = "customer/customer/cloudsubset_paid_customer";
$route['customer_ledger']      = "customer/customer/cloudsubset_customer_ledger";
$route['customer_ledger/(:num)']      = "customer/customer/cloudsubset_customer_ledger/$1";
$route['customer_ledgerdata']  = "customer/customer/cloudsubset_customer_ledgerData";
$route['customer_advance']     = "customer/customer/cloudsubset_customer_advance";
$route['advance_receipt/(:any)/(:num)']= "customer/customer/customer_advancercpt/$1/$1";