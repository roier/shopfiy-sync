
import { useEffect, useState } from 'react'

import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import apiFetch from '@wordpress/api-fetch'
import { SettingsTable } from '../utils'

/**
 * Returns Shopify Settings
 *
 * @return {?Object} Shopify Settings.
 */
const ShopifySettings = () => {

  const [settings, setSettings] = useState({ shopify_domain: '' })
  const [syncingProducts, setSyncingProducts] = useState(false)

  useEffect(async () => {
    const data = await apiFetch({ path: 'shopify-sync/settings' })
    if (data !== false) {
      setSettings(data)
    }
  }, [])

  const syncProducts = async () => {
    setSyncingProducts(true)
    const products = await apiFetch({path: 'shopify-sync/sync'})
    console.log(products)
    setSyncingProducts(false)
  }

  const fields = [
    {
      key: 'shopify-url',
      label: __('Shopify URL', 'shopify-sync'),
      fieldType: 'text',
      description: __('This is the url which your products will be synced', 'shopify-sync'),
      onChange: (key, value) => {
        setSettings({...settings, [key]: value })
      }
    }
  ]

  return (<>
    <p>{__('This options should not be shared with anyone.', 'shopify-sync')}</p>
    <SettingsTable domain='shopify_sync_settings' fields={fields} settings={settings} />
    {!!settings['shopify-url'] && (<>
      <Button isPrimary onClick={syncProducts} isBusy={syncingProducts} className='button button-primary'>{__('Sync Products', 'shyopify-sync')}</Button>
    </>)}
  </>)

}

export {
  ShopifySettings
}