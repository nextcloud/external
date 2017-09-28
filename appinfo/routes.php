<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

return [
	'routes' => [
		['name' => 'site#showDefaultPage', 'url' => '/', 'verb' => 'GET'],
		['name' => 'site#showPage', 'url' => '/{id}', 'verb' => 'GET'],
		['name' => 'icon#uploadIcon', 'url' => '/icons', 'verb' => 'POST'],
		['name' => 'icon#showIcon', 'url' => '/icons/{icon}', 'verb' => 'GET'],
		['name' => 'icon#deleteIcon', 'url' => '/icons/{icon}', 'verb' => 'DELETE'],
	],
	'ocs' => [
		['name' => 'API#get', 'url' => '/api/{apiVersion}', 'verb' => 'GET', 'requirements' => ['apiVersion' => 'v1']],
		['name' => 'API#getAdmin', 'url' => '/api/{apiVersion}/sites', 'verb' => 'GET', 'requirements' => ['apiVersion' => 'v1']],
		['name' => 'API#add', 'url' => '/api/{apiVersion}/sites', 'verb' => 'POST', 'requirements' => ['apiVersion' => 'v1']],
		['name' => 'API#update', 'url' => '/api/{apiVersion}/sites/{id}', 'verb' => 'PUT', 'requirements' => ['apiVersion' => 'v1', 'id' => '\d+']],
		['name' => 'API#delete', 'url' => '/api/{apiVersion}/sites/{id}', 'verb' => 'DELETE', 'requirements' => ['apiVersion' => 'v1', 'id' => '\d+']],
	],
];
