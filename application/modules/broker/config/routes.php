<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['add_broker']         = "broker/broker/cloudsubset_form";
$route['broker_list']        = "broker/broker/index";
$route['edit_broker/(:num)'] = 'broker/broker/cloudsubset_form/$1';
$route['broker_ledger']      = "broker/broker/cloudsubset_broker_ledger";
$route['broker_ledger/(:num)']= "broker/broker/cloudsubset_broker_ledger/$1";
$route['broker_ledgerdata']  = "broker/broker/cloudsubset_broker_ledgerData";
$route['broker_ledgerinfo/(:any)']= "broker/broker/cloudsubset_broker_ledgerinfo/$1";
$route['broker_advance']     = "broker/broker/cloudsubset_broker_advance";
$route['broker_advance_receipt/(:any)/(:num)']= "broker/broker/broker_advancercpt/$1/$1";