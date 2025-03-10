<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @copyright Copyright (c) 2012 Frank Karlitschek <frank@karlitschek.de>
 * @author Frank Karlitschek <frank@karlitschek.de>
 * @author Joas Schilling <coding@schilljs.com>
 * @license GNU AGPL version 3 or any later version
 *
 * SPDX-FileCopyrightText: 2017 Joas Schilling <coding@schilljs.com>
 * SPDX-FileCopyrightText: 2012 Frank Karlitschek <frank@karlitschek.de>
 * SPDX-License-Identifier: AGPL-3.0-or-later
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
		<p class="settings-hint"><?php p($l->t('The placeholders {email}, {uid}, {displayname}, {groups}, {language} and {locale} can be used and are filled with the user´s values to customize the links.')); ?></p>
		<p class="settings-hint"><?php p($l->t('When accessing the external site through the Nextcloud link, path parameters will be forwarded to the external site. So you can also create deep links (e.g. "mycloud.com/external/1/pageA" will lead to Nextcloud with the iframe pointed at "externalsite.com/pageA").')); ?></p>
		<p class="settings-hint"><?php print_unescaped(str_replace(
			['{linkstart}', '{linkend}'],
			['<a target="_blank" class="external" href="https://github.com/nextcloud/external/blob/master/docs/jwt-sample.php" rel="noreferrer nofollow">', ' ↗</a>'],
			$l->t('A JSON Web Token containing user´s email, uid and display name in its payload can be embedded into the link using the {jwt} placeholder. See the {linkstart}documentation{linkend} how to decode it.')
		)); ?></p>

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

		<form class="uploadButton">
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
