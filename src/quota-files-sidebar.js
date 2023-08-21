import {loadState} from '@nextcloud/initial-state'

document.addEventListener('DOMContentLoaded', () => {
	const state = loadState('external', 'external-quota-sites')

	for (const site of state) {
		const image = document.createElement('img')
		image.src = site.image

		const icon = document.createElement('div')
		icon.classList.add('app-navigation-entry-icon')
		icon.append(image)

		const name = document.createElement('span')
		name.classList.add('app-navigation-entry__name')
		name.innerText = site.name

		const a = document.createElement('a')
		a.classList.add('app-navigation-entry-link')
		a.href = site.href
		a.append(icon)
		a.append(name)

		const div = document.createElement('div')
		div.classList.add('app-navigation-entry')
		div.append(a)

		const li = document.createElement('li')
		li.append(div)

		document.getElementsByClassName('app-navigation-entry__settings')[0].prepend(li)
	}
})
