<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['broker_receipt']           = "receipt/receipt/cloudsubset_broker_receipt";
$route['broker_receipt_search']        = "receipt/receipt/cloudsubset_broker_receipt_search";
$route['broker_receipt_search/(:num)']= "receipt/receipt/cloudsubset_broker_receipt_search/$1";
$route['broker_receipt_edit/(:any)']= "receipt/receipt/cloudsubset_broker_receipt_edit/$1";
$route['broker_receipt_clear']        = "receipt/receipt/cloudsubset_broker_receipt_clear";
$route['broker_receipt_payment']        = "receipt/receipt/cloudsubset_broker_receipt_payment";
$route['broker_receipt_payment_clear']        = "receipt/receipt/cloudsubset_broker_receipt_payment_clear";
$route['broker_receipt_detail']        = "receipt/receipt/cloudsubset_broker_receipt_detail";
$route['broker_receipt_payments_update']        = "receipt/receipt/cloudsubset_broker_receipt_payments_update";

// $route['customer_receipt']           = "receipt/receipt/cloudsubset_customer_receipt";
// $route['customer_receipt_search']        = "receipt/receipt/cloudsubset_customer_receipt_search";
// $route['customer_receipt_search/(:num)']= "receipt/receipt/cloudsubset_customer_receipt_search/$1";
// $route['customer_receipt_edit/(:any)']= "receipt/receipt/cloudsubset_customer_receipt_edit/$1";