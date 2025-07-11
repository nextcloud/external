<?php

/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External;

use OCA\External\Exceptions\GroupNotFoundException;
use OCA\External\Exceptions\IconNotFoundException;
use OCA\External\Exceptions\InvalidDeviceException;
use OCA\External\Exceptions\InvalidNameException;
use OCA\External\Exceptions\InvalidTypeException;
use OCA\External\Exceptions\InvalidURLException;
use OCA\External\Exceptions\LanguageNotFoundException;
use OCA\External\Exceptions\SiteNotFoundException;
use OCP\App\IAppManager;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\SimpleFS\ISimpleFile;
use OCP\IAppConfig;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;
use OCP\L10N\IFactory;

class SitesManager {
	public const TYPE_LINK = 'link';
	public const TYPE_SETTING = 'settings';
	public const TYPE_LOGIN = 'guest';
	public const TYPE_QUOTA = 'quota';

	public const DEVICE_ALL = '';
	public const DEVICE_ANDROID = 'android';
	public const DEVICE_IOS = 'ios';
	public const DEVICE_DESKTOP = 'desktop';
	public const DEVICE_BROWSER = 'browser';

	/** @var IRequest */
	protected $request;

	/** @var IAppConfig */
	protected $config;

	/** @var IFactory */
	protected $languageFactory;

	/** @var IAppManager */
	protected $appManager;

	/** @var IGroupManager */
	protected $groupManager;

	/** @var IUserSession */
	protected $userSession;

	/** @var JWTManager */
	protected $JWTManager;

	/** @var IAppData */
	protected $appData;


	public function __construct(IRequest $request,
		IAppConfig $config,
		IAppManager $appManager,
		IGroupManager $groupManager,
		IUserSession $userSession,
		IFactory $languageFactory,
		JWTManager $JWTManager,
		IAppData $appData) {
		$this->request = $request;
		$this->config = $config;
		$this->appManager = $appManager;
		$this->groupManager = $groupManager;
		$this->userSession = $userSession;
		$this->languageFactory = $languageFactory;
		$this->JWTManager = $JWTManager;
		$this->appData = $appData;
	}

	/**
	 * @return array
	 * @throws SiteNotFoundException
	 */
	public function getSiteById(int $id) {
		$sites = $this->getSitesToDisplay();

		if (isset($sites[$id])) {
			return $sites[$id];
		}

		throw new SiteNotFoundException();
	}

	/**
	 * @return array[]
	 */
	public function getSitesToDisplay() {
		$sites = $this->getSites();
		$lang = $this->languageFactory->findLanguage();
		$locale = $this->languageFactory->findLocale();
		$device = $this->getDeviceFromUserAgent();

		$user = $this->userSession->getUser();
		if ($user instanceof IUser) {
			$groups = $this->groupManager->getUserGroupIds($user);
		} else {
			$groups = [];
		}

		$email = $user instanceof IUser ? $user->getEMailAddress() : '';
		$uid = $user instanceof IUser ? $user->getUID() : '';
		$displayName = $user instanceof IUser ? $user->getDisplayName() : '';
		$email = $email ?? '';

		$jwt = '';
		if ($user instanceof IUser) {
			$payload = ['email' => $email, 'uid' => $uid, 'displayName' => $displayName];
			$jwt = $this->JWTManager->getToken($payload);
		}

		$langSites = [];
		foreach ($sites as $id => $site) {
			if ($site['lang'] !== '' && $site['lang'] !== $lang) {
				continue;
			}

			if ($site['device'] !== self::DEVICE_ALL && $site['device'] !== $device) {
				continue;
			}

			if (!empty($site['groups']) && empty(array_intersect($site['groups'], $groups))) {
				continue;
			}

			$site['url'] = str_replace(
				['{email}', '{uid}', '{displayname}', '{jwt}', '{groups}', '{language}', '{locale}'],
				array_map('rawurlencode', [$email, $uid, $displayName, $jwt, implode(',', $groups), $lang, $locale]),
				$site['url']
			);

			$langSites[$id] = $site;
		}

		return $langSites;
	}

	/**
	 * @return array[]
	 */
	public function getSites() {
		$jsonEncodedList = $this->config->getValueString('external', 'sites', '');
		$sites = json_decode($jsonEncodedList, true);

		if (!is_array($sites) || empty($sites)) {
			return [];
		}

		if (isset($sites[0][0])) {
			return $this->getSitesFromOldConfig($sites);
		}

		$sites = array_map([$this, 'fillSiteArray'], $sites);

		return $sites;
	}

