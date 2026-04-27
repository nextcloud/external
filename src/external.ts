// SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
// SPDX-License-Identifier: AGPL-3.0-or-later

import './style.css'

function pageY(elem: HTMLElement): number {
	return elem.offsetParent
		? elem.offsetTop + pageY(elem.offsetParent as HTMLElement)
		: elem.offsetTop
}

function resizeIframe(): void {
	const iframe = document.getElementById('ifm') as HTMLIFrameElement | null
	if (!iframe) {
		return
	}

	let height = document.documentElement.clientHeight
	height -= pageY(iframe)
	height = height < 0 ? 0 : height
	iframe.style.height = height + 'px'
}

function updateHash(iframe: HTMLIFrameElement): void {
	if (!window.location.hash || !iframe.src) {
		return
	}

	const iframeURL = new URL(iframe.src)
	iframeURL.hash = window.location.hash
	iframe.src = iframeURL.toString()
}

document.addEventListener('DOMContentLoaded', () => {
	const iframe = document.getElementById('ifm') as HTMLIFrameElement | null
	if (!iframe) {
		return
	}

	iframe.addEventListener('load', resizeIframe)
	window.addEventListener('resize', resizeIframe)
	resizeIframe()

	if (window.location.hash) {
		updateHash(iframe)
	}

	window.addEventListener('hashchange', () => updateHash(iframe))
})
