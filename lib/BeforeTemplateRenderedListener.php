<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Joas Schilling <coding@schilljs.com>
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

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\Util;

class BeforeTemplateRenderedListener implements IEventListener {

	/** @var SitesManager */
	protected $sitesManager;
	/** @var INavigationManager */
	protected $navigationManager;
	/** @var IURLGenerator */
	protected $urlGenerator;

	public function __construct(SitesManager $sitesManager,
								INavigationManager $navigationManager,
								IURLGenerator $urlGenerator) {
		$this->sitesManager = $sitesManager;
		$this->navigationManager = $navigationManager;
		$this->urlGenerator = $urlGenerator;
	}

	public function handle(Event $event): void {
		if ($event instanceof BeforeTemplateRenderedEvent) {
			$this->generateNavigationLinks();
		}

		if ($event instanceof LoadAdditionalScriptsEvent) {
			$this->loadQuotaInformationOnFilesApp($event);
		}
	}

	protected function loadQuotaInformationOnFilesApp(LoadAdditionalScriptsEvent $event): void {
		$sites = $this->sitesManager->getSitesToDisplay();

		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$link = $site['url'];
				if (!$site['redirect']) {
					$link = $this->urlGenerator->linkToRoute('external.site.showPage', ['id'=> $site['id']]);
				}

				$event->addHiddenField('external_quota_link', $link);
				$event->addHiddenField('external_quota_name', $site['name']);

				Util::addScript('external', 'quota-files-sidebar');
				return;
			}
		}
	}

	protected function generateNavigationLinks(): void {
		$sites = $this->sitesManager->getSitesToDisplay();

		foreach ($sites as $id => $site) {
			if ($site['type'] !== SitesManager::TYPE_LINK && $site['type'] !== SitesManager::TYPE_SETTING && $site['type'] !== SitesManager::TYPE_LOGIN ) {
				continue;
			}

			$this->navigationManager->add(function() use ($site) {
				if ($site['icon'] !== '') {
					$image = $this->urlGenerator->linkToRoute('external.icon.showIcon', ['icon' => $site['icon']]);
				} else {
					$image = $this->urlGenerator->linkToRoute('external.icon.showIcon', ['icon' => 'external.svg']);
				}

				$href = $site['url'];
				if (!$site['redirect']) {
					$href = $this->urlGenerator->linkToRoute('external.site.showPage', ['id'=> $site['id']]);
				}

				return [
					'id' => 'external_index' . $site['id'],
					'order' => 80 + $site['id'],
					'href' => $href,
					'icon' => $image,
					'type' => $site['type'],
					'name' => $site['name'],
				];
			});
		}
	}
}
