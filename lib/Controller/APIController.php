<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Controller;

use OCA\External\Exceptions\GroupNotFoundException;
use OCA\External\Exceptions\IconNotFoundException;
use OCA\External\Exceptions\InvalidDeviceException;
use OCA\External\Exceptions\InvalidNameException;
use OCA\External\Exceptions\InvalidTypeException;
use OCA\External\Exceptions\InvalidURLException;
use OCA\External\Exceptions\LanguageNotFoundException;
use OCA\External\Exceptions\SiteNotFoundException;
use OCA\External\SitesManager;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;

class APIController extends OCSController {
	/** @var SitesManager */
	private $sitesManager;

	/** @var IURLGenerator */
	private $url;

	/** @var IL10N */
	private $l;

	/** @var IUserSession */
	protected $userSession;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param SitesManager $sitesManager
	 * @param IURLGenerator $url
	 * @param IL10N $l
	 * @param IUserSession $userSession
	 */
	public function __construct($appName, IRequest $request, SitesManager $sitesManager, IURLGenerator $url, IL10N $l, IUserSession $userSession) {
		parent::__construct($appName, $request);

		$this->sitesManager = $sitesManager;
		$this->url = $url;
		$this->l = $l;
		$this->userSession = $userSession;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function get(): DataResponse {
		$data = $this->sitesManager->getSitesToDisplay();

		$sites = [];
		foreach ($data as $site) {
			if ($site['icon'] !== '') {
				$site['icon'] = $this->url->linkToRouteAbsolute('external.icon.showIcon', ['icon' => $site['icon']]);
			} else {
				$site['icon'] = $this->url->linkToRouteAbsolute('external.icon.showIcon', ['icon' => 'external.svg']);
			}

			$site['redirect'] = (int)$site['redirect'];

			unset($site['lang'], $site['device'], $site['groups']);
			$sites[] = $site;
		}


		$etag = md5(json_encode($sites));
		if ($this->request->getHeader('If-None-Match') === $etag) {
			return new DataResponse([], Http::STATUS_NOT_MODIFIED);
		}

		return new DataResponse($sites, Http::STATUS_OK, ['ETag' => $etag]);
	}

	/**
	 * @NoCSRFRequired
	 */
	public function getAdmin(): DataResponse {
		$icons = array_map(function ($icon) {
			return [
				'icon' => $icon,
				'name' => $icon,
				'url' => $this->url->linkToRoute('external.icon.showIcon', ['icon' => $icon]),
			];
		}, $this->sitesManager->getAvailableIcons());
		array_unshift($icons, ['icon' => '', 'name' => $this->l->t('Select an icon')]);

		$languages = $this->sitesManager->getAvailableLanguages();
		array_unshift($languages, ['code' => '', 'name' => $this->l->t('All languages')]);

		$types = [
			['type' => SitesManager::TYPE_LINK, 'name' => $this->l->t('Header')],
			['type' => SitesManager::TYPE_SETTING, 'name' => $this->l->t('Setting menu')],
			['type' => SitesManager::TYPE_QUOTA, 'name' => $this->l->t('User quota')],
			['type' => SitesManager::TYPE_LOGIN, 'name' => $this->l->t('Public footer')],
		];

		$devices = [
			['device' => SitesManager::DEVICE_ALL, 'name' => $this->l->t('All devices')],
			['device' => SitesManager::DEVICE_ANDROID, 'name' => $this->l->t('Only in the Android app')],
			['device' => SitesManager::DEVICE_IOS, 'name' => $this->l->t('Only in the iOS app')],
			['device' => SitesManager::DEVICE_DESKTOP, 'name' => $this->l->t('Only in the desktop client')],
			['device' => SitesManager::DEVICE_BROWSER, 'name' => $this->l->t('Only in the browser')],
		];

		return new DataResponse([
			'sites' => array_values($this->sitesManager->getSites()),
			'icons' => $icons,
			'languages' => $languages,
			'types' => $types,
			'devices' => $devices,
		]);
	}

	/**
	 * @param string[] $groups
	 */
	public function add(string $name, string $url, string $lang, string $type, string $device, string $icon, array $groups, int $redirect): DataResponse {
		try {
			return new DataResponse($this->sitesManager->addSite($name, $url, $lang, $type, $device, $icon, $groups, (bool)$redirect));
		} catch (InvalidNameException $e) {
			return new DataResponse(['error' => $this->l->t('The given label is invalid'), 'field' => 'name'], Http::STATUS_BAD_REQUEST);
		} catch (InvalidURLException $e) {
			return new DataResponse(['error' => $this->l->t('The given URL is invalid'), 'field' => 'url'], Http::STATUS_BAD_REQUEST);
		} catch (LanguageNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('The given language does not exist'), 'field' => 'lang'], Http::STATUS_BAD_REQUEST);
		} catch (InvalidTypeException $e) {
			return new DataResponse(['error' => $this->l->t('The given type is invalid'), 'field' => 'type'], Http::STATUS_BAD_REQUEST);
		} catch (InvalidDeviceException $e) {
			return new DataResponse(['error' => $this->l->t('The given device is invalid'), 'field' => 'device'], Http::STATUS_BAD_REQUEST);
		} catch (GroupNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('At least one of the given groups does not exist'), 'field' => 'groups'], Http::STATUS_BAD_REQUEST);
		} catch (IconNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('The given icon does not exist'), 'field' => 'icon'], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param string[] $groups
	 */
	public function update(int $id, string $name, string $url, string $lang, string $type, string $device, string $icon, array $groups, int $redirect): DataResponse {
		try {
			return new DataResponse($this->sitesManager->updateSite($id, $name, $url, $lang, $type, $device, $icon, $groups, (bool)$redirect));
		} catch (SiteNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('The site does not exist'), 'field' => 'site'], Http::STATUS_NOT_FOUND);
		} catch (InvalidNameException $e) {
			return new DataResponse(['error' => $this->l->t('The given label is invalid'), 'field' => 'name'], Http::STATUS_BAD_REQUEST);
		} catch (InvalidURLException $e) {
			return new DataResponse(['error' => $this->l->t('The given URL is invalid'), 'field' => 'url'], Http::STATUS_BAD_REQUEST);
		} catch (LanguageNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('The given language does not exist'), 'field' => 'lang'], Http::STATUS_BAD_REQUEST);
		} catch (InvalidTypeException $e) {
			return new DataResponse(['error' => $this->l->t('The given type is invalid'), 'field' => 'type'], Http::STATUS_BAD_REQUEST);
		} catch (InvalidDeviceException $e) {
			return new DataResponse(['error' => $this->l->t('The given device is invalid'), 'field' => 'device'], Http::STATUS_BAD_REQUEST);
		} catch (GroupNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('At least one of the given groups does not exist'), 'field' => 'groups'], Http::STATUS_BAD_REQUEST);
		} catch (IconNotFoundException $e) {
			return new DataResponse(['error' => $this->l->t('The given icon does not exist'), 'field' => 'icon'], Http::STATUS_BAD_REQUEST);
		}
	}

	public function delete(int $id): DataResponse {
		$this->sitesManager->deleteSite($id);
		return new DataResponse();
	}
}
