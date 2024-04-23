<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2022 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2022 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

include_once __DIR__ . '/vendor/autoload.php';

/**
 * Sample script for reading and verifying the JWT parameter of external sites
 *
 * 1. Install JWT
 *
 *        composer require firebase/php-jwt
 *
 * 2. Define this page as external site with the parameter `?jwt={jwt}`
 * 3. Replace the key below with the result of the following command:
 *
 *        occ config:app:get external jwt_token_pubkey_es256
 *
 * 4. The $decoded variable contains the JWT token details
 *
 *        object(stdClass)[4]
 *        public 'iss' => string 'https://nextcloud25.local/' (length=26)
 *        public 'iat' => int 1663331793
 *        public 'exp' => int 1663335393
 *        public 'userdata' =>
 *          object(stdClass)[5]
 *            public 'email' => string 'admin@schilljs.com' (length=18)
 *            public 'uid' => string 'admin' (length=5)
 *            public 'displayName' => string 'Laura Adams' (length=11)
 *
 */

$key = '-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEys95/Y0nsJZ/OIz59H1eOTHmzkBs
d2yHITf+BPqxirqskhFpnF7OrXPG/i3HB2mC1JoBjvpGdWov0pkzst5CuQ==
-----END PUBLIC KEY-----
';

$jwt = rawurldecode($_REQUEST['jwt']);

try {
	$keyO = new \Firebase\JWT\Key($key, 'ES256');
	$decoded = \Firebase\JWT\JWT::decode($jwt, $keyO);
	var_dump($decoded);
} catch (\Throwable $e) {
	var_dump($e);
	exit;
}
