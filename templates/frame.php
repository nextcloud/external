<?php
/**
 * @copyright Copyright (c) 2012 Frank Karlitschek <frank@karlitschek.de>
 * @author Frank Karlitschek <frank@karlitschek.de>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2012 Frank Karlitschek <frank@karlitschek.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use OCP\Util;

Util::addScript('external', 'external-external');
Util::addStyle('external', 'external-external');

/** @var array $_ */
?>
<iframe id="ifm" title="<?php p($_['name']); ?>" src="<?php p($_['url']); ?>" allowfullscreen></iframe>
