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

// Report
$router->get('/report/{id}/download', 'ReportController@download');
$router->get('/verify/{report_number}', 'ReportController@verify');

// Admin Auth
$router->get('/admin/login', 'AdminController@login');
$router->get('/admin/logout', 'AdminController@logout');

// Client Auth
$router->get('/user/login', 'UserController@login');
$router->get('/user/dashboard', 'UserController@dashboard');
$router->get('/user/logout', 'UserController@logout');

// AQMI Auth System (Secure with OTP)
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
$router->get('/dashboard', 'AuthController@dashboard');
$router->get('/history', 'AuthController@history');
$router->get('/logout', 'AuthController@logout');