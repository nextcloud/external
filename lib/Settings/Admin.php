<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IURLGenerator;
use OCP\Settings\ISettings;

class Admin implements ISettings {
	/** @var IURLGenerator */
	protected $url;

	public function __construct(IURLGenerator $url) {
		$this->url = $url;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		return new TemplateResponse('external', 'settings', [
			'uploadRoute' => $this->url->linkToRoute('external.icon.uploadIcon'),
		], 'blank');
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'external';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 *             the admin section. The forms are arranged in ascending order of the
	 *             priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 55;
	}
}
