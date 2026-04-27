<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
  -->
<template>
	<NcAppNavigationItem
		v-for="site in sites"
		:key="site.href"
		:name="site.name"
		:href="site.href">
		<template #icon>
			<picture class="quota-site-icon">
				<source :srcset="site.imageLight" media="(prefers-color-scheme: dark)">
				<source :srcset="site.imageDark" media="(prefers-color-scheme: light)">
				<img :srcset="site.imageDark" :alt="site.name" class="quota-site-icon">
			</picture>
		</template>
	</NcAppNavigationItem>
</template>

<script setup lang="ts">
import NcAppNavigationItem from '@nextcloud/vue/components/NcAppNavigationItem'

interface QuotaSite {
	name: string
	href: string
	imageLight: string
	imageDark: string
}

defineProps<{
	sites: QuotaSite[]
}>()
</script>

<style scoped>
.quota-site-icon {
	width: 20px;
	height: 20px;
	object-fit: contain;

	img {
		display: block;
	}
}
</style>
