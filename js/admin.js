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

/* global OC, OCA, _, Backbone */
(function(OC, OCA, _, Backbone) {
	if (!OCA.External) {
		/**
		 * @namespace
		 */
		OCA.External = {};
	}

	Handlebars.registerHelper('isSelected', function(currentValue, itemValue) {
		return currentValue === itemValue;
	});

	Handlebars.registerHelper('getLanguages', function() {
		return OCA.External.App.availableLanguages;
	});

	Handlebars.registerHelper('getTypes', function() {
		return OCA.External.App.availableTypes;
	});

	Handlebars.registerHelper('getIcons', function() {
		return OCA.External.App.availableIcons;
	});

	OCA.External.Models = OCA.External.Models || {};

	OCA.External.Models.Site = Backbone.Model.extend({
		defaults: {
			name: '',
			url: '',
			lang: '',
			type: 'link',
			icon: 'external.svg'
		},

		parse: function(response) {
			if (!_.isUndefined(response.ocs)) {
				// Parse of single response from save/create
				return response.ocs.data;
			}

			// Parse of entry from collection data
			return response;
		}
	});

	OCA.External.Models.SiteCollection = Backbone.Collection.extend({
		model: OCA.External.Models.Site,
		url: OC.linkToOCS('apps/external/api/v1', 2) + 'sites',

		parse: function(response) {
			return response.ocs.data.sites;
		}
	});

	OCA.External.App = {
		/** @property {OCA.External.Models.SiteCollection} _sites  */
		_sites: null,

		$list: null,

		_compiledTemplate: null,

		init: function() {
			var self = this;
			this.$list = $('ul.external_sites');

			this._sites = new OCA.External.Models.SiteCollection();
			this._sites.fetch({
				success: function(_, response) {
					$('#loading_sites').removeClass('icon-loading-small');
					self.availableIcons = response.ocs.data.icons;
					self.availableLanguages = response.ocs.data.languages;
					self.availableTypes = response.ocs.data.types;

					if (response.ocs.data.sites.length === 0) {
						var $el = $(self._compiledTemplate({
							id: 'undefined'
						}));
						self._attachEvents($el);
						self.$list.append($el);
					} else {
						self._render();
					}
				}
			});

			$('#add_external_site').click(function(e) {
				e.preventDefault();

				var $el = $(self._compiledTemplate({
					id: 'undefined',
					icon: 'external.svg',
					type: 'link'
				}));
				self._attachEvents($el);
				self.$list.append($el);
			});
			this._compiledTemplate = Handlebars.compile($('#site-template').html());
		},

		_render: function() {
			var self = this;

			_.each(this._sites.models, function(site) {
				var $el = $(self._compiledTemplate(site.attributes));
				self._attachEvents($el);
				self.$list.append($el);
			});
		},

		_attachEvents: function($site) {
			$site.find('.delete-button').click(_.bind(this._deleteSite, this));
			$site.find('.trigger-save').change(_.bind(this._saveSite, this));
		},

		_deleteSite: function(e) {
			e.preventDefault();

			var $site = $(e.target).closest('li'),
				site = this._sites.get($site.data('site-id'));

			$site.find('.saving').removeClass('hidden');

			if (!_.isUndefined(site)) {
				site.destroy({
					success: function() {
						$site.remove();
						if(OC.Settings && OC.Settings.Apps) {
							OC.Settings.Apps.rebuildNavigation();
						}
					}
				});
			} else {
				$site.remove();
			}
		},

		_saveSite: function(e) {
			e.preventDefault();

			var $target = $(e.target),
				$site = $target.closest('li'),
				site = this._sites.get($site.data('site-id')),
				data = {
					name: $site.find('.site-name').val(),
					url: $site.find('.site-url').val(),
					lang: $site.find('.site-lang').val(),
					type: $site.find('.site-type').val(),
					icon: $site.find('.site-icon').val()
				};

			$site.find('.failure').addClass('hidden');
			$site.find('.saved').addClass('hidden');
			$site.find('.saving').removeClass('hidden');

			if (!_.isUndefined(site)) {
				site.save(data, {
					success: function() {
						$site.find('.saving').addClass('hidden');
						$site.find('.saved').removeClass('hidden');
						setTimeout(function() {
							$site.find('.saved').addClass('hidden');
						}, 2500);
						if(OC.Settings && OC.Settings.Apps) {
							OC.Settings.Apps.rebuildNavigation();
						}
					},
					error: function() {
						$site.find('.saving').addClass('hidden');
						$site.find('.failure').removeClass('hidden');
					}
				});
			} else {
				this._sites.create(data, {
					success: function(site) {
						$site.data('site-id', site.get('id'));
						$site.find('.saving').addClass('hidden');
						$site.find('.saved').removeClass('hidden');
						setTimeout(function() {
							$site.find('.saved').addClass('hidden');
						}, 2500);
						if(OC.Settings && OC.Settings.Apps) {
							OC.Settings.Apps.rebuildNavigation();
						}
					},
					error: function() {
						$site.find('.saving').addClass('hidden');
						$site.find('.failure').removeClass('hidden');
					}
				});
			}
		}
	}
})(OC, OCA, _, OC.Backbone);

$(document).ready(function(){
	OCA.External.App.init();
});
