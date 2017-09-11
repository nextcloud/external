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

use OCA\External\Exceptions\SiteNotFoundException;
use OCA\External\SitesManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\INavigationManager;
use OCP\IRequest;
use OCP\IURLGenerator;

class SiteController extends Controller {

	/** @var IConfig */
	protected $config;
	/** @var SitesManager */
	protected $sitesManager;
	/** @var INavigationManager */
	protected $navigationManager;
	/** @var IURLGenerator */
	protected $url;
	/** @var IL10N */
	protected $l10n;

	/**
	 * SiteController constructor.
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param IConfig $config
	 * @param INavigationManager $navigationManager
	 * @param SitesManager $sitesManager
	 * @param IURLGenerator $url
	 * @param IL10N $l10n
	 */
	public function __construct($appName,
								IRequest $request,
								IConfig $config,
								INavigationManager $navigationManager,
								SitesManager $sitesManager,
								IURLGenerator $url,
								IL10N $l10n) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->sitesManager = $sitesManager;
		$this->navigationManager = $navigationManager;
		$this->url = $url;
		$this->l10n = $l10n;
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
			return $this->createResponse($id, $site);
		} catch (SiteNotFoundException $e) {
			return new RedirectResponse(\OC_Util::getDefaultPageUrl());
		}
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * This is used when the app is set as default app
	 *
	 * @return TemplateResponse|RedirectResponse
	 */
	public function showDefaultPage() {
		// Show first available page when there is one
		$sites = $this->sitesManager->getSitesToDisplay();
		if (!empty($sites)) {
			reset($sites);
			$id = key($sites);
			return $this->createResponse($id, $sites[$id]);
		}

		// Redirect to default page when it's not the external sites app
		if ($this->config->getSystemValue('defaultapp', 'files') !== 'external') {
			return new RedirectResponse(\OC_Util::getDefaultPageUrl());
		}

		// Fall back to the files app
		if ($this->config->getSystemValue('htaccess.IgnoreFrontController', false) === true ||
			getenv('front_controller_active') === 'true') {
			return new RedirectResponse($this->url->getAbsoluteURL('/apps/files/'));
		}
		return new RedirectResponse($this->url->getAbsoluteURL('/index.php/apps/files/'));
	}

	/**
	 * @param int $id
	 * @param array $site
	 * @return RedirectResponse|TemplateResponse
	 */
	protected function createResponse($id, array $site) {
		$this->navigationManager->setActiveEntry('external_index' . $id);

		$response = new TemplateResponse('external', 'frame', [
			'url' => $site['url'],
		], 'user');

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedChildSrcDomain('*');
		$policy->addAllowedFrameDomain('*');
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function renderQuotaLink() {
		$sites = $this->sitesManager->getSitesToDisplay();

		$quotaLink = [];
		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$quotaLink = $site;
				break;
			}
		}

		$url = $quotaLink['url'];
		if (!$quotaLink['redirect']) {
			$url = $this->url->linkToRoute('external.site.showPage', ['id'=> $quotaLink['id']]);
		}

		return new TemplateResponse('external', 'quota', [
			'quotaLink'			=> $url,
			'quotaName'			=> $quotaLink['name'],
		], '');
	}
}
