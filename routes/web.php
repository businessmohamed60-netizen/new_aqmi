<?php
use App\Helpers\Router;

/** @var Router $router */

// Home
$router->get('/', 'HomeController@index');
$router->post('/contact', 'HomeController@contact');
$router->get('/lang/{lang}', 'HomeController@switchLang');

// Assessment
$router->get('/assessment/start', 'AssessmentController@start');
$router->get('/assessment/{id}', 'AssessmentController@show');
$router->get('/assessment/save-answer', 'AssessmentController@saveAnswer');
$router->get('/assessment/{id}/complete', 'AssessmentController@complete');
$router->get('/assessment/save-lead', 'AssessmentController@saveLead');
$router->get('/assessment/{id}/results', 'AssessmentController@results');
$router->get('/assessment/{id}/download-summary', 'ReportController@downloadSummary');
$router->get('/assessment/{id}/request-report', 'AssessmentController@requestCertification');

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