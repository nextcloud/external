<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External;

use OCP\Capabilities\ICapability;

/**
 * Class Capabilities
 *
 * @package OCA\External
 */
class Capabilities implements ICapability {
	/**
	 * Return this classes capabilities
	 */
	public function getCapabilities() {
		return [
			'external' => [
				'v1' => [
					'sites',
					'device',
					'groups',
					'redirect',
				],
			],
		];
	}
}
