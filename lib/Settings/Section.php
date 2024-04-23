<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class Section implements IIconSection {
	private IL10N $l;
	private IURLGenerator $url;

	public function __construct(IURLGenerator $url, IL10N $l) {
		$this->url = $url;
		$this->l = $l;
	}

	public function getIcon(): string {
		return $this->url->imagePath('external', 'external-dark.svg');
	}

	public function getID(): string {
		return 'external';
	}

	public function getName(): string {
		return $this->l->t('External sites');
	}

	public function getPriority(): int {
		return 55;
	}
}
