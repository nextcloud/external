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
script('external', 'admin');

/** @var array $_ */
/** @var \OCP\IL10N $l */
?>
<div id="external">
	<div class="section">
		<h2><?php p($l->t('External sites'));?></h2>
		<p class="settings-hint"><?php p($l->t('Add a website directly to the app list in the top bar. This will be visible for all users and is useful to quickly reach other internally used web apps or important sites.')); ?></p>

		<div id="loading_sites" class="icon-loading-small"></div>

		<ul class="external_sites"></ul>

		<input type="button" id="add_external_site" value="<?php p($l->t('New site')); ?>" />

		<script type="text/template" id="site-template">
			<li data-site-id="{{id}}">
				<input type="text" class="site-name trigger-save" name="site-name" value="{{name}}" placeholder="<?php p($l->t('Name')); ?>">
				<input type="text" class="site-url trigger-save"  name="site-url" value="{{url}}" placeholder="<?php p($l->t('URL')); ?>">
				<a class="icon-more" href="#"></a>

				<div class="options hidden-FIXME">
					<div>
						<label>
							<span><?php p($l->t('Language')) ?></span>
							<select class="site-lang trigger-save">
								{{#each (getLanguages lang)}}
								{{#if (isSelected code ../lang)}}
								<option value="{{code}}" selected="selected">{{name}}</option>
								{{else}}
								<option value="{{code}}">{{name}}</option>
								{{/if}}
								{{/each}}
							</select>
						</label>
					</div>

					<div>
						<label>
							<span><?php p($l->t('Groups')) ?></span>
							<input type="hidden" name="site-groups" class="site-groups" value="{{ join groups }}" style="width: 320px;" />
						</label>
					</div>

					<div>
						<label>
							<span><?php p($l->t('Devices')) ?></span>
							<select class="site-device trigger-save">
								{{#each (getDevices device)}}
								{{#if (isSelected device ../device)}}
								<option value="{{device}}" selected="selected">{{name}}</option>
								{{else}}
								<option value="{{device}}">{{name}}</option>
								{{/if}}
								{{/each}}
							</select>
						</label>
					</div>

					<div>
						<label>
							<span><?php p($l->t('Icon')) ?></span>
							<select class="site-icon trigger-save">
								{{#each (getIcons icon)}}
								{{#if (isSelected icon ../icon)}}
								<option value="{{icon}}" selected="selected">{{name}}</option>
								{{else}}
								<option value="{{icon}}"><img class="svg action delete-button" src="<?php p(image_path('core', 'actions/delete.svg')); ?>" title="<?php p($l->t('Remove site')); ?>"> {{name}}</option>
								{{/if}}
								{{/each}}
							</select>
						</label>
					</div>

					<div>
						<label>
							<span><?php p($l->t('Position')) ?></span>
							<select class="site-type trigger-save">
								{{#each (getTypes type)}}
								{{#if (isSelected type ../type)}}
								<option value="{{type}}" selected="selected">{{name}}</option>
								{{else}}
								<option value="{{type}}">{{name}}</option>
								{{/if}}
								{{/each}}
							</select>
						</label>
					</div>

					<div class="site-redirect-box">
						<label>
							<span><?php p($l->t('Redirect')) ?></span>
							<input type="checkbox" id="site_redirect_{{id}}" name="site_redirect_{{id}}"
								   value="1" class="site-redirect checkbox trigger-save" {{#if redirect}} checked="checked"{{/if}} />
							<label for="site_redirect_{{id}}"><?php p($l->t('This site does not allow embeding')) ?></label>
						</label>
					</div>

					<div class="button delete-button"><?php p($l->t('Remove site')); ?></div>
				</div>
			</li>
		</script>

		<script type="text/template" id="icon-template">
			<li data-icon="{{name}}">
				<div class="img">
					<img src="{{url}}">
				</div>
				<span class="name">{{name}}</span>
				<span class="icon icon-delete" title="<?php p($l->t('Delete icon')); ?>"></span>
			</li>
		</script>

		<p>
			<em><?php p($l->t('Please note that some browsers will block displaying of sites via http if you are running https.')); ?></em>
			<br>
			<em><?php p($l->t('Furthermore please note that many sites these days disallow iframing due to security reasons.')); ?></em>
			<br>
			<em><?php p($l->t('We highly recommend to test the configured sites above properly.')); ?></em>
		</p>
	</div>

	<div class="section">
		<h2><?php p($l->t('Icons'));?></h2>

		<p class="settings-hint">
			<?php p($l->t('If you upload a test.png and a test-dark.png file, both will be used as one icon. The dark version will be used on mobile devices, otherwise the white icon is not visible on the white background on the mobile apps.')); ?>
			<?php p($l->t('Uploading an icon with the same name will replace the current icon.')); ?>
		</p>

		<ul class="icon-list">
		</ul>

		<form class="uploadButton" method="post" action="<?php p($_['uploadRoute']); ?>">
			<input id="uploadlogo" class="upload-logo-field" name="uploadlogo" type="file" />
			<label for="uploadlogo" class="button icon-upload svg" id="uploadlogo" title="<?php p($l->t('Upload new logo')) ?>"></label>
			<span class="msg"></span>
		</form>
	</div>
</div>