	/**
	 * Adds default values for new attributes of sites
	 */
	protected function fillSiteArray(array $site): array {
		return array_merge([
			'icon' => 'external.svg',
			'lang' => '',
			'type' => self::TYPE_LINK,
			'device' => self::DEVICE_ALL,
			'groups' => [],
			'redirect' => false,
		],
			$site
		);
	}

	/**
	 * @param string[] $groups
	 * @throws InvalidNameException
	 * @throws InvalidURLException
	 * @throws LanguageNotFoundException
	 * @throws InvalidTypeException
	 * @throws InvalidDeviceException
	 * @throws GroupNotFoundException
	 * @throws IconNotFoundException
	 */
	public function addSite(string $name, string $url, string $lang, string $type, string $device, string $icon, array $groups, bool $redirect): array {
		$id = 1 + $this->config->getValueInt('external', 'max_site');

		if ($name === '') {
			throw new InvalidNameException();
		}

		if (filter_var($url, FILTER_VALIDATE_URL) === false
			|| (strpos($url, 'http://') !== 0
				&& strpos($url, 'https://') !== 0
				&& strpos($url, 'mailto:') !== 0)) {
			throw new InvalidURLException();
		}

		if ($lang !== '') {
			$valid = false;
			foreach ($this->getAvailableLanguages() as $language) {
				if ($language['code'] === $lang) {
					$valid = true;
					break;
				}
			}

			if (!$valid) {
				throw new LanguageNotFoundException();
			}
		}

		if (!in_array($type, [self::TYPE_LINK, self::TYPE_SETTING, self::TYPE_QUOTA, self::TYPE_LOGIN], true)) {
			throw new InvalidTypeException();
		}

		if (!in_array($device, [self::DEVICE_ALL, self::DEVICE_ANDROID, self::DEVICE_IOS, self::DEVICE_DESKTOP, self::DEVICE_BROWSER], true)) {
			throw new InvalidDeviceException();
		}

		foreach ($groups as $gid) {
			if (!$this->groupManager->groupExists($gid)) {
				throw new GroupNotFoundException();
			}
		}

		$icons = $this->getAvailableIcons();
		if ($icon === '') {
			$icon = 'external.svg';
		}
		if (!in_array($icon, $icons, true)) {
			throw new IconNotFoundException();
		}

		if ($type === self::TYPE_LOGIN) {
			$redirect = true;
		}

		$sites = $this->getSites();
		$sites[$id] = [
			'id' => $id,
			'name' => $name,
			'url' => $url,
			'lang' => $lang,
			'type' => $type,
			'device' => $device,
			'icon' => $icon,
			'groups' => $groups,
			'redirect' => $redirect,
		];
		$this->config->setValueString('external', 'sites', json_encode($sites));
		$this->config->setValueInt('external', 'max_site', $id);

		return $sites[$id];
	}

	/**
	 * @param string[] $groups
	 * @throws SiteNotFoundException
	 * @throws InvalidNameException
	 * @throws InvalidURLException
	 * @throws LanguageNotFoundException
	 * @throws InvalidTypeException
	 * @throws InvalidDeviceException
	 * @throws GroupNotFoundException
	 * @throws IconNotFoundException
	 */
	public function updateSite(int $id, string $name, string $url, string $lang, string $type, string $device, string $icon, array $groups, bool $redirect): array {
		$sites = $this->getSites();
		if (!isset($sites[$id])) {
			throw new SiteNotFoundException();
		}

		if ($name === '') {
			throw new InvalidNameException();
		}

		if (filter_var($url, FILTER_VALIDATE_URL) === false
			|| (strpos($url, 'http://') !== 0
				&& strpos($url, 'https://') !== 0
				&& strpos($url, 'mailto:') !== 0)) {
			throw new InvalidURLException();
		}

		if ($lang !== '') {
			$valid = false;
			foreach ($this->getAvailableLanguages() as $language) {
				if ($language['code'] === $lang) {
					$valid = true;
					break;
				}
			}

			if (!$valid) {
				throw new LanguageNotFoundException();
			}
		}

		if (!in_array($type, [self::TYPE_LINK, self::TYPE_SETTING, self::TYPE_QUOTA, self::TYPE_LOGIN], true)) {
			throw new InvalidTypeException();
		}

		if (!in_array($device, [self::DEVICE_ALL, self::DEVICE_ANDROID, self::DEVICE_IOS, self::DEVICE_DESKTOP, self::DEVICE_BROWSER], true)) {
			throw new InvalidDeviceException();
		}

		foreach ($groups as $gid) {
			if (!$this->groupManager->groupExists($gid)) {
				throw new GroupNotFoundException();
			}
		}

		$icons = $this->getAvailableIcons();
		if ($icon === '') {
			$icon = 'external.svg';
		}
		if (!in_array($icon, $icons, true)) {
			throw new IconNotFoundException();
		}

		if ($type === self::TYPE_LOGIN) {
			$redirect = true;
		}

		$sites[$id] = [
			'id' => $id,
			'name' => $name,
			'url' => $url,
			'lang' => $lang,
			'type' => $type,
			'device' => $device,
			'icon' => $icon,
			'groups' => $groups,
			'redirect' => $redirect,
		];
		$this->config->setValueString('external', 'sites', json_encode($sites));

		return $sites[$id];
	}

