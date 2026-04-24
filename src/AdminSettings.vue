<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
  -->
<template>
	<div>
		<NcSettingsSection
			:name="t('external', 'External sites')"
			:description="t('external', 'Add a website directly to the app list in the top bar. This will be visible for all users and is useful to quickly reach other internally used web apps or important sites.')">
			<p class="settings-hint">
				{{ t('external', 'The placeholders {email}, {uid}, {displayname}, {groups}, {language} and {locale} can be used and are filled with the user\'s values to customize the links.') }}
			</p>
			<p class="settings-hint">
				{{ t('external', 'When accessing the external site through the Nextcloud link, path parameters will be forwarded to the external site. So you can also create deep links (e.g. "mycloud.com/external/1/pageA" will lead to Nextcloud with the iframe pointed at "externalsite.com/pageA").') }}
			</p>
			<!-- eslint-disable-next-line vue/no-v-html -->
			<p class="settings-hint" v-html="jwtExplanation" />

			<NcLoadingIcon v-if="loading" :size="44" />

			<ul v-else class="external-sites-list">
				<li
					v-for="(site, index) in sites"
					:key="site.id ?? 'new-' + index"
					class="external-site-row">
					<NcTextField
						v-model="site.name"
						:label="t('external', 'Name')"
						:error="!!site.nameError"
						:helperText="site.nameError"
						class="site-name-field"
						@change="saveSite(site)" />
					<NcTextField
						v-model="site.url"
						:label="t('external', 'URL')"
						:error="!!site.urlError"
						:helperText="site.urlError"
						class="site-url-field"
						@change="saveSite(site)" />
					<NcButton
						:aria-label="t('external', 'Configure site')"
						:title="t('external', 'Configure site')"
						@click="openSiteDialog(site)">
						<template #icon>
							<CogOutline :size="20" />
						</template>
					</NcButton>
					<NcButton
						:aria-label="t('external', 'Remove site')"
						:title="t('external', 'Remove site')"
						variant="error"
						@click="deleteSite(site, index)">
						<template #icon>
							<Delete :size="20" />
						</template>
					</NcButton>
				</li>
			</ul>

			<NcButton @click="addSite">
				{{ t('external', 'New site') }}
			</NcButton>

			<p><em>{{ t('external', 'Please note that some browsers will block displaying of sites via HTTP if you are running HTTPS.') }}</em></p>
			<p><em>{{ t('external', 'Furthermore please note that many sites these days disallow iframing due to security reasons.') }}</em></p>
			<p><em>{{ t('external', 'We highly recommend to test the configured sites above properly.') }}</em></p>
		</NcSettingsSection>

		<NcSettingsSection :name="t('external', 'Icons')">
			<p class="settings-hint">
				{{ t('external', 'If you upload a test.png and a test-dark.png file, both will be used as one icon. The dark version will be used on mobile devices, otherwise the white icon is not visible on the white background in the mobile apps.') }}
				{{ t('external', 'Uploading an icon with the same name will replace the current icon.') }}
			</p>

			<ul class="icon-list">
				<li
					v-for="group in groupedIcons"
					:key="group.groupKey"
					class="icon-row">
					<div class="icon-previews">
						<img
							v-for="icon in group.icons"
							:key="icon.icon"
							:src="icon.url"
							:alt="icon.name"
							class="icon-preview">
					</div>
					<span class="icon-name">{{ group.groupKey }}</span>
					<NcButton
						:aria-label="t('external', 'Delete icon')"
						:title="t('external', 'Delete icon')"
						variant="error"
						@click="removeIconGroup(group)">
						<template #icon>
							<Delete :size="20" />
						</template>
					</NcButton>
				</li>
			</ul>

			<form class="upload-button" @submit.prevent>
				<input
					id="uploadicon"
					class="hidden-upload-input"
					name="uploadicon"
					type="file"
					accept="image/*"
					@change="handleIconUpload">
				<label for="uploadicon">
					<NcButton tag="span" :aria-label="t('external', 'Upload new icon')">
						<template #icon>
							<Upload :size="20" />
						</template>
						{{ t('external', 'Upload new icon') }}
					</NcButton>
				</label>
				<span v-if="uploadMessage" class="upload-message">{{ uploadMessage }}</span>
			</form>
		</NcSettingsSection>

		<!-- Site settings dialog -->
		<ExternalWebsite
			v-if="editingSite"
			:site="editingSite"
			:config="config"
			@save="onDialogSave"
			@close="editingSite = null" />
	</div>
</template>

<script setup lang="ts">
import type { Ref } from 'vue'
import type { ExternalWebsiteConfig, Icon, Site } from './types.ts'

import { t } from '@nextcloud/l10n'
import { computed, onMounted, ref } from 'vue'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'
import NcSettingsSection from '@nextcloud/vue/components/NcSettingsSection'
import NcTextField from '@nextcloud/vue/components/NcTextField'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Upload from 'vue-material-design-icons/Upload.vue'
import ExternalWebsite from './components/ExternalWebsite.vue'
import logger from './logger.ts'
import { createSite, deleteIcon, deleteSite as deleteSiteApi, fetchConfig, updateSite, uploadIcon } from './services/api.ts'
import rebuildNavigation from './services/rebuild-navigation.ts'

interface IconGroup {
	groupKey: string
	icons: Icon[]
}

interface SiteWithErrors extends Site {
	nameError?: string
	urlError?: string
}

