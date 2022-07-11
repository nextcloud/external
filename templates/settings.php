<?php
/**
 * @copyright Copyright (c) 2012 Frank Karlitschek <frank@karlitschek.de>
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @author Frank Karlitschek <frank@karlitschek.de>
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

style('external', 'style');
script('external', 'dist/admin');
script('settings', 'apps');
script('external', 'templates');

/** @var array $_ */
/** @var \OCP\IL10N $l */
?>
<div id="external">
	<div class="section">
		<h2><?php p($l->t('External sites'));?></h2>
		<p class="settings-hint"><?php p($l->t('Add a website directly to the app list in the top bar. This will be visible for all users and is useful to quickly reach other internally used web apps or important sites.')); ?></p>
		<p class="settings-hint"><?php p($l->t('The placeholders {email}, {uid} and {displayname} can be used and are filled with the user´s values to customize the links.')); ?></p>
		<p class="settings-hint"><?php p($l->t('A JSON Web Token containing user´s email, uid and display name in its payload can be embedded into the link using the {jwt} placeholder. The HS256 secret used for signing the JWT should be defined in the "external_jwt_secret" configuration parameter.')); ?></p>

		<div id="loading_sites" class="icon-loading-small"></div>

		<ul class="external_sites"></ul>

		<input type="button" id="add_external_site" value="<?php p($l->t('New site')); ?>" />

		<script type="text/template" id="icon-template">

		</script>

		<p>
			<em><?php p($l->t('Please note that some browsers will block displaying of sites via HTTP if you are running HTTPS.')); ?></em>
			<br>
			<em><?php p($l->t('Furthermore please note that many sites these days disallow iframing due to security reasons.')); ?></em>
			<br>
			<em><?php p($l->t('We highly recommend to test the configured sites above properly.')); ?></em>
		</p>
	</div>

	<div class="section">
		<h2><?php p($l->t('Icons'));?></h2>

		<p class="settings-hint">
			<?php p($l->t('If you upload a test.png and a test-dark.png file, both will be used as one icon. The dark version will be used on mobile devices, otherwise the white icon is not visible on the white background in the mobile apps.')); ?>
			<?php p($l->t('Uploading an icon with the same name will replace the current icon.')); ?>
		</p>

		<ul class="icon-list">
		</ul>

		<form class="uploadButton" method="post" action="<?php p($_['uploadRoute']); ?>">
			<input id="uploadicon" class="hidden" name="uploadicon" type="file" />
			<label for="uploadicon">
				<span class="button">
					<span class="icon icon-upload svg"></span>
					<?php p($l->t('Upload new icon')) ?>
				</span>
			</label>
			<span class="msg"></span>
		</form>
	</div>
</div>
