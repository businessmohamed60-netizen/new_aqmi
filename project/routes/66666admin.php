<?php
use App\Helpers\Router;

/** @var Router $router */

// Admin routes (require an authenticated user with an admin role)
$adminMiddleware = [\App\Middleware\AdminMiddleware::class];

// Dashboard
$router->get('/admin', 'AdminController@dashboard', $adminMiddleware);
$router->get('/admin/dashboard', 'AdminController@dashboard', $adminMiddleware);

// Questions
$router->get('/admin/questions', 'AdminController@questions', $adminMiddleware);
$router->get('/admin/questions/create', 'AdminController@questionForm', $adminMiddleware);
$router->get('/admin/questions/edit/{id}', 'AdminController@questionForm', $adminMiddleware);
$router->post('/admin/questions/save', 'AdminController@questionSave', $adminMiddleware);
$router->get('/admin/questions/delete/{id}', 'AdminController@questionDelete', $adminMiddleware);
$router->get('/admin/questions/duplicate/{id}', 'AdminController@questionDuplicate', $adminMiddleware);
$router->post('/admin/questions/toggle/{id}', 'AdminController@questionToggle', $adminMiddleware);
$router->post('/admin/questions/reorder', 'AdminController@questionReorder', $adminMiddleware);
$router->post('/admin/questions/import', 'AdminController@questionImport', $adminMiddleware);
$router->get('/admin/questions/export', 'AdminController@questionExport', $adminMiddleware);

// Domains
$router->get('/admin/domains', 'AdminController@domains', $adminMiddleware);
$router->get('/admin/domains/create', 'AdminController@domainForm', $adminMiddleware);
$router->get('/admin/domains/edit/{id}', 'AdminController@domainForm', $adminMiddleware);
$router->post('/admin/domains/save', 'AdminController@domainSave', $adminMiddleware);
$router->get('/admin/domains/delete/{id}', 'AdminController@domainDelete', $adminMiddleware);

// Score Levels
$router->get('/admin/score-levels', 'AdminController@scoreLevels', $adminMiddleware);
$router->get('/admin/score-levels/create', 'AdminController@scoreLevelForm', $adminMiddleware);
$router->get('/admin/score-levels/edit/{id}', 'AdminController@scoreLevelForm', $adminMiddleware);
$router->post('/admin/score-levels/save', 'AdminController@scoreLevelSave', $adminMiddleware);
$router->get('/admin/score-levels/delete/{id}', 'AdminController@scoreLevelDelete', $adminMiddleware);

// Recommendations
$router->get('/admin/recommendations', 'AdminController@recommendations', $adminMiddleware);
$router->get('/admin/recommendations/create', 'AdminController@recommendationForm', $adminMiddleware);
$router->get('/admin/recommendations/edit/{id}', 'AdminController@recommendationForm', $adminMiddleware);
$router->post('/admin/recommendations/save', 'AdminController@recommendationSave', $adminMiddleware);
$router->get('/admin/recommendations/delete/{id}', 'AdminController@recommendationDelete', $adminMiddleware);

// Leads
$router->get('/admin/leads', 'AdminController@leads', $adminMiddleware);
$router->get('/admin/leads/detail/{id}', 'AdminController@leadDetail', $adminMiddleware);
$router->post('/admin/leads/save-fields/{id}', 'AdminController@leadSaveFields', $adminMiddleware);
$router->get('/admin/leads/export', 'AdminController@leadExport', $adminMiddleware);
$router->post('/admin/leads/delete/{id}', 'AdminController@leadDelete', $adminMiddleware);

// Evaluation Models
$router->get('/admin/evaluation-models', 'AdminController@evaluationModels', $adminMiddleware);
$router->get('/admin/evaluation-models/create', 'AdminController@evaluationModelForm', $adminMiddleware);
$router->get('/admin/evaluation-models/edit/{id}', 'AdminController@evaluationModelForm', $adminMiddleware);
$router->post('/admin/evaluation-models/save', 'AdminController@evaluationModelSave', $adminMiddleware);
$router->get('/admin/evaluation-models/delete/{id}', 'AdminController@evaluationModelDelete', $adminMiddleware);
$router->post('/admin/evaluation-models/domains/save', 'AdminController@evaluationModelDomainsSave', $adminMiddleware);

// Lead Custom Fields
$router->get('/admin/lead-fields', 'AdminController@leadFields', $adminMiddleware);
$router->get('/admin/lead-fields/create', 'AdminController@leadFieldForm', $adminMiddleware);
$router->get('/admin/lead-fields/edit/{id}', 'AdminController@leadFieldForm', $adminMiddleware);
$router->post('/admin/lead-fields/save', 'AdminController@leadFieldSave', $adminMiddleware);
$router->get('/admin/lead-fields/delete/{id}', 'AdminController@leadFieldDelete', $adminMiddleware);

// Reports
$router->get('/admin/reports', 'AdminController@reports', $adminMiddleware);
$router->post('/admin/reports/validate/{id}', 'AdminController@reportValidate', $adminMiddleware);
$router->post('/admin/reports/reject/{id}', 'AdminController@reportReject', $adminMiddleware);

// Users
$router->get('/admin/users', 'AdminController@users', $adminMiddleware);
$router->get('/admin/users/create', 'AdminController@userForm', $adminMiddleware);
$router->get('/admin/users/edit/{id}', 'AdminController@userForm', $adminMiddleware);
$router->post('/admin/users/save', 'AdminController@userSave', $adminMiddleware);
$router->get('/admin/users/delete/{id}', 'AdminController@userDelete', $adminMiddleware);

// Settings
$router->get('/admin/settings', 'AdminController@settings', $adminMiddleware);
$router->post('/admin/settings/save', 'AdminController@settingsSave', $adminMiddleware);