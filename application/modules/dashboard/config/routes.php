<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['home']     = "dashboard/home";
$route['language'] = "dashboard/language";
$route['editPhrase/(:any)'] = "dashboard/language/editPhrase/$1";
$route['editPhrase/(:any)/(:any)'] = "dashboard/language/editPhrase/$1/$1";
$route['phrases']  = "dashboard/language/phrase";
$route['phrases/(:num)']  = "dashboard/language/phrase/$1";
$route['settings'] = "dashboard/setting";
$route['company_list'] = "dashboard/setting/cloudsubset_company_info";
$route['edit_company/(:any)'] = "dashboard/setting/cloudsubset_edit_company/$1";
$route['user_list'] = "dashboard/user/cloudsubset_userlist";
$route['add_user'] = "dashboard/user/cloudsubset_userform";
$route['add_user/(:num)'] = "dashboard/user/cloudsubset_userform/$1";
$route['currency_form'] = "dashboard/setting/cloudsubset_currencyform";
$route['currency_form/(:num)'] = "dashboard/setting/cloudsubset_currencyform/$1";
$route['currency_list'] = "dashboard/setting/cloudsubset_currency_list";
$route['mail_setting']  = "dashboard/setting/cloudsubset_mail_config";
$route['sms_setting']  = "dashboard/setting/cloudsubset_sms_config";
$route['app_setting']  = "dashboard/setting/cloudsubset_app_setting";
$route['add_role']  = "dashboard/permission/cloudsubset_add_role";
$route['role_list']  = "dashboard/permission/cloudsubset_role_list";
$route['edit_role/(:num)']  = "dashboard/permission/cloudsubset_edit_role/$1";
$route['assign_role']  = "dashboard/permission/cloudsubset_user_roleassign";
$route['restore']      = "dashboard/backup_restore/restore_form";
$route['db_import']    = "dashboard/backup_restore/import_form";
$route['commission']   = "dashboard/setting/commission";
$route['commission_generate']   = "dashboard/setting/commission_generate";
$route['out_of_stock']   = "dashboard/home/out_of_stock";
$route['edit_profile']   = "dashboard/home/profile";
$route['change_password']= "dashboard/home/change_password_form";
$route['print_setting']  = "dashboard/padprint/index";

