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
use OCP\Settings\ISettings;
use Override;

class Admin implements ISettings {
	#[Override]
	public function getForm(): TemplateResponse {
		return new TemplateResponse('external', 'settings', [], '');
	}

	#[Override]
	public function getSection(): string {
		return 'external';
	}

	#[Override]
	public function getPriority(): int {
		return 55;
	}
}
