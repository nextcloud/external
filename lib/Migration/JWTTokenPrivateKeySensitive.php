<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Migration;

use OCA\External\AppInfo\Application;
use OCP\IAppConfig;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class JWTTokenPrivateKeySensitive implements IRepairStep {
	public function __construct(
		private IAppConfig $config,
	) {
	}

	public function getName() {
		return 'Mark JWT token private key as sensitive';
	}

	public function run(IOutput $output): void {
		foreach ($this->config->getKeys(Application::APP_ID) as $key) {
			if (!str_starts_with($key, 'jwt_token_privkey_')) {
				continue;
			}

			$secret = $this->config->getValueString(Application::APP_ID, $key);
			$this->config->setValueString(Application::APP_ID, $key, $secret, sensitive: true);
		}
	}
}
