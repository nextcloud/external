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
use OCA\External\Exceptions\InvalidURLException;
use OCA\External\Exceptions\SiteNotFoundException;
use OCA\External\SitesManager;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IL10N;
use OCP\IRequest;

class APIController extends OCSController {
	/** @var SitesManager */
	private $sitesManager;

	/** @var IL10N */
	private $l;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param SitesManager $sitesManager
	 * @param IL10N $l
	 */
	public function __construct($appName, IRequest $request, SitesManager $sitesManager, IL10N $l) {
		parent::__construct($appName, $request);

		$this->sitesManager = $sitesManager;
		$this->l = $l;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return DataResponse
	 */
	public function getAll() {
		return new DataResponse($this->sitesManager->getSites());
	}

	/**
	 * @param string $name
	 * @param string $url
	 * @param string $icon
	 * @return DataResponse
	 */
	public function add($name, $url, $icon) {
		try {
			return new DataResponse($this->sitesManager->addSite($name, $url, $icon));
		} catch (InvalidNameException $e) {
			return new DataResponse($this->l->t('The given name is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (InvalidURLException $e) {
			return new DataResponse($this->l->t('The given url is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (IconNotFoundException $e) {
			return new DataResponse($this->l->t('The given icon does not exist'), Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param string $url
	 * @param string $icon
	 * @return DataResponse
	 */
	public function update($id, $name, $url, $icon) {
		try {
			return new DataResponse($this->sitesManager->updateSite($id, $name, $url, $icon));
		} catch (SiteNotFoundException $e) {
			return new DataResponse($this->l->t('The site does not exist'), Http::STATUS_NOT_FOUND);
		} catch (InvalidNameException $e) {
			return new DataResponse($this->l->t('The given name is invalid'), Http::STATUS_BAD_REQUEST);
		} catch (InvalidURLException $e) {
			return new DataResponse($this->l->t('The given url is invalid'), Http::STATUS_BAD_REQUEST);
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
