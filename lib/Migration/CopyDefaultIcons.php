<?php

/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Migration;

use OCP\App\IAppManager;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\SimpleFS\ISimpleFolder;
use OCP\IL10N;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class CopyDefaultIcons implements IRepairStep {
	protected IL10N $l;
	protected IAppManager $appManager;
	protected IAppData $appData;

	public function __construct(IL10N $l, IAppManager $appManager, IAppData $appData) {
		$this->l = $l;
		$this->appManager = $appManager;
		$this->appData = $appData;
	}

	public function getName(): string {
		return 'Copy default images to the app data directory';
	}

	/**
	 * @throws \Exception in case of failure
	 */
	public function run(IOutput $output): void {
		try {
			$folder = $this->appData->getFolder('icons');
		} catch (NotFoundException $e) {
			$folder = $this->appData->newFolder('icons');
		}

		$this->copyDefaultIcon($output, $folder, 'external.svg');
		$this->copyDefaultIcon($output, $folder, 'external-dark.svg');
		$this->copyDefaultIcon($output, $folder, 'settings.svg');
		$this->copyDefaultIcon($output, $folder, 'settings-dark.svg');
	}

	protected function copyDefaultIcon(IOutput $output, ISimpleFolder $folder, string $file): void {
		try {
			$folder->getFile($file);
			$output->info(sprintf('Icon %s already exists', $file));
			return;
		} catch (NotFoundException $exception) {
		}

		// Default icon is missing, copy it from img/
		$content = file_get_contents($this->appManager->getAppPath('external') . '/img/' . $file);
		if ($content === false) {
			$output->info(sprintf('Could not read icon %s', $file));
			return;
		}

		$externalSVG = $folder->newFile($file);
		$externalSVG->putContent($content);

		$output->info(sprintf('Icon %s copied successfully', $file));
	}
}
