<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2022 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2022 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\External;

use OCA\External\AppInfo\Application;
use OCA\External\Vendor\Firebase\JWT\JWT;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IAppConfig;
use OCP\IURLGenerator;

class JWTManager {
	protected IAppConfig $config;
	protected ITimeFactory $timeFactory;
	protected IURLGenerator $urlGenerator;

	public function __construct(IAppConfig $config,
		ITimeFactory $timeFactory,
		IURLGenerator $urlGenerator) {
		$this->config = $config;
		$this->timeFactory = $timeFactory;
		$this->urlGenerator = $urlGenerator;
	}


	/**
	 * @param array $userdata
	 * @return string
	 */
	public function getToken(array $userdata): string {
		$timestamp = $this->timeFactory->now()->getTimestamp();
		$data = [
			'iss' => $this->urlGenerator->getAbsoluteURL(''),
			'iat' => $timestamp,
			'exp' => $timestamp + 3600,  // Valid for 1 hour.
			'userdata' => $userdata,
		];

		$alg = $this->getTokenAlgorithm();
		$secret = $this->getTokenPrivateKey($alg);

		/** @psalm-suppress UndefinedClass */
		return JWT::encode($data, $secret, $alg);
	}

	public function getTokenPublicKey(?string $alg = null): string {
		if ($alg === null) {
			$alg = $this->getTokenAlgorithm();
		}
		$this->ensureTokenKeys($alg);

		return $this->config->getValueString(Application::APP_ID, 'jwt_token_pubkey_' . strtolower($alg));
	}

	protected function getTokenPrivateKey(?string $alg = null): string {
		if ($alg === null) {
			$alg = $this->getTokenAlgorithm();
		}
		$this->ensureTokenKeys($alg);

		return $this->config->getValueString(Application::APP_ID, 'jwt_token_privkey_' . strtolower($alg));
	}

	protected function ensureTokenKeys(string $alg): void {
		$secret = $this->config->getValueString(Application::APP_ID, 'jwt_token_privkey_' . strtolower($alg));
		if ($secret) {
			return;
		}

		if (strpos($alg, 'ES') === 0) {
			$privKey = openssl_pkey_new([
				'curve_name' => 'prime256v1',
				'private_key_bits' => 2048,
				'private_key_type' => OPENSSL_KEYTYPE_EC,
			]);
			$pubKey = openssl_pkey_get_details($privKey);
			$public = $pubKey['key'];
			if (!openssl_pkey_export($privKey, $secret)) {
				throw new \Exception('Could not export private key');
			}
		} elseif (strpos($alg, 'RS') === 0) {
			$privKey = openssl_pkey_new([
				'private_key_bits' => 2048,
				'private_key_type' => OPENSSL_KEYTYPE_RSA,
			]);
			$pubKey = openssl_pkey_get_details($privKey);
			$public = $pubKey['key'];
			if (!openssl_pkey_export($privKey, $secret)) {
				throw new \Exception('Could not export private key');
			}
		} elseif ($alg === 'EdDSA') {
			$privKey = sodium_crypto_sign_keypair();
			$public = base64_encode(sodium_crypto_sign_publickey($privKey));
			$secret = base64_encode(sodium_crypto_sign_secretkey($privKey));
		} else {
			throw new \Exception('Unsupported algorithm ' . $alg);
		}

		$this->config->setValueString(Application::APP_ID, 'jwt_token_privkey_' . strtolower($alg), $secret, sensitive: true);
		$this->config->setValueString(Application::APP_ID, 'jwt_token_pubkey_' . strtolower($alg), $public);
	}

	protected function getTokenAlgorithm(): string {
		return $this->config->getValueString(Application::APP_ID, 'jwt_token_alg', 'ES256');
	}
}
