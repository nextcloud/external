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
use Override;

class Section implements IIconSection {
	public function __construct(
		private readonly IURLGenerator $url,
		private readonly IL10N $l,
	) {
	}

	#[Override]
	public function getIcon(): string {
		return $this->url->imagePath('external', 'external-dark.svg');
	}

	#[Override]
	public function getID(): string {
		return 'external';
	}

	#[Override]
	public function getName(): string {
		return $this->l->t('External sites');
	}

	#[Override]
	public function getPriority(): int {
		return 55;
	}
}
