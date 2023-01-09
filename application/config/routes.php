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
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//site routes
$route['privacy-policy'] = 'home/privacy_policy';
$route['terms-and-conditions'] = 'home/terms_and_condition';


$route['agent'] = 'agent/auth/login';
$route['agent/login'] = 'agent/auth/login';
$route['agent/forgot-password'] = 'agent/auth/forget_password';
$route['agent/edit-profile'] = 'agent/user/edit_profile';
$route['agent/change-password'] = 'agent/user/change_password';
$route['agent/dashboard'] = 'agent/user/dashboard';
$route['agent/edit-profile'] = 'agent/user/edit_profile';

$route['agent/hostess-list'] = 'agent/user/hostess_ist';
$route['agent/sub-agent'] = 'agent/user/sub_agent';
$route['agent/hostess-settlement'] = 'agent/user/HostessSettlement';
$route['agent/subagent-settlement'] = 'agent/user/SubagentSettlement';

$route['agent/daily-report'] = 'agent/DailyReport';
$route['agent/daily-report-sub'] = 'agent/DailyReport/DailyReportSub';
//$route['public-profile/(:any)'] = 'user/public_profile/$1';

//admin routes
$route['Admin'] = 'Admin/Login';
$route['admin'] = 'Admin/Login';
//$route['admin/login'] = 'Admin/Login';
//$route['admin/Do_login'] = 'Admin/Login/do_login';
$route['admin/dashboard'] = 'Admin/Dashboard';
$route['admin/profile'] = 'Admin/Profile';
$route['admin/users'] = 'Admin/Users/user_list';
$route['admin/manage-coin'] = 'Admin/Users/manage_coin';
$route['admin/transaction-history'] = 'Admin/Users/transaction_history';
$route['admin/withdrawal-request'] = 'Admin/FormBuilder/withdrawal';
$route['admin/withdrawal-approved'] = 'Admin/FormBuilder/withdrawalApproved';
$route['admin/withdrawal-rejected'] = 'Admin/FormBuilder/withdrawalRejected';



$route['admin/verified_users'] = 'Admin/Users/verified_users';
$route['admin/unverified_users'] = 'Admin/Users/unverified_users';

$route['admin/boys_list'] = 'Admin/Users/boys_list';
$route['admin/girls_list'] = 'Admin/Users/girls_list';

$route['admin/top_earners'] = 'Admin/Users/top_earners';
$route['admin/top_rich'] = 'Admin/Users/top_rich';
$route['admin/guardian-list'] = 'Admin/Trade/guardian_list';
$route['admin/smtp-details'] = 'Admin/Trade/smtp_details';
$route['admin/email'] = 'Admin/Email';
$route['admin/update_user_balance/(:any)'] = 'Admin/Users/update_user_balance';
$route['admin/email-builder-forget'] = 'Admin/Email/email_builder_forget';
$route['admin/email-builder-otp'] = 'Admin/Email/email_builder_otp';
$route['admin/email-builder-block-by'] = 'Admin/Email/email_builder_block_by';
$route['admin/email-builder-report-other'] = 'Admin/Email/email_builder_report_other';

$route['admin/email-builder-signup'] = 'Admin/Email/email_builder_signup';

$route['admin/email-block-agent'] = 'Admin/Email/email_block_agent';
$route['admin/email-edit-agent'] = 'Admin/Email/email_edit_agent';



$route['admin/email-builder-agent'] = 'Admin/Email/email_builder_agent';

$route['admin/email-builder-hostess'] = 'Admin/Email/email_builder_hostess';


$route['admin/batches-management'] = 'Admin/Batch';
$route['admin/merchant-list'] = 'Admin/Merchant';


$route['sub-agent/sign-up/(:any)'] = 'Home/sign_up';
$route['hostess/sign-up/(:any)'] = 'Home/sign_up_hostess';
$route['hostess/success-signup'] = 'Home/hostess_success_signup';


$route['sub-agent/sign-up-form'] = 'Home/sign_up_form';


$route['sub-agent/verify-otp/(:any)'] = 'Home/verify_otp';
$route['admin/total_country'] = 'Admin/Users/total_country';


$route['admin/language'] = 'Admin/Users/language_list/';

$route['admin/level-boys'] = 'Admin/Level/Level_boys';
$route['admin/level-girls'] = 'Admin/Level/Level_girls';
$route['admin/category'] = 'Admin/Level/category';
$route['admin/ring'] = 'Admin/Level/ring';

$route['admin/agent'] = 'Admin/Agent';

$route['admin/sub-agent'] = 'Admin/Agent/SubAgent';
$route['admin/agent-comission'] = 'Admin/Agent/agent_comission';
$route['admin/diamond'] = 'Admin/Users/diamond';

$route['admin/gift_listing'] = 'Admin/Users/gift_listing/';

$route['admin/report-category'] = 'Admin/Report/category';
$route['admin/report-post'] = 'Admin/Report/post';
$route['admin/post-management'] = 'Admin/Report/post_management';
$route['admin/report-list'] = 'Admin/Report';


$route['admin/logout'] = 'Admin/Profile/logout';
$route['admin/trade'] = 'Admin/Trade';


$route['admin/privacy-policy'] = 'Admin/ContentManagment/privacy_policy';
$route['admin/terms-condition'] = 'Admin/ContentManagment/terms_condition';
$route['admin/about-us'] = 'Admin/ContentManagment/about_us';
$route['admin/rate-us'] = 'Admin/ContentManagment/rate_us';
$route['admin/spin-wheel'] = 'Admin/SpinWheel/index';

$route['admin/form-builder'] = 'Admin/FormBuilder';

$route['admin/album-list'] = 'Admin/Album';
$route['admin/album-list-approved'] = 'Admin/Album/album_approved';
$route['admin/album-list-rejected'] = 'Admin/Album/album_rejected';
$route['admin/boys-rating-category'] = 'Admin/RatingCategory';
$route['admin/girls-rating-category'] = 'Admin/RatingCategory/RatingGirl';
$route['admin/rating-list'] = 'Admin/RatingCategory/Rating_list';

$route['admin/verification-limit'] = 'Admin/RatingCategory/verification_limit';
$route['admin/verification-request'] = 'Admin/RatingCategory/verification_request';

$route['admin/white-list'] = 'Admin/IpManagement';
$route['admin/black-list'] = 'Admin/IpManagement/black_list';
$route['admin/official-badges'] = 'Admin/RatingCategory/official_badges';

$route['admin/form-edit/(:any)'] = 'Admin/FormBuilder/Form_edit';


/**********************************/


