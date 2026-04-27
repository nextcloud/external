<?php

/**
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2018 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Settings;

use OCA\External\SitesManager;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IURLGenerator;
use OCP\Settings\ISettings;
use Override;

class Personal implements ISettings {
	public function __construct(
		private readonly SitesManager $sitesManager,
		private readonly IURLGenerator $url,
	) {
	}

	#[Override]
	public function getForm(): TemplateResponse {
		$sites = $this->sitesManager->getSitesToDisplay();

		$quotaLink = [];
		foreach ($sites as $site) {
			if ($site['type'] === SitesManager::TYPE_QUOTA) {
				$quotaLink = $site;
				break;
			}
		}

		$url = $quotaLink['url'];
		if (!$quotaLink['redirect']) {
			$url = $this->url->linkToRoute('external.site.showPage', ['id' => $quotaLink['id'], 'path' => '']);
		}

		return new TemplateResponse('external', 'quota', [
			'quotaLink' => $url,
			'quotaName' => $quotaLink['name'],
		], '');
	}

	#[Override]
	public function getSection(): string {
		return 'personal-info';
	}

	#[Override]
	public function getPriority(): int {
		return 55;
	}
}
