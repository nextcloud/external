// SPDX-FileCopyrightText: 2023 Kate <jld3103yt@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

import { loadState } from '@nextcloud/initial-state'
import { createApp } from 'vue'
import QuotaFilesSidebarEntries from './components/QuotaFilesSidebarEntries.vue'

interface QuotaSite {
	name: string
	href: string
	image: string
}

document.addEventListener('DOMContentLoaded', () => {
	const settingsEl = document.getElementsByClassName('app-navigation-entry__settings')[0]
	if (!settingsEl) {
		return
	}

	const sites = loadState<QuotaSite[]>('external', 'external-quota-sites')

	const container = document.createElement('div')
	container.style.display = 'contents'
	settingsEl.prepend(container)

	createApp(QuotaFilesSidebarEntries, { sites }).mount(container)
})
