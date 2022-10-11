/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 * @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
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

import escapeHTML from 'escape-html'
import { generateUrl, imagePath, generateOcsUrl } from '@nextcloud/router';

/* global _, Handlebars, $ */
(function(OC, OCA, _, Backbone) {
	if (!OCA.External) {
		/**
		 * @namespace
		 */
		OCA.External = {}
	}

	Handlebars.registerHelper('isSelected', function(currentValue, itemValue) {
		return currentValue === itemValue
	})

	Handlebars.registerHelper('join', function(array) {
		if (_.isUndefined(array)) {
			return ''
		}
		return array.join('|')
	})

	Handlebars.registerHelper('getLanguages', function() {
		return OCA.External.App.availableLanguages
	})

	Handlebars.registerHelper('getTypes', function() {
		return OCA.External.App.availableTypes
	})

	Handlebars.registerHelper('getDevices', function() {
		return OCA.External.App.availableDevices
	})

	Handlebars.registerHelper('getIcons', function() {
		return OCA.External.App.availableIcons
	})

	OCA.External.Models = OCA.External.Models || {}

	OCA.External.Models.Site = Backbone.Model.extend({
		defaults: {
			name: '',
			url: '',
			lang: '',
			type: 'link',
			device: '',
			icon: 'external.svg',
			groups: [],
		},

		parse: function(response) {
			if (!_.isUndefined(response.ocs)) {
				// Parse of single response from save/create
				return response.ocs.data
			}

			// Parse of entry from collection data
			return response
		},
	})

	OCA.External.Models.SiteCollection = Backbone.Collection.extend({
		model: OCA.External.Models.Site,
		url: generateOcsUrl('apps/external/api/v1/sites'),

		parse: function(response) {
			return response.ocs.data.sites
		},
	})

	OCA.External.App = {
		/** @property {OCA.External.Models.SiteCollection} _sites  */
		_sites: null,

		$list: null,

		_renderSite: function(data) {
			data.nameTXT = t('external', 'Name')
			data.urlTXT = t('external', 'URL')
			data.languageTXT = t('external', 'Language')
			data.groupsTXT = t('external', 'Groups')
			data.devicesTXT = t('external', 'Devices')
			data.iconTXT = t('external', 'Icon')
			data.positionTXT = t('external', 'Position')
			data.redirectTXT = t('external', 'Redirect')
			data.removeSiteTXT = t('external', 'Remove site')
			data.noEmbedTXT = t('external', 'This site does not allow embedding')
			data.deleteIMG = imagePath('core', 'actions/delete.svg')

			return OCA.External.Templates.site(data)
		},

		init: function() {
			const self = this
			this.$list = $('ul.external_sites')

			$('#add_external_site').click(function(e) {
				e.preventDefault()

				const $el = $(self._renderSite({
					id: 'new-' + Date.now(),
					name: t('external', 'New site'),
					icon: 'external.svg',
					type: 'link',
					lang: '',
					device: '',
				}))
				self._attachEvents($el)
				$el.find('.options').removeClass('hidden')
				self.$list.append($el)
			})

			this.load()
		},

		load: function() {
			const self = this
			$('#loading_sites').removeClass('hidden')
			this.$list.empty()

			this._sites = new OCA.External.Models.SiteCollection()
			this._sites.fetch({
				success: function(_, response) {
					$('#loading_sites').addClass('hidden')

					self.availableIcons = response.ocs.data.icons
					self._buildIconList(response.ocs.data.icons)
					self.availableLanguages = response.ocs.data.languages
					self.availableTypes = response.ocs.data.types
					self.availableDevices = response.ocs.data.devices

					if (response.ocs.data.sites.length === 0) {
						const $el = $(self._renderSite({
							id: 'undefined',
						}))
						self._attachEvents($el)
						self.$list.append($el)
					} else {
						self._render()
					}
				},
			})
		},

		_render: function() {
			const self = this

			_.each(this._sites.models, function(site) {
				const $el = $(self._renderSite(site.attributes))
				self._attachEvents($el)
				if (site.attributes.type === 'guest') {
					$el.find('.site-redirect-box').hide()
				}
				self.$list.append($el)
			})
		},

		_attachEvents: function($site) {
			$site.find('.delete-button').click(_.bind(this._deleteSite, this))
			$site.find('.trigger-save').change(_.bind(this._saveSite, this))
			$site.find('.icon-more').on('click', function() {
				$site.find('.options').toggleClass('hidden')
			})

			const self = this
			const $groupsSelect = $site.find('.site-groups')

			OC.Settings.setupGroupsSelect($groupsSelect)
			$groupsSelect.change(function(e) {
				self._saveSite(e)
			})
		},

		_deleteSite: function(e) {
			e.preventDefault()

			const $site = $(e.target).closest('li')
			const site = this._sites.get($site.data('site-id'))

			$site.removeClass('failure saved').addClass('saving')

			if (!_.isUndefined(site)) {
				site.destroy({
					success: function() {
						$site.remove()
						if (OC.Settings && OC.Settings.Apps) {
							OC.Settings.Apps.rebuildNavigation()
						}
					},
				})
			} else {
				$site.remove()
			}
		},

		_saveSite: function(e) {
			e.preventDefault()

			const self = this
			const $target = $(e.target)
			const $site = $target.closest('li')
			const site = this._sites.get($site.data('site-id'))
			const groups = $site.find('input.site-groups').val()
			const data = {
				name: $site.find('.site-name').val(),
				url: $site.find('.site-url').val(),
				lang: $site.find('.site-lang').val(),
				type: $site.find('.site-type').val(),
				device: $site.find('.site-device').val(),
				redirect: $site.find('.site-redirect').prop('checked') ? 1 : 0,
				groups: groups === '' ? [] : groups.split('|'),
				icon: $site.find('.site-icon').val(),
			}

			$site.removeClass('failure saved').addClass('saving')
			$site.find('.invalid-value').removeClass('invalid-value')
			if (data.type === 'guest') {
				$site.find('.site-redirect-box').hide()
			} else {
				$site.find('.site-redirect-box').show()
			}

			if (!_.isUndefined(site)) {
				site.save(data, {
					success: function() {
						$site.removeClass('saving').addClass('saved')
						$site.find('h3').html(escapeHTML(data.name) + ' <small>' + escapeHTML(data.url) + '</small>')
						$('#appmenu li[data-id="external_index' + site.get('id') + '"] span').text(data.name)
						setTimeout(function() {
							$site.removeClass('saved')
						}, 2500)
						self._rebuildNavigation()
					},
					error: function(model, xhr) {
						if (!_.isUndefined(xhr.responseJSON.ocs.data.field) && xhr.responseJSON.ocs.data.field !== '') {
							$site.find('.site-' + xhr.responseJSON.ocs.data.field).addClass('invalid-value')
						}
						$site.removeClass('saving').addClass('failure')
					},
				})
			} else {
				this._sites.create(data, {
					success: function(site) {
						$site.data('site-id', site.get('id'))
						$site.removeClass('saving').addClass('saved')
						$site.find('h3').html(escapeHTML(data.name) + ' <small>' + escapeHTML(data.url) + '</small>')
						setTimeout(function() {
							$site.removeClass('saved')
						}, 2500)
						self._rebuildNavigation()
					},
					error: function(model, xhr) {
						if (!_.isUndefined(xhr.responseJSON.ocs.data.field) && xhr.responseJSON.ocs.data.field !== '') {
							$site.find('.site-' + xhr.responseJSON.ocs.data.field).addClass('invalid-value')
						}
						$site.removeClass('saving').addClass('failure')
					},
				})
			}
		},

		_buildIconList: function(data) {
			const self = this
			const $table = $('ul.icon-list')
			let lastIcon = ''
			let $lastIcon = null
			const icons = []
			$table.empty()

			_.each(data, function(data) {
				if (data.icon === '') {
					icons.push(data)
					return
				}

				if (lastIcon !== '' && data.name === lastIcon.replace('-dark.', '.')) {
					$lastIcon.find('div.img').prepend($('<img>').attr('src', data.url))
					$lastIcon.find('span.name').prepend(data.name + ' / ')
					$lastIcon.addClass('twin-icons')

					icons.pop()
					icons.push(_.extend(data, {
						name: data.name + ' / ' + lastIcon,
					}))
					return
				}

				data.deleteTXT = t('external', 'Delete icon')

				const $row = $(OCA.External.Templates.icon(data))
				self._attachEventsIcon($row)
				$table.append($row)
				icons.push(data)

				lastIcon = data.name
				$lastIcon = $row
			})

			this.availableIcons = icons
		},

		_attachEventsIcon: function($icon) {
			$icon.find('span.icon-delete').click(_.bind(this._deleteIcon, this))
		},

		_deleteIcon: function(e) {
			const $row = $(e.currentTarget).parents('li')
			const icon = $row.attr('data-icon')

			$.ajax({
				type: 'DELETE',
				url: generateUrl('/apps/external/icons/' + icon),
			}).done(function() {
				$row.slideUp()
				setTimeout(function() {
					$row.remove()
				}, 750)
			})
		},

		_rebuildNavigation: function() {
			OC.Settings.Apps.rebuildNavigation()
		},
	}
})(OC, OCA, _, OC.Backbone)

window.addEventListener('DOMContentLoaded', function() {
	OCA.External.App.init()

	const uploadParamsLogo = {
		pasteZone: null,
		dropZone: null,
		submit: function() {
			OC.msg.startAction('form.uploadButton span.msg', t('external', 'Uploading…'))
			$('label#uploadlogo').removeClass('icon-upload').addClass('icon-loading-small')
		},
		done: function() {
			OCA.External.App.load()
			OC.msg.finishedSuccess('form.uploadButton span.msg', t('external', 'Reloading icon list…'))
			$('label#uploadlogo').addClass('icon-upload').removeClass('icon-loading-small')
		},
		fail: function(e, result) {
			if (_.isUndefined(result.jqXHR.responseJSON.error)) {
				OC.msg.finishedError('form.uploadButton span.msg', t('external', 'Icon could not be uploaded'))
			} else {
				OC.msg.finishedError('form.uploadButton span.msg', result.jqXHR.responseJSON.error)
			}
			$('label#uploadlogo').addClass('icon-upload').removeClass('icon-loading-small')
		},
	}

	$('#uploadicon').fileupload(uploadParamsLogo)
})
