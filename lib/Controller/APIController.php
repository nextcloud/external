<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
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

use OCA\External\Exceptions\IconNotFoundException;
use OCA\External\Exceptions\InvalidNameException;
use OCA\External\Exceptions\InvalidTypeException;
use OCA\External\Exceptions\InvalidURLException;
use OCA\External\Exceptions\LanguageNotFoundException;
use OCA\External\Exceptions\SiteNotFoundException;
use OCA\External\SitesManager;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;

class APIController extends OCSController {
	/** @var SitesManager */
	private $sitesManager;

	/** @var IURLGenerator */
	private $url;

	/** @var IL10N */
	private $l;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param SitesManager $sitesManager
	 * @param IURLGenerator $url
	 * @param IL10N $l
	 */
	public function __construct($appName, IRequest $request, SitesManager $sitesManager, IURLGenerator $url, IL10N $l) {
		parent::__construct($appName, $request);

		$this->sitesManager = $sitesManager;
		$this->url = $url;
		$this->l = $l;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return DataResponse
	 */
	public function get() {
		$data = $this->sitesManager->getSitesByLanguage($this->l->getLanguageCode());

		$sites = [];
		foreach ($data as $site) {
			$site['icon'] = $this->url->getAbsoluteURL($this->url->imagePath('external', $site['icon']));
			$sites[] = $site;
		}


		$etag = md5(json_encode($sites));
		if ($this->request->getHeader('If-None-Match') === $etag) {
			return new DataResponse([], Http::STATUS_NOT_MODIFIED);
		}

		return new DataResponse($sites, Http::STATUS_OK, ['ETag' => $etag]);
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return DataResponse
	 */
	public function getAdmin() {
		$icons = array_map(function($icon) {
			return ['icon' => $icon, 'name' => $icon];
		}, $this->sitesManager->getAvailableIcons());
		array_unshift($icons, ['icon' => '', 'name' => $this->l->t('Select an icon')]);

		$languages = $this->sitesManager->getAvailableLanguages();
		array_unshift($languages, ['code' => '', 'name' => $this->l->t('All languages')]);

		$types = [
			['type' => SitesManager::LINK, 'name' => $this->l->t('Normal')],
			['type' => SitesManager::SETTING, 'name' => $this->l->t('Setting')],
			['type' => SitesManager::QUOTA, 'name' => $this->l->t('Quota')],
		];

		return new DataResponse([
			'sites' => array_values($this->sitesManager->getSites()),
			'icons' => $icons,
			'languages' => $languages,
			'types' => $types,
		]);
	}

	/**
	 * @param string $name
	 * @param string $url
	 * @param string $lang
	 * @param string $type
	 * @param string $icon
	 * @return DataResponse
	 */
	public function add($name, $url, $lang, $type, $icon) {
		try {
			return new DataResponse($this->sitesManager->addSite($name, $url, $lang, $type, $icon));
		} catch (InvalidNameException $e) {
			return new DataResponse($this->l->t('The given name is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (InvalidURLException $e) {
			return new DataResponse($this->l->t('The given URL is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (LanguageNotFoundException $e) {
			return new DataResponse($this->l->t('The given language does not exist'), Http::STATUS_BAD_REQUEST);
		} catch (InvalidTypeException $e) {
			return new DataResponse($this->l->t('The given type is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (IconNotFoundException $e) {
			return new DataResponse($this->l->t('The given icon does not exist'), Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param string $url
	 * @param string $lang
	 * @param string $type
	 * @param string $icon
	 * @return DataResponse
	 */
	public function update($id, $name, $url, $lang, $type, $icon) {
		try {
			return new DataResponse($this->sitesManager->updateSite($id, $name, $url, $lang, $type, $icon));
		} catch (SiteNotFoundException $e) {
			return new DataResponse($this->l->t('The site does not exist'), Http::STATUS_NOT_FOUND);
		} catch (InvalidNameException $e) {
			return new DataResponse($this->l->t('The given name is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (InvalidURLException $e) {
			return new DataResponse($this->l->t('The given URL is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (LanguageNotFoundException $e) {
			return new DataResponse($this->l->t('The given language does not exist'), Http::STATUS_BAD_REQUEST);
		} catch (InvalidTypeException $e) {
			return new DataResponse($this->l->t('The given type is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (IconNotFoundException $e) {
			return new DataResponse($this->l->t('The given icon does not exist'), Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param int $id
	 * @return DataResponse
	 */
	public function delete($id) {
		$this->sitesManager->deleteSite($id);
		return new DataResponse();
	}
}
