<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2020 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\AppFramework\Services\IInitialState;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\Util;

/**
 * @template-implements IEventListener<Event>
 */
class BeforeTemplateRenderedListener implements IEventListener {
	public function __construct(
		private readonly SitesManager $sitesManager,
		private readonly INavigationManager $navigationManager,
		private readonly IURLGenerator $urlGenerator,
		private readonly IInitialState $initialState,
	) {
	}

	#[\Override]
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

		$data = [];
		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$imageDark = $this->generateImageLink($site, true);
				$imageLight = $this->generateImageLink($site, false);
				$href = $this->getHref($site);

				$data[] = ['name' => $site['name'], 'href' => $href, 'imageLight' => $imageLight, 'imageDark' => $imageDark];
			}
		}

		if (count($data) > 0) {
			$this->initialState->provideInitialState('external-quota-sites', $data);
			Util::addStyle('external', 'external-quota-files-sidebar');
			Util::addScript('external', 'external-quota-files-sidebar');
		}
	}

	protected function generateImageLink(array $site, bool $dark): string {
		if ($site['icon'] !== '') {
			return $this->urlGenerator->linkToRoute('external.icon.showIcon', ['icon' => $site['icon'], 'dark' => $dark]);
		}

		return $this->urlGenerator->linkToRoute('external.icon.showIcon', ['icon' => 'external.svg', 'dark' => $dark]);
	}

	protected function getHref(array $site): string {
		if (!$site['redirect']) {
			return $this->urlGenerator->linkToRoute('external.site.showPage', ['id' => $site['id'], 'path' => '']);
		}

		return $site['url'];
	}

	protected function generateNavigationLinks(): void {
		$sites = $this->sitesManager->getSitesToDisplay();

		foreach ($sites as $id => $site) {
			if ($site['type'] !== SitesManager::TYPE_LINK && $site['type'] !== SitesManager::TYPE_SETTING && $site['type'] !== SitesManager::TYPE_LOGIN) {
				continue;
			}

			$this->navigationManager->add(function () use ($site) {
				$image = $this->generateImageLink($site, false);
				$href = $this->getHref($site);

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
