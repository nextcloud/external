// SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
// SPDX-License-Identifier: AGPL-3.0-or-later

import { createAppConfig } from '@nextcloud/vite-config'
import { join } from 'node:path'

const isProduction = process.env.NODE_ENV === 'production'

export default createAppConfig({
	admin: join(import.meta.dirname, 'src', 'admin.ts'),
	'quota-files-sidebar': join(import.meta.dirname, 'src', 'quota-files-sidebar.js'),
}, {
	minify: isProduction,
	extractLicenseInformation: true,
	thirdPartyLicense: false,
	createEmptyCSSEntryPoints: true,
	emptyOutputDirectory: {
		additionalDirectories: ['css'],
	},
})
