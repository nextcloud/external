<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Julius Haertl <jus@bitgrid.net>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-FileCopyrightText: 2017 Julius Haertl <jus@bitgrid.net>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External\Controller;

use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\SimpleFS\ISimpleFile;
use OCP\Files\SimpleFS\ISimpleFolder;
use OCP\IL10N;
use OCP\IRequest;

class IconController extends Controller {
	/** @var IL10N */
	private $l10n;
	/** @var IAppData */
	private $appData;
	/** @var IAppManager */
	private $appManager;
	/** @var ITimeFactory */
	private $timeFactory;

	/**
	 * ThemingController constructor.
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param IL10N $l
	 * @param IAppData $appData
	 * @param IAppManager $appManager
	 * @param ITimeFactory $timeFactory
	 */
	public function __construct(
		$appName,
		IRequest $request,
		IL10N $l,
		IAppData $appData,
		IAppManager $appManager,
		ITimeFactory $timeFactory
	) {
		parent::__construct($appName, $request);

		$this->l10n = $l;
		$this->appData = $appData;
		$this->appManager = $appManager;
		$this->timeFactory = $timeFactory;
	}

	/**
	 * Upload an icon to the appdata folder
	 *
	 * @return DataResponse
	 */
	public function uploadIcon() {
		$icon = $this->request->getUploadedFile('uploadicon');
		if (empty($icon)) {
			return new DataResponse([
				'error' => $this->l10n->t('No file uploaded'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		$imageSize = getimagesize($icon['tmp_name']);

		if ($imageSize === false && $icon['type'] !== 'image/svg+xml') {
			// Not an image
			return new DataResponse([
				'error' => $this->l10n->t('Provided file is not an image'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		if ($imageSize !== false && (!in_array($imageSize[0], [16, 24, 32], true) || $imageSize[0] !== $imageSize[1])) {
			// Not a square
			return new DataResponse([
				'error' => $this->l10n->t('Provided image is not a square of 16, 24 or 32 pixels width'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		try {
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
		} catch (NotPermittedException $e) {
			return new DataResponse([
				'error' => $this->l10n->t('An error occurred while uploading the icon, please make sure the data directory is writable'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		} catch (\RuntimeException $e) {
			return new DataResponse([
				'error' => $this->l10n->t('An error occurred while uploading the icon, please make sure the data directory is writable'),
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		$target->putContent(file_get_contents($icon['tmp_name'], false));

		return new DataResponse([
			'id' => $target->getName(),
			'name' => $target->getName(),
		]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $icon
	 * @return FileDisplayResponse
	 */
	public function showIcon($icon) {
		$folder = $this->appData->getFolder('icons');
		try {
			$iconFile = $folder->getFile($icon);
		} catch (NotFoundException $exception) {
			$iconFile = $this->getDefaultIcon($folder, 'external.svg');
		}

		if (strpos($icon, '-dark.') === false && $this->request->isUserAgent([
			IRequest::USER_AGENT_CLIENT_ANDROID,
			IRequest::USER_AGENT_CLIENT_IOS,
			IRequest::USER_AGENT_CLIENT_DESKTOP,
		])) {
			// Check if there is a dark icon as well
			$basename = pathinfo($iconFile->getName(), PATHINFO_FILENAME);
			$basename .= '-dark.';
			$basename .= pathinfo($iconFile->getName(), PATHINFO_EXTENSION);

			try {
				$iconFile = $folder->getFile($basename);
			} catch (NotFoundException $exception) {
			}
		}

		$response = new FileDisplayResponse($iconFile, Http::STATUS_OK, ['Content-Type' => $iconFile->getMimeType()]);
		$response->cacheFor(86400);
		$expires = \DateTime::createFromImmutable($this->timeFactory->now());
		$expires->add(new \DateInterval('PT24H'));
		$response->addHeader('Expires', $expires->format(\DateTimeInterface::RFC2822));
		$response->addHeader('Pragma', 'cache');
		$csp = new ContentSecurityPolicy();
		$response->setContentSecurityPolicy($csp);
		return $response;
	}

	/**
	 * @param string $icon
	 * @return DataResponse
	 */
	public function deleteIcon($icon) {
		$folder = $this->appData->getFolder('icons');

		try {
			$iconFile = $folder->getFile($icon);
			$iconFile->delete();

			if (strpos($icon, '-dark.') !== false) {
				// Delete the white version as well
				$iconFile = $folder->getFile(str_replace('-dark.', '.', $icon));
				$iconFile->delete();
			}
		} catch (NotFoundException $exception) {
		} catch (NotPermittedException $exception) {
		}

		return new DataResponse();
	}

	/**
	 * @param ISimpleFolder $folder
	 * @param string $file
	 * @return ISimpleFile
	 * @throws NotFoundException
	 */
	protected function getDefaultIcon(ISimpleFolder $folder, $file) {
		try {
			return $folder->getFile($file);
		} catch (NotFoundException $exception) {
		}

		// Default icon is missing, copy it from img/
		$content = file_get_contents($this->appManager->getAppPath('external') . '/img/' . $file);
		if ($content === false) {
			throw new NotFoundException();
		}

		$externalSVG = $folder->newFile($file);
		$externalSVG->putContent($content);
		return $externalSVG;
	}
}
