<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
 * @author Julius Haertl <jus@bitgrid.net>
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

namespace OCA\External\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\IL10N;
use OCP\IRequest;

class IconController extends Controller {
	/** @var IL10N */
	private $l10n;
	/** @var IAppData */
	private $appData;

	/**
	 * ThemingController constructor.
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param IL10N $l
	 * @param IAppData $appData
	 */
	public function __construct(
		$appName,
		IRequest $request,
		IL10N $l,
		IAppData $appData
	) {
		parent::__construct($appName, $request);

		$this->l10n = $l;
		$this->appData = $appData;
	}

	/**
	 * Upload an icon to the appdata folder
	 *
	 * @return DataResponse
	 */
	public function uploadIcon() {
		$icon = $this->request->getUploadedFile('uploadlogo');
		if (empty($icon)) {
			return new DataResponse([
				'data' => [
					'message' => $this->l10n->t('No file uploaded'),
				]], Http::STATUS_UNPROCESSABLE_ENTITY
			);
		}

		$imageSize = getimagesize($icon['tmp_name']);

		if ($imageSize === false && $icon['type'] !== 'image/svg+xml') {
			// Not an image
			return new DataResponse([
				'error' => $this->l10n->t('Provided file is not an image'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		if ($imageSize !== false && $imageSize[0] !== $imageSize[1]) {
			// Not a square
			return new DataResponse([
				'error' => $this->l10n->t('Provided image is not a square'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		try {
			$icons = $this->appData->getFolder('icons');
		} catch (NotFoundException $e) {
			$icons = $this->appData->newFolder('icons');
		}

		try {
			$target = $icons->getFile($icon['name']);
		} catch (NotFoundException $e) {
			$target = $icons->newFile($icon['name']);
		}

		$target->putContent(file_get_contents($icon['tmp_name'], 'r'));

		return new DataResponse([
			'id' => $target->getName(),
			'name' => $target->getName(),
		]);
	}
}
