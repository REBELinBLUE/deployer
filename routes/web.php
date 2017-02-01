<?php

/** @var \Illuminate\Routing\Router $router */

// Include the API routes inside an app path and add the authentication middleware to protect them
$router->middleware(['auth', 'jwt'])->group(base_path('routes/api.php'));

// Dashboard routes
$router->get('timeline', 'DashboardController@timeline')->name('dashboard.timeline');
$router->get('/', 'DashboardController@index')->name('dashboard');

// Deployments
$router->get('webhook/{id}/refresh', 'WebhookController@refresh')->name('webhook.refresh');

$router->get('projects/{id}', 'DeploymentController@project')->name('projects');
$router->post('projects/{id}/deploy', 'DeploymentController@deploy')->name('projects.deploy');
$router->post('projects/{id}/refresh', 'DeploymentController@refresh')->name('projects.refresh');

$router->get('deployment/{id}', 'DeploymentController@show')->name('deployments');
$router->post('deployment/{id}/rollback', 'DeploymentController@rollback')->name('deployments.rollback');
$router->post('deployment/{id}/abort', 'DeploymentController@abort')->name('deployments.abort');

$router->get('log/{id}', 'DeploymentController@log')->name('deployments.log');

// User profile
$router->get('profile', 'ProfileController@index')->name('profile.index');
$router->post('profile/update', 'ProfileController@update')->name('profile.update');
$router->post('profile/settings', 'ProfileController@settings')->name('profile.settings');
$router->post('profile/email', 'ProfileController@requestEmail')->name('profile.request-change-email');
$router->post('profile/upload', 'ProfileController@upload')->name('profile.upload-avatar');
$router->post('profile/avatar', 'ProfileController@avatar')->name('profile.avatar');
$router->post('profile/gravatar', 'ProfileController@gravatar')->name('profile.gravatar');
$router->post('profile/twofactor', 'ProfileController@twoFactor')->name('profile.twofactor');
$router->get('profile/email/{token}', 'ProfileController@email')->name('profile.confirm-change-email');
$router->post('profile/update-email', 'ProfileController@changeEmail')->name('profile.change-email');
