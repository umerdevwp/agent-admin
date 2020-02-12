<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// auth
$route['default_controller'] = 'login';
$route['callback'] = 'login/callback';
$route['logout'] = 'login/logout';
$route['confirm'] = 'tasks/getAccessToken';

// views
$route['portal/attachments/(:num)/(:num)'] = 'portal/attachments/$1/$2';
$route['entity/form/(:num)'] = 'entity/form/$1';
$route['entity/add']['post'] = 'entity/add';
$route['entity/(:num)'] = 'entity/index/$1';
$route['contacts'] = 'contacts/index';
$route['attachments'] = 'attachments/index';
$route['agents'] = 'RegisterAgents/index';
$route['notification/form'] = 'notifications/form';
$route['notification/cron'] = 'notifications/setRules';

// actions
$route['zoho'] = 'tasks/getZohoCode';
$route['task/update/(:num)'] = 'tasks/completeTaskInZoho/$1';
$route['task/add']['post'] = 'tasks/add';
$route['ajax/theme/save']['post'] = 'user/saveThemeAjax/$1';
$route['ajax/theme/(:any)']['get'] = 'user/getThemeAjax/$1';
$route['ajax/contact/save']['post'] = 'contacts/addAjax';
$route['synch'] = 'synch';

// automate
$route['notify'] = 'notifications/notify';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;