<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

script('external', 'quota-personal');
?>
<div id="quota_link" class="section hidden">
	<a class="button" href="<?php p($_['quotaLink']); ?>"><?php p($_['quotaName']); ?></a>
</div>
