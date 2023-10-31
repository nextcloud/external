<?php
/**
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
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
