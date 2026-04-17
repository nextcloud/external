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
	 */
	public function showPage(int $id, string $path): TemplateResponse|RedirectResponse {
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
	 */
	public function showDefaultPage(): TemplateResponse|RedirectResponse {
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
		if ($this->config->getSystemValue('htaccess.IgnoreFrontController', false) === true
			|| getenv('front_controller_active') === 'true') {
			return new RedirectResponse($this->url->getAbsoluteURL('/apps/files/'));
		}
		return new RedirectResponse($this->url->getAbsoluteURL('/index.php/apps/files/'));
	}

	protected function createResponse(int $id, array $site, string $path = ''): TemplateResponse {
		$this->navigationManager->setActiveEntry('external_index' . $id);

		if ($path !== '') {
			// Parse the URL to properly insert the path before any query parameters
			$parts = parse_url($site['url']);

			if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
				throw new \RuntimeException('Invalid site URL: ' . $site['url']);
			}

			$url = $parts['scheme'] . '://';
			if (isset($parts['user'])) {
				$url .= rawurlencode($parts['user']);
				if (isset($parts['pass'])) {
					$url .= ':' . rawurlencode($parts['pass']);
				}
				$url .= '@';
			}
			$url .= $parts['host'];
			if (isset($parts['port'])) {
				$url .= ':' . $parts['port'];
			}
			$url .= rtrim($parts['path'] ?? '', '/') . '/' . $path;
			if (isset($parts['query'])) {
				$url .= '?' . $parts['query'];
			}
			if (isset($parts['fragment'])) {
				$url .= '#' . $parts['fragment'];
			}
		} else {
			$url = $site['url'];
		}

		$response = new TemplateResponse('external', 'frame', [
			'url' => $url,
			'name' => $site['name'],
		], 'user');

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedWorkerSrcDomain('*');
		$policy->addAllowedFrameDomain('*');
		$policy->addAllowedFrameDomain('blob:');
		$response->setContentSecurityPolicy($policy);

		return $response;
	}
}
