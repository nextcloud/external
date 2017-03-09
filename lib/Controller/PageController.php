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

namespace OCA\External\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;

class PageController extends Controller {

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param int $id
	 * @return TemplateResponse|RedirectResponse
	 */
	public function showPage($id) {

		$sites = \OCA\External\External::getSites();
		if (isset($sites[$id - 1])) {
			$url = $sites[$id - 1][1];
			\OCP\App::setActiveNavigationEntry('external_index' . $id);

			$response = new TemplateResponse('external', 'frame', [
				'url' => $url
			], 'user');
			$policy = new ContentSecurityPolicy();
			$policy->addAllowedChildSrcDomain('*');
			$response->setContentSecurityPolicy($policy);
			return $response;
		} else {
			return new RedirectResponse(\OC_Util::getDefaultPageUrl());
		}
	}
}
