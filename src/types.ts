// SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
// SPDX-License-Identifier: AGPL-3.0-or-later

export interface Site {
	id: number | null
	name: string
	icon: string
	url: string
	lang: string
	type: string
	device: string
	groups: string[]
	redirect: boolean
}

export interface Device {
	device: string
	name: string
}

export interface Type {
	type: string
	name: string
}

export interface Language {
	code: string
	name: string
}

export interface Icon {
	icon: string
	name: string
	url: string
}

export interface ExternalWebsiteConfig {
	sites: Site[]
	icons: Icon[]
	languages: Language[]
	types: Type[]
	devices: Device[]
}

export interface Group {
	id: string
	displayname: string
	usercount: number
	disabled: number
	canAdd: boolean
	canRemove: boolean
}
