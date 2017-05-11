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

namespace OCA\External\AppInfo;

use OCA\External\Capabilities;
use OCA\External\SitesManager;
use OCP\AppFramework\App;
use OCP\IServerContainer;

class Application extends App {

	public function __construct() {
		parent::__construct('external');

		$this->getContainer()->registerCapability(Capabilities::class);
	}

	public function register() {
		$server = $this->getContainer()->getServer();

		/** @var SitesManager $sitesManager */
		$sitesManager = $this->getContainer()->query(SitesManager::class);
		$sites = $sitesManager->getSitesByLanguage($server->getL10NFactory()->findLanguage());

		$this->registerNavigationEntries($server, $sites);
		$this->registerPersonalPage($sites);
	}

	/**
	 * @param IServerContainer $server
	 * @param array[] $sites
	 */
	public function registerNavigationEntries(IServerContainer $server, array $sites) {
		foreach ($sites as $id => $site) {
			if ($site['type'] !== SitesManager::LINK && $site['type'] !== SitesManager::SETTING) {
				continue;
			}

			$server->getNavigationManager()->add(function() use ($site, $server) {
				$url = $server->getURLGenerator();

				try {
					$image = $url->imagePath('external', $site['icon']);
				} catch (\RuntimeException $e) {
					$image = $url->imagePath('external', 'external.svg');
				}
				return [
					'id' => 'external_index' . $site['id'],
					'order' =>  80 + $site['id'],
					'href' => $url->linkToRoute('external.page.showPage', ['id'=> $site['id']]),
					'icon' => $image,
					'type' => $site['type'],
					'name' => $site['name'],
				];
			});
		}
	}

	/**
	 * @param array[] $sites
	 */
	public function registerPersonalPage(array $sites) {
		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::QUOTA) {
				\OCP\App::registerPersonal('external', 'personal');
				return;
			}
		}
	}
}
