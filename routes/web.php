<?php

use App\Helpers\Router;

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

$router->get('/', 'HomeController@index');
$router->post('/contact', 'HomeController@contact');
$router->get('/lang/{lang}', 'HomeController@switchLang');


/*
|--------------------------------------------------------------------------
| AUTH (OTP)
|--------------------------------------------------------------------------
*/

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

$router->get('/logout', 'AuthController@logout');


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

$router->get('/dashboard', 'UserController@dashboard');
$router->get('/history', 'UserController@history');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

$router->get('/admin', 'AdminController@dashboard');

$router->get('/admin/users', 'AdminController@users');

$router->get('/admin/leads', 'AdminController@leads');

$router->get('/admin/reports', 'AdminController@reports');

$router->get('/admin/assessments', 'AdminController@assessments');

$router->get('/admin/settings', 'AdminController@settings');


/*
|--------------------------------------------------------------------------
| ASSESSMENT
|--------------------------------------------------------------------------
*/

$router->get('/assessment/start', 'AssessmentController@start');

$router->get('/assessment/save-answer', 'AssessmentController@saveAnswer');

$router->post('/assessment/save-lead', 'AssessmentController@saveLead');

$router->get('/assessment/{id}', 'AssessmentController@show');

$router->get('/assessment/{id}/complete', 'AssessmentController@complete');

$router->get('/assessment/{id}/results', 'AssessmentController@results');

$router->get('/assessment/{id}/lead-form', 'AssessmentController@showLeadForm');

$router->get('/assessment/{id}/request-report', 'AssessmentController@requestReport');


/*
|--------------------------------------------------------------------------
| REPORTS
|--------------------------------------------------------------------------
*/

$router->get('/report/{id}/download', 'ReportController@download');