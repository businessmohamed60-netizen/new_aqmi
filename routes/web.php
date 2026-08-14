<?php
use App\Helpers\Router;

/** @var Router $router */

// Home
$router->get('/', 'HomeController@index');
$router->post('/contact', 'HomeController@contact');
$router->post('/account-request', 'HomeController@accountRequest');
$router->get('/lang/{lang}', 'HomeController@switchLang');

// Assessment (auth required)
$auth = [\App\Middleware\AuthMiddleware::class];
$router->get('/assessment/start', 'AssessmentController@start', $auth);
$router->post('/assessment/select-model', 'AssessmentController@selectModel', $auth);
$router->get('/assessment/save-answer', 'AssessmentController@saveAnswer', $auth);
$router->get('/assessment/{id}', 'AssessmentController@show', $auth);
$router->get('/assessment/{id}/complete', 'AssessmentController@complete', $auth);
$router->get('/assessment/{id}/lead', 'AssessmentController@showLeadForm', $auth);
$router->post('/assessment/save-lead', 'AssessmentController@saveLead', $auth);
$router->get('/assessment/{id}/results', 'AssessmentController@results', $auth);
$router->get('/assessment/{id}/download-summary', 'ReportController@downloadSummary', $auth);
$router->get('/assessment/{id}/request-report', 'AssessmentController@requestReport', $auth);

// Consolidated reports (auth required)
$router->get('/user/consolidated', 'UserController@consolidatedView', $auth);
$router->post('/user/consolidated/create', 'UserController@consolidatedCreate', $auth);
$router->get('/user/consolidated/{id}', 'UserController@consolidatedDetail', $auth);
$router->post('/user/consolidated/{id}/request', 'UserController@consolidatedRequest', $auth);
$router->post('/user/consolidated/{id}/delete', 'UserController@consolidatedDelete', $auth);

// Report
$router->get('/report/{id}/download', 'ReportController@download');
$router->get('/verify/{report_number}', 'ReportController@verify');

// Admin Auth
$router->get('/admin/login', 'AdminController@login');
$router->get('/admin/logout', 'AdminController@logout');

// Client Auth — single login at /login, dashboard at /user/dashboard
$router->get('/user/dashboard', 'UserController@dashboard', $auth);
$router->get('/user/logout', 'UserController@logout');

// Legacy redirects (keep old links working)
$router->get('/user/login', 'AuthController@login');
$router->get('/dashboard', 'AuthController@dashboardRedirect');

// AQMI Auth System (Secure with OTP) — single login page
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@doLogin');
$router->get('/otp', 'AuthController@otp');
$router->post('/otp', 'AuthController@doOtp');
$router->get('/otp/resend', 'AuthController@resendOtp');
$router->get('/forgot', 'AuthController@forgot');
$router->post('/forgot', 'AuthController@doForgot');
$router->get('/reset', 'AuthController@reset');
$router->post('/reset', 'AuthController@doReset');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@doRegister');
$router->get('/history', 'AuthController@history');
$router->get('/logout', 'AuthController@logout');