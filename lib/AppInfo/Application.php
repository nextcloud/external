<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2020 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\AppInfo;

use OCA\External\BeforeTemplateRenderedListener;
use OCA\External\Capabilities;
use OCA\External\Settings\Personal;
use OCA\External\SitesManager;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\Settings\IManager;

class Application extends App implements IBootstrap {
	public const APP_ID = 'external';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerCapability(Capabilities::class);
		$context->registerEventListener(BeforeTemplateRenderedEvent::class, BeforeTemplateRenderedListener::class);
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, BeforeTemplateRenderedListener::class);
	}

	public function boot(IBootContext $context): void {
		$context->injectFn([$this, 'registerSites']);
	}

	public function registerSites(
		SitesManager $sitesManager,
		IManager $settingsManager,
		INavigationManager $navigationManager,
		IURLGenerator $url): void {
		$sites = $sitesManager->getSitesToDisplay();

		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$settingsManager->registerSetting(IManager::SETTINGS_PERSONAL, Personal::class);
				continue;
			}

			if ($site['type'] !== SitesManager::TYPE_LINK
				&& $site['type'] !== SitesManager::TYPE_SETTING
				&& $site['type'] !== SitesManager::TYPE_LOGIN) {
				continue;
			}

			$navigationManager->add(function () use ($site, $url) {
				if ($site['icon'] !== '') {
					$image = $url->linkToRoute('external.icon.showIcon', ['icon' => $site['icon']]);
				} else {
					$image = $url->linkToRoute('external.icon.showIcon', ['icon' => 'external.svg']);
				}

				$href = $site['url'];
				if (!$site['redirect']) {
					$href = $url->linkToRoute('external.site.showPage', ['id' => $site['id'], 'path' => '']);
				}

				return [
					'id' => 'external_index' . $site['id'],
					'order' => 80 + $site['id'],
					'href' => $href,
					'icon' => $image,
					'type' => $site['type'],
					'name' => $site['name'],
					'target' => $site['redirect'],
				];
			});
		}
	}
}
