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
	/** @var SitesManager */
	protected $sitesManager;
	/** @var INavigationManager */
	protected $navigationManager;
	/** @var IURLGenerator */
	protected $urlGenerator;

	/** @var IInitialState */
	private $initialState;

	public function __construct(
		SitesManager $sitesManager,
		INavigationManager $navigationManager,
		IURLGenerator $urlGenerator,
		IInitialState $initialState,
	) {
		$this->sitesManager = $sitesManager;
		$this->navigationManager = $navigationManager;
		$this->urlGenerator = $urlGenerator;
		$this->initialState = $initialState;
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

		$data = [];
		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$image = $this->generateImageLink($site);
				$href = $this->getHref($site);

				$data[] = ['name' => $site['name'], 'href' => $href, 'image' => $image];
			}
		}

		if (count($data) > 0) {
			$this->initialState->provideInitialState('external-quota-sites', $data);
			Util::addScript('external', 'dist/quota-files-sidebar');
		}
	}

	protected function generateImageLink(array $site): string {
		if ($site['icon'] !== '') {
			return $this->urlGenerator->linkToRoute('external.icon.showIcon', ['icon' => $site['icon']]);
		}

		return $this->urlGenerator->linkToRoute('external.icon.showIcon', ['icon' => 'external.svg']);
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
				$image = $this->generateImageLink($site);
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
