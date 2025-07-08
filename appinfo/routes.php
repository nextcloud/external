<?php

/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

return [
	'routes' => [
		['name' => 'site#showDefaultPage', 'url' => '/', 'verb' => 'GET'],
		['name' => 'icon#uploadIcon', 'url' => '/icons', 'verb' => 'POST'],
		['name' => 'icon#showIcon', 'url' => '/icons/{icon}', 'verb' => 'GET'],
		['name' => 'icon#deleteIcon', 'url' => '/icons/{icon}', 'verb' => 'DELETE'],
		['name' => 'site#showPage', 'url' => '/{id}/{path}', 'verb' => 'GET', 'requirements' => ['path' => '.*']],
	],
	'ocs' => [
		['name' => 'API#get', 'url' => '/api/{apiVersion}', 'verb' => 'GET', 'requirements' => ['apiVersion' => 'v1']],
		['name' => 'API#getAdmin', 'url' => '/api/{apiVersion}/sites', 'verb' => 'GET', 'requirements' => ['apiVersion' => 'v1']],
		['name' => 'API#add', 'url' => '/api/{apiVersion}/sites', 'verb' => 'POST', 'requirements' => ['apiVersion' => 'v1']],
		['name' => 'API#update', 'url' => '/api/{apiVersion}/sites/{id}', 'verb' => 'PUT', 'requirements' => ['apiVersion' => 'v1', 'id' => '\d+']],
		['name' => 'API#delete', 'url' => '/api/{apiVersion}/sites/{id}', 'verb' => 'DELETE', 'requirements' => ['apiVersion' => 'v1', 'id' => '\d+']],
	],
];
