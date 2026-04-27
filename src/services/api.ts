// SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
// SPDX-License-Identifier: AGPL-3.0-or-later

import type { ExternalWebsiteConfig, Site } from '../types.ts'

import axios from '@nextcloud/axios'
import { t } from '@nextcloud/l10n'
import { generateOcsUrl, generateUrl } from '@nextcloud/router'
import logger from '../logger.ts'

/**
 *
 */
export async function fetchConfig(): Promise<ExternalWebsiteConfig> {
	try {
		const { data } = await axios.get(generateOcsUrl('/apps/external/api/v1/sites'))
		const config: ExternalWebsiteConfig = data.ocs?.data
		return config
	} catch (error) {
		logger.error(t('external', 'Failed to load admin config'), { error })
		throw new Error(t('external', 'Failed to load admin config'), {
			cause: error,
		})
	}
}

export type SiteFormData = Omit<Site, 'id'> & { redirect: boolean }

/**
 *
 * @param siteData Data
 */
export async function createSite(siteData: SiteFormData): Promise<Site> {
	const { data } = await axios.post(generateOcsUrl('/apps/external/api/v1/sites'), {
		...siteData,
		redirect: siteData.redirect ? 1 : 0,
	})
	return data.ocs.data
}

/**
 *
 * @param id Id of the external site
 * @param siteData Data
 */
export async function updateSite(id: number, siteData: SiteFormData): Promise<Site> {
	const { data } = await axios.put(generateOcsUrl('/apps/external/api/v1/sites/{id}', { id }), {
		...siteData,
		redirect: siteData.redirect ? 1 : 0,
	})
	return data.ocs.data
}

/**
 * @param id Id of the external site
 */
export async function deleteSite(id: number): Promise<void> {
	await axios.delete(generateOcsUrl('/apps/external/api/v1/sites/{id}', { id }))
}

/**
 *
 * @param file file to upload
 */
export async function uploadIcon(file: File): Promise<void> {
	const formData = new FormData()
	formData.append('uploadicon', file)
	await axios.post(generateUrl('/apps/external/icons'), formData, {
		headers: { 'Content-Type': 'multipart/form-data' },
	})
}

/**
 *
 * @param icon icon name to delete
 */
export async function deleteIcon(icon: string): Promise<void> {
	await axios.delete(generateUrl('/apps/external/icons/{icon}', { icon }))
}
