<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
  -->
<template>
	<NcDialog
		:name="t('external', 'Configure site')"
		:open="true"
		@update:open="$emit('close')">
		<template #actions>
			<NcButton @click="$emit('close')">
				{{ t('external', 'Cancel') }}
			</NcButton>
			<NcButton variant="primary" @click="save">
				{{ t('external', 'Save') }}
			</NcButton>
		</template>

		<div class="dialog-content">
			<NcSelect
				v-model="selectedIcon"
				:inputLabel="t('external', 'Icon')"
				:options="iconOptions"
				class="dialog-field" />

			<NcSelect
				v-model="selectedLanguage"
				:inputLabel="t('external', 'Language')"
				:options="languageOptions"
				class="dialog-field" />

			<NcSelect
				v-model="selectedType"
				:inputLabel="t('external', 'Position')"
				:options="typeOptions"
				class="dialog-field" />

			<NcSelect
				v-model="selectedDevice"
				:inputLabel="t('external', 'Devices')"
				:options="deviceOptions"
				class="dialog-field" />

			<NcSettingsSelectGroup
				v-model="selectedGroups"
				:label="t('external', 'Groups')"
				:placeholder="t('external', 'All groups')"
				class="dialog-field" />

			<NcCheckboxRadioSwitch
				v-if="selectedType?.id !== 'guest'"
				v-model="redirect"
				class="dialog-field">
				{{ t('external', 'Redirect') }}
			</NcCheckboxRadioSwitch>
		</div>
	</NcDialog>
</template>

<script setup lang="ts">
import type { Ref } from 'vue'
import type { ExternalWebsiteConfig, Site } from '../types.ts'

import { t } from '@nextcloud/l10n'
import { computed, ref } from 'vue'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcCheckboxRadioSwitch from '@nextcloud/vue/components/NcCheckboxRadioSwitch'
import NcDialog from '@nextcloud/vue/components/NcDialog'
import NcSelect from '@nextcloud/vue/components/NcSelect'
import NcSettingsSelectGroup from '@nextcloud/vue/components/NcSettingsSelectGroup'

interface SelectOption {
	id: string
	label: string
}

interface Props {
	site: Site
	config: ExternalWebsiteConfig
}

const props = defineProps<Props>()
const emit = defineEmits<{
	(e: 'save', site: Site): void
	(e: 'close'): void
}>()

const iconOptions = computed<SelectOption[]>(() => props.config.icons
	.filter((icon) => icon.icon !== '')
	.map((icon) => ({ id: icon.icon, label: icon.name })))

const languageOptions = computed<SelectOption[]>(() => props.config.languages.map((lang) => ({ id: lang.code, label: lang.name })))

const typeOptions = computed<SelectOption[]>(() => props.config.types.map((type) => ({ id: type.type, label: type.name })))

const deviceOptions = computed<SelectOption[]>(() => props.config.devices.map((device) => ({ id: device.device, label: device.name })))

const selectedIcon: Ref<SelectOption | null> = ref(iconOptions.value.find((o) => o.id === props.site.icon) ?? null)

const selectedLanguage: Ref<SelectOption | null> = ref(languageOptions.value.find((o) => o.id === props.site.lang) ?? null)

const selectedType: Ref<SelectOption | null> = ref(typeOptions.value.find((o) => o.id === props.site.type) ?? null)

const selectedDevice: Ref<SelectOption | null> = ref(deviceOptions.value.find((o) => o.id === props.site.device) ?? null)

const selectedGroups: Ref<string[]> = ref([...props.site.groups])

const redirect = ref(props.site.redirect)

/**
 *
 */
function save() {
	emit('save', {
		...props.site,
		icon: selectedIcon.value?.id ?? props.site.icon,
		lang: selectedLanguage.value?.id ?? '',
		type: selectedType.value?.id ?? props.site.type,
		device: selectedDevice.value?.id ?? '',
		groups: selectedGroups.value,
		redirect: redirect.value,
	})
}
</script>

<style scoped lang="scss">
.dialog-content {
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
	padding: 0.5rem 0;
}

.dialog-field {
	width: 100%;
}
</style>
