<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
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
	protected IConfig $config;
	protected SitesManager $sitesManager;
	protected INavigationManager $navigationManager;
	protected IURLGenerator $url;
	protected IL10N $l10n;

	public function __construct(string $appName,
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
	 * @return TemplateResponse|RedirectResponse
	 */
	public function showPage(int $id, string $path) {
		try {
			$site = $this->sitesManager->getSiteById($id);
			return $this->createResponse($id, $site, $path);
		} catch (SiteNotFoundException $e) {
			return new RedirectResponse($this->url->linkToDefaultPageUrl());
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
			return new RedirectResponse($this->url->linkToDefaultPageUrl());
		}

		// Fall back to the files app
		if ($this->config->getSystemValue('htaccess.IgnoreFrontController', false) === true ||
			getenv('front_controller_active') === 'true') {
			return new RedirectResponse($this->url->getAbsoluteURL('/apps/files/'));
		}
		return new RedirectResponse($this->url->getAbsoluteURL('/index.php/apps/files/'));
	}

	protected function createResponse(int $id, array $site, string $path = ''): TemplateResponse {
		$this->navigationManager->setActiveEntry('external_index' . $id);

		if ($path !== '') {
			// Check whether we need to suffix the site URL with a slash, or not.
			$path = $site['url'][-1] == '/' ? $path : '/' . $path;
		}

		$response = new TemplateResponse('external', 'frame', [
			'url' => $site['url'] . $path,
			'name' => $site['name'],
		], 'user');

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedWorkerSrcDomain('*');
		$policy->addAllowedFrameDomain('*');
		$response->setContentSecurityPolicy($policy);

		return $response;
	}
}
