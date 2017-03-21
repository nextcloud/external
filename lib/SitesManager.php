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
use OCA\External\Exceptions\SiteNotFoundException;
use OCP\App\AppPathNotFoundException;
use OCP\App\IAppManager;
use OCP\IConfig;

class SitesManager {

	/** @var IConfig */
	protected $config;

	/** @var IAppManager */
	protected $appManager;

	public function __construct(IConfig $config, IAppManager $appManager) {
		$this->config = $config;
		$this->appManager = $appManager;
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
	 * @param string $icon
	 * @return array
	 * @throws InvalidNameException
	 * @throws InvalidURLException
	 * @throws IconNotFoundException
	 */
	public function addSite($name, $url, $icon) {
		$id = 1 + (int) $this->config->getAppValue('external', 'max_site', 0);

		if ($name === '') {
			throw new InvalidNameException();
		}

		if (filter_var($url, FILTER_VALIDATE_URL) === false ||
			  strpos($url, 'http://') === strpos($url, 'https://')) {
			throw new InvalidURLException();
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
	 * @param string $icon
	 * @return array
	 * @throws SiteNotFoundException
	 * @throws InvalidNameException
	 * @throws InvalidURLException
	 * @throws IconNotFoundException
	 */
	public function updateSite($id, $name, $url, $icon) {
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
}
