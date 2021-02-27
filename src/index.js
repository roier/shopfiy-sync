const { render } = wp.element

import { ShopifySettings } from './components/ShopifySettings'

document.addEventListener('DOMContentLoaded', () => {
  render(<ShopifySettings />, document.getElementById('shopify-sync-settings'))
})


export default {}