const jwtExplanation = t('external', 'A JSON Web Token containing user´s email, uid and display name in its payload can be embedded into the link using the {jwt} placeholder. See the {linkstart}documentation{linkend} how to decode it.', {
	linkstart: '<a target="_blank" class="external" href="https://github.com/nextcloud/external/blob/master/docs/jwt-sample.php" rel="noreferrer nofollow">',
	linkend: '↗</a>',
}, {
	escape: false,
})

const loading = ref(true)
const sites: Ref<SiteWithErrors[]> = ref([])
const availableIcons: Ref<Icon[]> = ref([])

const groupedIcons = computed<IconGroup[]>(() => {
	const groups = new Map<string, Icon[]>()
	for (const icon of availableIcons.value) {
		// Strip -dark suffix to get the group key (e.g. "test-dark.png" → "test.png")
		const groupKey = icon.icon.replace(/-dark(\.[^.]+)$/i, '$1')
		if (!groups.has(groupKey)) {
			groups.set(groupKey, [])
		}
		groups.get(groupKey)!.push(icon)
	}
	return Array.from(groups.entries()).map(([groupKey, icons]) => ({ groupKey, icons }))
})
const editingSite: Ref<SiteWithErrors | null> = ref(null)
const uploadMessage = ref('')

const config: Ref<ExternalWebsiteConfig> = ref({
	devices: [],
	sites: [],
	icons: [],
	languages: [],
	types: [],
})

onMounted(async () => {
	try {
		const data = await fetchConfig()
		config.value = data
		sites.value = data.sites.map((s) => ({ ...s }))
		availableIcons.value = data.icons.filter((i) => i.icon !== '')
	} finally {
		loading.value = false
	}
})

/**
 *
 */
function addSite() {
	sites.value.push({
		id: null,
		name: t('external', 'New site'),
		url: '',
		lang: '',
		type: 'link',
		device: '',
		icon: 'external.svg',
		groups: [],
		redirect: false,
	})
}

/**
 *
 * @param site
 */
async function saveSite(site: SiteWithErrors) {
	site.nameError = undefined
	site.urlError = undefined

	const siteData = {
		name: site.name,
		url: site.url,
		lang: site.lang,
		type: site.type,
		device: site.device,
		icon: site.icon,
		groups: site.groups,
		redirect: site.redirect,
	}

	try {
		if (site.id === null) {
			const created = await createSite(siteData)
			site.id = created.id
		} else {
			await updateSite(site.id, siteData)
		}
		rebuildNavigation()
	} catch (error: unknown) {
		const response = (error as { response?: { data?: { ocs?: { data?: { field?: string, error?: string } } } } })?.response?.data?.ocs?.data
		if (response?.field === 'name') {
			site.nameError = response.error
		} else if (response?.field === 'url') {
			site.urlError = response.error
		}
	}
}

/**
 *
 * @param site
 * @param index
 */
async function deleteSite(site: SiteWithErrors, index: number) {
	if (site.id !== null) {
		await deleteSiteApi(site.id)
		rebuildNavigation()
	}
	sites.value.splice(index, 1)
}

/**
 *
 * @param site
 */
function openSiteDialog(site: SiteWithErrors) {
	editingSite.value = site
}

/**
 *
 * @param updatedSite
 */
async function onDialogSave(updatedSite: Site) {
	const index = sites.value.findIndex((s) => s === editingSite.value)
	if (index !== -1) {
		const site = sites.value[index]
		Object.assign(site, updatedSite)
		await saveSite(site)
	}
	editingSite.value = null
}

/**
 *
 * @param group
 */
async function removeIconGroup(group: IconGroup) {
	for (const icon of group.icons) {
		await deleteIcon(icon.icon)
	}
	const groupIconNames = new Set(group.icons.map((i) => i.icon))
	availableIcons.value = availableIcons.value.filter((i) => !groupIconNames.has(i.icon))
}

/**
 *
 * @param event
 */
async function handleIconUpload(event: Event) {
	const input = event.target as HTMLInputElement
	if (!input.files || input.files.length === 0) {
		return
	}

	uploadMessage.value = t('external', 'Uploading…')
	try {
		await uploadIcon(input.files[0])
		const data = await fetchConfig()
		availableIcons.value = data.icons.filter((i) => i.icon !== '')
		config.value = data
		uploadMessage.value = t('external', 'Icon uploaded successfully')
	} catch (error) {
		uploadMessage.value = t('external', 'Icon could not be uploaded')
		logger.error('Icon could not be uploaded', { error })
	} finally {
		input.value = ''
		setTimeout(() => {
			uploadMessage.value = ''
		}, 3000)
	}
}
</script>

<style scoped lang="scss">
.external-sites-list {
	list-style: none;
	padding: 0;
	margin-block-end: 1rem;
}

.external-site-row {
	display: flex;
	flex-direction: row;
	align-items: flex-end;
	gap: 0.5rem;
	margin-block-end: 0.5rem;
}

.site-name-field {
	flex: 1 1 200px;
}

.site-url-field {
	flex: 2 1 300px;
}

.icon-list {
	list-style: none;
	padding: 0;
	margin-block-end: 1rem;
}

.icon-row {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	margin-block-end: 0.25rem;
}

.icon-previews {
	display: flex;
	gap: 0.25rem;
}

.icon-preview {
	width: 32px;
	height: 32px;
	object-fit: contain;
}

.icon-name {
	flex: 1;
}

.upload-button {
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.hidden-upload-input {
	position: absolute;
	width: 1px;
	height: 1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
}

.upload-message {
	color: var(--color-text-maxcontrast);
}
</style>
