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
<form id="external">
	<div class="section">
		<h2><?php p($l->t('External sites'));?></h2>
		<p class="settings-hint"><?php p($l->t('Add a website directly to the app list in the top bar. This will be visible for all users and is useful to quickly reach other internally used web apps or important sites.')); ?></p>

		<div id="loading_sites" class="icon-loading-small"></div>

		<ul class="external_sites"></ul>

		<input type="button" id="add_external_site" value="<?php p($l->t('Add')); ?>" />
		<span class="msg"></span>

		<script type="text/template" id="site-template">
			<li data-site-id="{{id}}">
				<input type="text" class="site-name trigger-save" name="site-name" value="{{name}}" placeholder="<?php p($l->t('Name')); ?>" />
				<input type="text" class="site-url trigger-save"  name="site-url" value="{{url}}" placeholder="<?php p($l->t('URL')); ?>" />
				<select class="site-icon trigger-save">
					{{#each (getIcons icon)}}
						{{#if (isSelected icon ../icon)}}
							<option value="{{icon}}" selected="selected">{{name}}</option>
						{{else}}
							<option value="{{icon}}">{{name}}</option>
						{{/if}}
					{{/each}}
				</select>
				<select class="site-lang trigger-save">
					{{#each (getLanguages lang)}}
						{{#if (isSelected code ../lang)}}
							<option value="{{code}}" selected="selected">{{name}}</option>
						{{else}}
							<option value="{{code}}">{{name}}</option>
						{{/if}}
					{{/each}}
				</select>
				<select class="site-type trigger-save">
					{{#each (getTypes type)}}
						{{#if (isSelected type ../type)}}
							<option value="{{type}}" selected="selected">{{name}}</option>
						{{else}}
							<option value="{{type}}">{{name}}</option>
						{{/if}}
					{{/each}}
				</select>
				<select class="site-device trigger-save">
					{{#each (getDevices device)}}
						{{#if (isSelected device ../device)}}
							<option value="{{device}}" selected="selected">{{name}}</option>
						{{else}}
							<option value="{{device}}">{{name}}</option>
						{{/if}}
					{{/each}}
				</select>
				<img class="svg action delete-button" src="<?php p(image_path('core', 'actions/delete.svg')); ?>" title="<?php p($l->t('Remove site')); ?>" />
				<img class="svg action saving hidden" src="<?php p(image_path('core', 'loading-small.gif')); ?>" alt="<?php p($l->t('Saving')); ?>" />
				<img class="svg action saved hidden" src="<?php p(image_path('core', 'actions/checkmark-color.svg')); ?>" alt="<?php p($l->t('Saved!')); ?>" />
				<img class="svg action failure hidden" src="<?php p(image_path('core', 'actions/error-color.svg')); ?>" alt="<?php p($l->t('Can not save site')); ?>" />
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
</form>
