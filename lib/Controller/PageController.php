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

use OCA\External\SiteNotFoundException;
use OCA\External\SitesManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\INavigationManager;
use OCP\IRequest;

class PageController extends Controller {

	/** @var SitesManager */
	protected $sitesManager;

	/** @var INavigationManager */
	protected $navigationManager;

	public function __construct($appName, IRequest $request, INavigationManager $navigationManager, SitesManager $sitesManager) {
		parent::__construct($appName, $request);
		$this->sitesManager = $sitesManager;
		$this->navigationManager = $navigationManager;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param int $id
	 * @return TemplateResponse|RedirectResponse
	 */
	public function showPage($id) {
		try {
			$site = $this->sitesManager->getSiteById($id);
			$this->navigationManager->setActiveEntry('external_index' . $id);

			$response = new TemplateResponse('external', 'frame', [
				'url' => $site['url'],
			], 'user');

			$policy = new ContentSecurityPolicy();
			$policy->addAllowedChildSrcDomain('*');
			$response->setContentSecurityPolicy($policy);

			return $response;
		} catch (SiteNotFoundException $e) {
			return new RedirectResponse(\OC_Util::getDefaultPageUrl());
		}
	}
}
