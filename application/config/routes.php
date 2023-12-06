<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
$route['default_controller'] = 'Home';
$route['admin'] = 'admin/login';
// $route['resendOtp'] = 'users/resendOtp';
// $route['validateOtp'] = 'users/validateOtp';
// $route['userDetails'] = 'users/userDetails';
// $route['saveUserLog'] = 'users/saveUserLog';
// $route['updateUserDetails'] = 'users/updateUserDetails';
// $route['homePage'] = 'common/homePage';
// $route['myContactedProperties'] = 'common/myContactedProperties';
// $route['viewedProperties'] = 'common/viewedProperties';
// $route['wishlist'] = 'common/wishlist';
// $route['myProperties'] = 'common/myProperties';
// $route['addToWishlist'] = 'common/addToWishlist';
// $route['savePropertyView'] = 'common/savePropertyView';
// $route['developerProfile'] = 'common/developerProfile';
// $route['savePropertyCallHistory'] = 'common/savePropertyCallHistory';
// $route['searchProperty'] = 'property/searchProperty';
// $route['saveApartment'] = 'property/saveApartment';
// $route['saveIndependentHouse'] = 'property/saveIndependentHouse';
// $route['saveVenture'] = 'property/saveVenture';
// $route['saveIndependentPlot'] = 'property/saveIndependentPlot';
// $route['saveLand'] = 'property/saveLand';
// $route['updatePropertyStatus'] = 'property/updatePropertyStatus';
// $route['propertyDetailsByCt'] = 'property/propertyDetailsByCt';
// $route['propertyDetailsById'] = 'property/propertyDetailsById';


$route['user/create'] = 'users';
$route['resend-otp'] = 'users/resendOtp';
$route['validate-otp'] = 'users/validateOtp';
$route['user/get-details'] = 'users/userDetails';
$route['save-user-log'] = 'users/saveUserLog';
$route['user/update-profile'] = 'users/updateUserDetails';
$route['home'] = 'common/homePage';
$route['get-contacted-properties'] = 'common/myContactedProperties';
$route['get-viewed-properties'] = 'common/viewedProperties';
$route['get-wishlist'] = 'common/wishlist';
$route['get-my-properties'] = 'common/myProperties';
$route['save-wishlist'] = 'common/addToWishlist';
$route['save-property-view'] = 'common/savePropertyView';
$route['developer-profile'] = 'common/developerProfile';
$route['save-call-history'] = 'common/savePropertyCallHistory';
$route['search-property'] = 'property/searchProperty';
$route['save-apartment'] = 'property/saveApartment';
$route['save-independent-house'] = 'property/saveIndependentHouse';
$route['save-venture'] = 'property/saveVenture';
$route['save-independent-plot'] = 'property/saveIndependentPlot';
$route['save-land'] = 'property/saveLand';
$route['update-property-status'] = 'property/updatePropertyStatus';
$route['get-property-details-by-ct'] = 'property/propertyDetailsByCt';
$route['get-property-details-by-id'] = 'property/propertyDetailsById';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
