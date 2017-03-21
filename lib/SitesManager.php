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

namespace OCA\External;

use OCA\External\Exceptions\IconNotFoundException;
use OCA\External\Exceptions\InvalidNameException;
use OCA\External\Exceptions\InvalidURLException;
use OCA\External\Exceptions\LanguageNotFoundException;
use OCA\External\Exceptions\SiteNotFoundException;
use OCP\App\AppPathNotFoundException;
use OCP\App\IAppManager;
use OCP\IConfig;
use OCP\L10N\IFactory;

class SitesManager {

	/** @var IConfig */
	protected $config;

	/** @var IFactory */
	protected $languageFactory;

	/** @var IAppManager */
	protected $appManager;

	public function __construct(IConfig $config, IAppManager $appManager, IFactory $languageFactory) {
		$this->config = $config;
		$this->appManager = $appManager;
		$this->languageFactory = $languageFactory;
	}

	/**
	 * @param int $id
	 * @return array
	 * @throws SiteNotFoundException
	 */
	public function getSiteById($id) {
		$sites = $this->getSites();

		if (isset($sites[$id])) {
			return $sites[$id];
		}

		throw new SiteNotFoundException();
	}

	/**
	 * @param string $lang
	 * @return array[]
	 */
	public function getSitesByLanguage($lang) {
		$sites = $this->getSites();

		$langSites = [];
		foreach ($sites as $id => $site) {
			if ($site['lang'] !== '' && $site['lang'] !== $lang) {
				continue;
			}
			$langSites[$id] = $site;
		}

		return $langSites;
	}

	/**
	 * @return array[]
	 */
	public function getSites() {
		$jsonEncodedList = $this->config->getAppValue('external', 'sites', '');
		$sites = json_decode($jsonEncodedList, true);

		if (!is_array($sites) || empty($sites)) {
			return [];
		}

		if (isset($sites[0][0])) {
			return $this->getSitesFromOldConfig($sites);
		}

		return $sites;
	}

	/**
	 * @param string $name
	 * @param string $url
	 * @param string $lang
	 * @param string $icon
	 * @return array
	 * @throws InvalidNameException
	 * @throws InvalidURLException
	 * @throws LanguageNotFoundException
	 * @throws IconNotFoundException
	 */
	public function addSite($name, $url, $lang, $icon) {
		$id = 1 + (int) $this->config->getAppValue('external', 'max_site', 0);

		if ($name === '') {
			throw new InvalidNameException();
		}

		if (filter_var($url, FILTER_VALIDATE_URL) === false ||
			  strpos($url, 'http://') === strpos($url, 'https://')) {
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

		$icons = $this->getAvailableIcons();
		if ($icon === '') {
			$icon = 'external.svg';
		}
		if (!in_array($icon, $icons, true)) {
			throw new IconNotFoundException();
		}

		$sites = $this->getSites();
		$sites[$id] = [
			'id'   => $id,
			'name' => $name,
			'url'  => $url,
			'lang' => $lang,
			'icon' => $icon,
		];
		$this->config->setAppValue('external', 'sites', json_encode($sites));
		$this->config->setAppValue('external', 'max_site', $id);

		return $sites[$id];
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param string $url
	 * @param string $lang
	 * @param string $icon
	 * @return array
	 * @throws SiteNotFoundException
	 * @throws InvalidNameException
	 * @throws InvalidURLException
	 * @throws LanguageNotFoundException
	 * @throws IconNotFoundException
	 */
	public function updateSite($id, $name, $url, $lang, $icon) {
		$sites = $this->getSites();
		if (!isset($sites[$id])) {
			throw new SiteNotFoundException();
		}

		if ($name === '') {
			throw new InvalidNameException();
		}

		if (filter_var($url, FILTER_VALIDATE_URL) === false ||
			  strpos($url, 'http://') === strpos($url, 'https://')) {
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

		$icons = $this->getAvailableIcons();
		if ($icon === '') {
			$icon = 'external.svg';
		}
		if (!in_array($icon, $icons, true)) {
			throw new IconNotFoundException();
		}

		$sites[$id] = [
			'id'   => $id,
			'name' => $name,
			'url'  => $url,
			'lang' => $lang,
			'icon' => $icon,
		];
		$this->config->setAppValue('external', 'sites', json_encode($sites));

		return $sites[$id];
	}

	/**
	 * @param int $id
	 */
	public function deleteSite($id) {
		$sites = $this->getSites();
		if (!isset($sites[$id])) {
			return;
		}

		unset($sites[$id]);
		$this->config->setAppValue('external', 'sites', json_encode($sites));
	}

	/**
	 * @param array[] $sites
	 * @return array[]
	 */
	protected function getSitesFromOldConfig($sites) {
		$fixedSites = [];

		/** @var array[] $sites */
		foreach ($sites as $id => $site) {
			$fixedSites[$id + 1] = [
				'id'   => $id + 1,
				'name' => $site[0],
				'url'  => $site[1],
				// TODO when php7+ is supported: 'icon' => $site[2] ?? 'external.svg',
				'icon' => isset($site[2]) ? $site[2] : 'external.svg',
				'lang' => '',
			];
		}

		$this->config->setAppValue('external', 'sites', json_encode($fixedSites));
		$this->config->setAppValue('external', 'max_site', max(array_keys($fixedSites)));
		return $fixedSites;
	}

	/**
	 * @return string[]
	 */
	public function getAvailableIcons() {
		try {
			return array_map('basename', glob($this->appManager->getAppPath('external') . '/img/*.*'));
		} catch (AppPathNotFoundException $e) {
			return ['external.svg'];
		}
	}

	/**
	 * @return string[]
	 */
	public function getAvailableLanguages() {
		$languageCodes = $this->languageFactory->findAvailableLanguages();

		$languages = [];
		foreach ($languageCodes as $lang) {
			$l = $this->languageFactory->get('settings', $lang);
			$potentialName = $l->t('__language_name__');

			$ln = ['code' => $lang, 'name' => $lang];
			if ($l->getLanguageCode() === $lang && strpos($potentialName, '_') !== 0) {
				$ln = ['code' => $lang, 'name' => $potentialName];
			} else if ($lang === 'en') {
				$ln = ['code' => $lang, 'name' => 'English (US)'];
			}

			$languages[] = $ln;
		}

		$commonLangCodes = ['en', 'es', 'fr', 'de', 'de_DE', 'ja', 'ar', 'ru', 'nl', 'it', 'pt_BR', 'pt_PT', 'da', 'fi_FI', 'nb_NO', 'sv', 'tr', 'zh_CN', 'ko'];

		usort($languages, function ($a, $b) use ($commonLangCodes) {
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
}