	public function deleteSite(int $id): void {
		$sites = $this->getSites();
		if (!isset($sites[$id])) {
			return;
		}

		unset($sites[$id]);
		$this->config->setValueString('external', 'sites', json_encode($sites));
	}

	/**
	 * @param array<int, array> $sites
	 * @return array<int, array>
	 */
	protected function getSitesFromOldConfig(array $sites): array {
		$fixedSites = [];

		foreach ($sites as $id => $site) {
			$fixedSites[$id + 1] = $this->fillSiteArray([
				'id' => $id + 1,
				'name' => $site[0],
				'url' => $site[1],
				'icon' => isset($site[2]) ? $site[2] : 'external.svg',
			]);
		}

		$this->config->setValueString('external', 'sites', json_encode($fixedSites));
		$this->config->setValueInt('external', 'max_site', max(array_keys($fixedSites)));
		return $fixedSites;
	}

	/**
	 * @return string[]
	 */
	public function getAvailableIcons() {
		try {
			$folder = $this->appData->getFolder('icons');
			$icons = $folder->getDirectoryListing();
			return array_map(function (ISimpleFile $icon) {
				return $icon->getName();
			}, $icons);
		} catch (NotFoundException $e) {
			return ['external.svg'];
		}
	}

	/**
	 * @return array[]
	 */
	public function getAvailableLanguages() {
		$languageCodes = $this->languageFactory->findAvailableLanguages();

		$languages = [];
		foreach ($languageCodes as $lang) {
			$l = $this->languageFactory->get('lib', $lang);
			$potentialName = $l->t('__language_name__');

			$ln = ['code' => $lang, 'name' => $lang];
			if ($l->getLanguageCode() === $lang && strpos($potentialName, '_') !== 0) {
				$ln = ['code' => $lang, 'name' => $potentialName];
			} elseif ($lang === 'en') {
				$ln = ['code' => $lang, 'name' => 'English (US)'];
			}

			$languages[] = $ln;
		}

		$commonLangCodes = ['en', 'es', 'fr', 'de', 'de_DE', 'ja', 'ar', 'ru', 'nl', 'it', 'pt_BR', 'pt_PT', 'da', 'fi_FI', 'nb_NO', 'sv', 'tr', 'zh_CN', 'ko'];

		usort($languages, function (array $a, array $b) use ($commonLangCodes): int {
			$aC = array_search($a['code'], $commonLangCodes, true);
			$bC = array_search($b['code'], $commonLangCodes, true);

			if ($aC === false && $bC !== false) {
				// If a is common, but b is not, list a before b
				return 1;
			}
			if ($aC !== false && $bC === false) {
				// If a is common, but b is not, list a before b
				return -1;
			}
			if ($aC !== false && $bC !== false) {
				// If a is common, but b is not, list a before b
				return $aC - $bC;
			}
			if ($a['code'] === $a['name'] && $b['code'] !== $b['name']) {
				// If a doesn't have a name, but b does, list b before a
				return 1;
			}
			if ($a['code'] !== $a['name'] && $b['code'] === $b['name']) {
				// If a does have a name, but b doesn't, list a before b
				return -1;
			}
			// Otherwise compare the names
			return strcmp($a['name'], $b['name']);
		});
		return $languages;
	}

	/**
	 * @return string
	 */
	protected function getDeviceFromUserAgent() {
		if ($this->request->isUserAgent([IRequest::USER_AGENT_CLIENT_ANDROID])) {
			return self::DEVICE_ANDROID;
		}
		if ($this->request->isUserAgent([IRequest::USER_AGENT_CLIENT_IOS])) {
			return self::DEVICE_IOS;
		}
		if ($this->request->isUserAgent([IRequest::USER_AGENT_CLIENT_DESKTOP])) {
			return self::DEVICE_DESKTOP;
		}
		return self::DEVICE_BROWSER;
	}
}
