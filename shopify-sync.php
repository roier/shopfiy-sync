<?php
/**
 * @package Shopify Sync
 */
/*
Plugin Name: Shopify Sync
Plugin URI: https://roier.dev/
Description: Used by millions.
Version: 0.0.1
Author: Roier
Author URI: https://roier.dev/wordpress-plugins/
License: GPLv2 or later
Text Domain: shopify-sync
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'SHOPIFY_SYNC_VERSION', '4.1.7' );
define( 'SHOPIFY_SYNC__MINIMUM_WP_VERSION', '4.0' );
define( 'SHOPIFY_SYNC__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHOPIFY_SYNC__PLUGIN_URL', plugins_url() . '/shopify-sync' );

register_activation_hook( __FILE__, array( 'ShopifySync', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'ShopifySync', 'plugin_deactivation' ) );

require_once( SHOPIFY_SYNC__PLUGIN_DIR . 'class.shopify-sync.php' );
// require_once( SHOPIFY_SYNC__PLUGIN_DIR . 'class.shopify-sync-widget.php' );
// require_once( SHOPIFY_SYNC__PLUGIN_DIR . 'class.shopify-sync-rest-api.php' );

add_action( 'init', array( 'ShopifySync', 'init' ) );

// add_action( 'rest_api_init', array( 'Akismet_REST_API', 'init' ) );

// if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
// 	require_once( AKISMET__PLUGIN_DIR . 'class.akismet-admin.php' );
// 	add_action( 'init', array( 'Akismet_Admin', 'init' ) );
// }

// //add wrapper class around deprecated akismet functions that are referenced elsewhere
// require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );

// if ( defined( 'WP_CLI' ) && WP_CLI ) {
// 	require_once( AKISMET__PLUGIN_DIR . 'class.akismet-cli.php' );
// }