<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['add_invoice']         = "invoice/invoice/cloudsubset_invoice_form";
$route['pos_invoice']         = "invoice/invoice/cloudsubset_pos_invoice";
$route['gui_pos']             = "invoice/invoice/cloudsubset_gui_pos";
$route['invoice_list']        = "invoice/invoice/cloudsubset_invoice_list";
$route['invoice_details/(:num)'] = 'invoice/invoice/cloudsubset_invoice_details/$1';
$route['delivery_invoice_details/(:num)'] = 'invoice/invoice/cloudsubset_delivery_invoice_details/$1';
$route['invoice_pad_print/(:num)'] = 'invoice/invoice/cloudsubset_invoice_pad_print/$1';
$route['pos_print/(:num)']    = 'invoice/invoice/cloudsubset_invoice_pos_print/$1';
$route['invoice_pos_print']    = 'invoice/invoice/cloudsubset_pos_print_direct';
$route['download_invoice/(:num)']  = 'invoice/invoice/cloudsubset_download_invoice/$1';
$route['invoice_edit/(:num)'] = 'invoice/invoice/cloudsubset_edit_invoice/$1';
$route['invoice_print'] = 'invoice/invoice/invoice_inserted_data_manual';

$route['terms_list'] = 'invoice/invoice/cloudsubset_terms_list';
$route['terms_add'] = 'invoice/invoice/cloudsubset_terms_form';
$route['terms_add/(:num)'] = 'invoice/invoice/cloudsubset_terms_form/$1';

$route['upload_invoice']           = "invoice/invoice/upload_invoice";