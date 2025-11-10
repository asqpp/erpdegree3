<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $route['salesman_commission']           = "payment/payment/cloudsubset_salesman_commission";
$route['salesman_commission_search']        = "payment/payment/cloudsubset_salesman_commission_search";
$route['salesman_commission_search/(:num)']= "payment/payment/cloudsubset_salesman_commission_search/$1";
$route['salesman_commission_search/(:num)/(:num)']= "payment/payment/cloudsubset_salesman_commission_search/$1/$2";
$route['salesman_commission_edit/(:any)']= "payment/payment/cloudsubset_salesman_commission_edit/$1";
$route['salesman_commission_clear']        = "payment/payment/cloudsubset_salesman_commission_clear";
// $route['broker_commission']           = "payment/payment/cloudsubset_broker_commission";
// $route['broker_commission_search']        = "payment/payment/cloudsubset_broker_commission_search";
// $route['broker_commission_search/(:num)']= "payment/payment/cloudsubset_broker_commission_search/$1";
// $route['broker_commission_edit/(:any)']= "payment/payment/cloudsubset_broker_commission_edit/$1";
// $route['supplier_payment']           = "payment/payment/cloudsubset_supplier_payment";
$route['supplier_payment_search']        = "payment/payment/cloudsubset_supplier_payment_search";
$route['supplier_payment_search/(:num)']= "payment/payment/cloudsubset_supplier_payment_search/$1";
$route['supplier_payment_search/(:num)/(:num)']= "payment/payment/cloudsubset_supplier_payment_search/$1/$2";
$route['supplier_payment_edit/(:any)']= "payment/payment/cloudsubset_supplier_payment_edit/$1";
$route['supplier_payment_clear']        = "payment/payment/cloudsubset_supplier_payment_clear";

// $route['customer_payment']           = "payment/payment/cloudsubset_customer_payment";
$route['customer_payment_search']        = "payment/payment/cloudsubset_customer_payment_search";
$route['customer_payment_search/(:num)']= "payment/payment/cloudsubset_customer_payment_search/$1";
$route['customer_payment_search/(:num)/(:num)']= "payment/payment/cloudsubset_customer_payment_search/$1/$2";
$route['customer_payment_edit/(:any)']= "payment/payment/cloudsubset_customer_payment_edit/$1";
$route['customer_payment_clear']        = "payment/payment/cloudsubset_customer_payment_clear";