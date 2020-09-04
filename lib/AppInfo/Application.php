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
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\INavigationManager;
use OCP\IServerContainer;
use OCP\IURLGenerator;
use OCP\Settings\IManager;
use OCP\Util;
use Symfony\Component\EventDispatcher\GenericEvent;

class Application extends App implements IBootstrap {

	public const APP_ID = 'external';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerCapability(Capabilities::class);
	}

	public function boot(IBootContext $context): void {
		/** @var SitesManager $sitesManager */
		$sitesManager = $context->getAppContainer()->get(SitesManager::class);
		$sites = $sitesManager->getSitesToDisplay();

		$this->registerNavigationEntries($context->getServerContainer(), $sites);
		$this->registerPersonalPage($context->getServerContainer(), $sites);
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

			$server->get(INavigationManager::class)->add(function() use ($site, $server) {
				$url = $server->get(IURLGenerator::class);

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
				$server->get(IManager::class)->registerSetting(IManager::KEY_PERSONAL_SETTINGS, Personal::class);
				$server->getEventDispatcher()->addListener('OCA\Files::loadAdditionalScripts', function(GenericEvent $event) use ($server, $site) {
					$url = $server->getURLGenerator();

					$hiddenFields = $event->getArgument('hiddenFields');

					$hiddenFields['external_quota_link'] = $site['url'];
					if (!$site['redirect']) {
						$hiddenFields['external_quota_link'] = $url->linkToRoute('external.site.showPage', ['id'=> $site['id']]);
					}
					$hiddenFields['external_quota_name'] = $site['name'];
					$event->setArgument('hiddenFields', $hiddenFields);

					Util::addScript('external', 'quota-files-sidebar');
				});
				return;
			}
		}
	}
}
