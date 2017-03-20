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

use OCP\IConfig;

class SitesManager {

	/** @var IConfig */
	protected $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
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
		return $fixedSites;
	}
}
