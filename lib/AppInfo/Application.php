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
use OCA\External\Settings\Personal;
use OCA\External\SitesManager;
use OCP\AppFramework\App;
use OCP\IServerContainer;
use Symfony\Component\EventDispatcher\GenericEvent;

class Application extends App {

	public function __construct() {
		parent::__construct('external');

		$this->getContainer()->registerCapability(Capabilities::class);
	}

	public function register() {
		$server = $this->getContainer()->getServer();

		/** @var SitesManager $sitesManager */
		$sitesManager = $this->getContainer()->query(SitesManager::class);
		$sites = $sitesManager->getSitesToDisplay();

		$this->registerNavigationEntries($server, $sites);
		$this->registerPersonalPage($server, $sites);
	}

	/**
	 * @param IServerContainer $server
	 * @param array[] $sites
	 */
	public function registerNavigationEntries(IServerContainer $server, array $sites) {
		foreach ($sites as $id => $site) {
			if ($site['type'] !== SitesManager::TYPE_LINK && $site['type'] !== SitesManager::TYPE_SETTING && $site['type'] !== SitesManager::TYPE_LOGIN ) {
				continue;
			}

			$server->getNavigationManager()->add(function() use ($site, $server) {
				$url = $server->getURLGenerator();

				if ($site['icon'] !== '') {
					$image = $url->linkToRoute('external.icon.showIcon', ['icon' => $site['icon']]);
				} else {
					$image = $url->linkToRoute('external.icon.showIcon', ['icon' => 'external.svg']);
				}

				$href = $site['url'];
				if (!$site['redirect']) {
					$href = $url->linkToRoute('external.site.showPage', ['id'=> $site['id']]);
				}

				return [
					'id' => 'external_index' . $site['id'],
					'order' =>  80 + $site['id'],
					'href' => $href,
					'icon' => $image,
					'type' => $site['type'],
					'name' => $site['name'],
				];
			});
		}
	}

	/**
	 * @param IServerContainer $server
	 * @param array[] $sites
	 */
	public function registerPersonalPage(IServerContainer $server, array $sites) {
		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$server->getSettingsManager()->registerSetting('personal', Personal::class);
				$server->getEventDispatcher()->addListener('OCA\Files::loadAdditionalScripts', function(GenericEvent $event) use ($server, $site) {
					$url = $server->getURLGenerator();

					$hiddenFields = $event->getArgument('hiddenFields');

					$hiddenFields['external_quota_link'] = $site['url'];
					if (!$site['redirect']) {
						$hiddenFields['external_quota_link'] = $url->linkToRoute('external.site.showPage', ['id'=> $site['id']]);
					}
					$hiddenFields['external_quota_name'] = $site['name'];
					$event->setArgument('hiddenFields', $hiddenFields);

					\OCP\Util::addScript('external', 'quota-files-sidebar');
				});
				return;
			}
		}
	}
}
