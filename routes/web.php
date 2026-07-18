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
$router->get('/assessment/{id}/request-report', 'AssessmentController@requestReport');

// Report
$router->get('/report/{id}/download', 'ReportController@download');

// Admin Auth
$router->get('/login', 'AdminController@login');
$router->get('/logout', 'AdminController@logout');

// Client Auth
$router->get('/user/login', 'UserController@login');
$router->get('/user/dashboard', 'UserController@dashboard');
$router->get('/user/logout', 'UserController@logout');

// AQMI Auth System (Secure with OTP)
$router->get('/aqmi/login', 'AuthController@login');
$router->post('/aqmi/login', 'AuthController@doLogin');
$router->get('/aqmi/otp', 'AuthController@otp');
$router->post('/aqmi/otp', 'AuthController@doOtp');
$router->get('/aqmi/otp/resend', 'AuthController@resendOtp');
$router->get('/aqmi/forgot', 'AuthController@forgot');
$router->post('/aqmi/forgot', 'AuthController@doForgot');
$router->get('/aqmi/reset', 'AuthController@reset');
$router->post('/aqmi/reset', 'AuthController@doReset');
$router->get('/aqmi/register', 'AuthController@register');
$router->post('/aqmi/register', 'AuthController@doRegister');
$router->get('/aqmi/dashboard', 'AuthController@dashboard');
$router->get('/aqmi/history', 'AuthController@history');
$router->get('/aqmi/logout', 'AuthController@logout');