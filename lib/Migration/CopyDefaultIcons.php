<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
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

namespace OCA\External\Migration;


use OCP\App\IAppManager;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\SimpleFS\ISimpleFolder;
use OCP\IL10N;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class CopyDefaultIcons implements IRepairStep {

	/** @var IL10N */
	protected $l;
	/** @var IAppManager */
	protected $appManager;
	/** @var IAppData */
	protected $appData;

	/**
	 * @param IL10N $l
	 * @param IAppManager $appManager
	 * @param IAppData $appData
	 */
	public function __construct(IL10N $l, IAppManager $appManager, IAppData $appData) {
		$this->l = $l;
		$this->appManager = $appManager;
		$this->appData = $appData;
	}

	/**
	 * @return string
	 * @since 9.1.0
	 */
	public function getName() {
		return 'Copy default images to the app data directory';
	}

	/**
	 * @param IOutput $output
	 * @throws \Exception in case of failure
	 */
	public function run(IOutput $output) {
		try {
			$folder = $this->appData->getFolder('icons');
		} catch (NotFoundException $e) {
			$folder = $this->appData->newFolder('icons');
		}

		$this->copyDefaultIcon($output, $folder, 'external.svg');
		$this->copyDefaultIcon($output, $folder, 'external-dark.svg');
		$this->copyDefaultIcon($output, $folder, 'settings.svg');
	}

	/**
	 * @param IOutput $output
	 * @param ISimpleFolder $folder
	 * @param string $file
	 */
	protected function copyDefaultIcon(IOutput $output, ISimpleFolder $folder, $file) {
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
