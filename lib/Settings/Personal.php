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

class Personal implements ISettings {
	/** @var SitesManager */
	protected $sitesManager;

	/** @var IURLGenerator */
	protected $url;

	public function __construct(SitesManager $sitesManager, IURLGenerator $url) {
		$this->sitesManager = $sitesManager;
		$this->url = $url;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
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

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'personal-info';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 55;
	}
}
