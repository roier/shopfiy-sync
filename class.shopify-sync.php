<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

class ShopifySync {
	
  // private static $is_rest_api_call = false;
  private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
    self::$initiated = true;

    // add_action( 'init', [ 'ShopifySync', 'register_post_type' ], 10, 2 );
    self::register_post_type();
		add_action( 'admin_init', [ 'ShopifySync', 'register_settings' ], 10, 2 );
		add_action( 'admin_menu', [ 'ShopifySync', 'register_admin_menu' ], 10, 2 );
    add_action( 'rest_api_init', [ 'ShopifySync', 'register_api_routes' ], 10, 2 );
    add_action( 'admin_enqueue_scripts', [ 'ShopifySync', 'register_js_files' ], 10, 2 );
  }

  public static function register_settings() {
    register_setting( 'shopify-sync-settings', 'shopify_sync_settings' );
  }

  public static function register_admin_menu() {
    add_menu_page(
      'Shopify Sync Settings',
      'Shopify Sync',
      'manage_options',
      'shopify-sync-settings',
      function() {
        if (isset($_GET['settings-updated'])) {
          add_settings_error('shopify_sync_messages', 'shopify_sync_message', __('Settings Saved', 'shopify_sync'), 'updated');
        }
        settings_errors('shopify_sync_messages');

        ?>
          <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
          <form action="options.php" method="post">
            <?php settings_fields( 'shopify-sync-settings' ); ?>
            <div id="shopify-sync-settings"></div>
            <?php submit_button( 'Save Settings' ); ?>
          </form>
        <?php
      }
    );
  }

  public static function register_post_type() {
    $labels = [
      'name' => __('Products', 'shopify-sync'),
      'singular_name' => __('Product', 'shopify-sync'),
      'edit_item' => __('Edit Product', 'shopify-sync'),
      'add_new' => __('New Product', 'shopify-sync'),
      'add_new_item' => __('New Product', 'shopify-sync'),
      'view_item' =>  __('View Product', 'shopify-sync'),
      'view_items' => __('View Products', 'shopify-sync')
    ];
    register_post_type('product', [
      'labels' => $labels,
      'description' => '',
      'public' => true,
      'has_archive' => true,
      'supports' => ['title', 'template', 'editor', 'thumbnail', 'post-formats', 'custom-fields'],
      'taxonomies' => [],
      'show_in_rest' => true,
      'rewrite' => ['slug' => 'products'],
      'menu_icon' => 'dashicons-admin-post',
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true
    ]);
  }

  public static function shopify_sync_products() {
    $base_uri = get_option('shopify_sync_settings')['shopify-url'];
    $client = new GuzzleHttp\Client(['base_uri' => $base_uri]);

    $products = [];
    $productsResponse = [];
    // $page = 12;
    $page = 13;

    do {
      $response = $client->request('GET', 'products.json', [
        'query' => [
          'page'  => $page,
          'limit' => '250'
        ]
      ]);
      $productsResponse = json_decode($response->getBody(), true)['products'];
      $page += 1;
      $products = array_merge($products, $productsResponse);
    } while (count($productsResponse) != 0);

    if ($response->getStatusCode() == 200) {
      return $products;
    }

    return [
      'code' => $response->getStatusCode(),
      'reason' => $response->getReasonPhrase()
    ];
  }

  public static function register_api_routes() {
    register_rest_route('shopify-sync', 'settings', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => function($data) {
        return get_option('shopify_sync_settings');
			},
			'permission_callback' => function() {
				return is_user_logged_in();
			}
		]);

    register_rest_route('shopify-sync', 'sync', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => ['ShopifySync', 'shopify_sync_products'],
			'permission_callback' => function() {
				return is_user_logged_in();
			}
		]);

  }

  public static function register_js_files() {
    if (WP_DEBUG) {
      wp_enqueue_script( 'shopify-wordpress-livereload', 'http://localhost:35729/livereload.js', [], '');
    }
    wp_enqueue_script( 'shopify-wordpress', SHOPIFY_SYNC__PLUGIN_URL . '/build/index.js', ['wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-plugins', 'wp-i18n', 'wp-api-fetch', 'wp-edit-post'], time());
  }

  /**
   * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
   * @static
   */
  public static function plugin_activation() {
    if ( version_compare( $GLOBALS['wp_version'], SHOPIFY_SYNC__MINIMUM_WP_VERSION, '<' ) ) {
      load_plugin_textdomain( 'shopify-sync' );
      // $message = '<strong>'.sprintf(esc_html__( 'Shopify Sync %s requires WordPress %s or higher.' , 'shopify-sync'), SHOPIFY_SYNC_VERSION, SHOPIFY_SYNC__MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 0.0.1 of the Shopify Sync plugin</a>.', 'shopify-sync'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://roier.dev/plugins/shopify-sync/download/');
      // ShopifySync::bail_on_activation( $message );
    } elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
      add_option( 'Activated_ShopifySync', true );
    }
  }

  /**
   * Removes all connection options
   * @static
   */
  public static function plugin_deactivation( ) {
    // self::deactivate_key( self::get_api_key() );
  
    // // Remove any scheduled cron jobs.
    // $akismet_cron_events = array(
    //   'akismet_schedule_cron_recheck',
    //   'akismet_scheduled_delete',
    // );
  
    // foreach ( $akismet_cron_events as $akismet_cron_event ) {
    //   $timestamp = wp_next_scheduled( $akismet_cron_event );
    
    //   if ( $timestamp ) {
    //     wp_unschedule_event( $timestamp, $akismet_cron_event );
    //   }
    // }
  }

}