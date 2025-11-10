<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['add_salesman']         = "salesman/salesman/cloudsubset_form";
$route['salesman_list']        = "salesman/salesman/index";
$route['edit_salesman/(:num)'] = 'salesman/salesman/cloudsubset_form/$1';
$route['salesman_ledger']      = "salesman/salesman/cloudsubset_salesman_ledger";
$route['salesman_ledger/(:num)']= "salesman/salesman/cloudsubset_salesman_ledger/$1";
$route['salesman_ledgerdata']  = "salesman/salesman/cloudsubset_salesman_ledgerData";
$route['salesman_ledgerinfo/(:any)']= "salesman/salesman/cloudsubset_salesman_ledgerinfo/$1";
$route['salesman_advance']     = "salesman/salesman/cloudsubset_salesman_advance";
$route['salesman_advance_receipt/(:any)/(:num)']= "salesman/salesman/salesman_advancercpt/$1/$1";