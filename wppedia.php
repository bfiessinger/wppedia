<?php

/**
 * WPPedia - The most advanced Glossary solution for WordPress! 
 * 
 * @wordpress-plugin
 * 
 * Plugin Name: WPPedia
 * Description: The most advanced Glossary solution for WordPress!
 * Author: 			Bastian Fießinger & WPPedia Glossary Team
 * AuthorURI: 	https://github.com/bfiessinger/
 * Version: 		1.2.3
 * Text Domain: wppedia
 */

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

/**
 * Core WPPedia functions
 */
require_once plugin_dir_path(__FILE__) . 'core/inc/core-functions.php';

// Core Classes
use WPPedia\template;
use WPPedia\restController;
use WPPedia\WPPediaQuerySetup;
use WPPedia\admin;
use WPPedia\options;
use WPPedia\postMeta;
use WPPedia\customize;
use WPPedia\postType;

// Modules
use WPPedia\modules\crossLinkModule;
use WPPedia\modules\tooltipModule;

// Compatibility
use WPPedia\compatibilities\compatibilityCollection;

class WPPedia {

  /**
   * Static variable for instanciation
   */
  protected static $instance = null;

  /**
   * Get current Instance
   */
  public static function getInstance() {

    if ( null === self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;

  }

  protected function __clone() {}

  protected function __construct() {}

	/**
	 * Define Plugin Constants
	 * 
	 * @since 1.2.0
	 */
	private function define_constants() {

		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$pluginData = array_values(array_filter(get_plugins(), function ($plugins) {
			return ('WPPedia' === $plugins['Name']);
		}))[0];

		wppedia_maybe_define_constant('WPPediaPluginVersion', $pluginData['Version']);

		// Path Constants
		wppedia_maybe_define_constant('WPPediaPluginDir', plugin_dir_path(__FILE__));
		wppedia_maybe_define_constant('WPPediaPluginUrl', plugin_dir_url(__FILE__));
		wppedia_maybe_define_constant('WPPediaPluginBaseName', plugin_basename( __FILE__ ));

		// Env Constants
		wppedia_maybe_define_constant('WPPedia_TEMPLATE_DEBUG_MODE', false);
		
	}

	public function setup() {
		load_plugin_textdomain( 'wppedia', false, dirname( WPPediaPluginBaseName ) . '/languages' );
	}

	public function init() {

		// psr4 Autoloader
		$loader = require "vendor/autoload.php";
		$loader->addPsr4('WPPedia\\', __DIR__);

		$vendor_loader = require "3rd-party/vendor/autoload.php";
		$vendor_loader->add('WPPedia_Vendor\\', __DIR__ . '3rd-party/');

		$this->define_constants();

		add_action( 'after_setup_theme', [ $this, 'setup' ] );

		/**
		 * Instantiate Template Utils
		 */
		template::getInstance();
		new compatibilityCollection();

		/**
		 * Instantiate REST API Controller Class
		 */
		new restController();

		/**
		 * Instantiate Query Controller
		 */
		new WPPediaQuerySetup();

		/**
		 * Instatiate Admin View
		 * Used to edit post or edit views in wp_admin
		 */
		new admin();

		/**
		 * Options
		 * Setup options and settings pages
		 */
		options::getInstance();

		/**
		 * Post meta
		 * Setup custom postmeta for WPPedia articles
		 */
		new postMeta();

		/**
		 * Setup Customizer Controls
		 */
		new customize();

		/**
		 * Instantiate Post Type
		 * Generates the WPPedia Post type and related taxonomies
		 */
		postType::getInstance();

		/**
		 * Modify Wiki Content
		 */
		$crosslinks_active = ( get_option('wppedia_feature_crosslinks', options::get_option_defaults('wppedia_feature_crosslinks')) ) ? true : false;
		$prefer_single_words = ( get_option('wppedia_crosslinks_prefer_single_words', options::get_option_defaults('wppedia_crosslinks_prefer_single_words')) ) ? true : false;

		new crossLinkModule(
			$crosslinks_active,
			$prefer_single_words		
		);

		/**
		 * Tooltips
		 */
		new tooltipModule();

	}

	/**
	 * Get default path for templates in themes.
	 * By default the template path is yourtheme/wppedia
	 * 
	 * If you want to override the default behaviour in your theme use
	 * the filter "wppedia_template_path" and return your preferred folder name
	 * in the callback.
	 * 
	 * @since 1.1.3
	 */
	public function template_path() {
    return trailingslashit(apply_filters( 'wppedia_template_path', 'wppedia' ));
  }

	/**
	 * Get default plugin path
	 * 
	 * @since 1.2.0
	 */
	public function plugin_path() {
		return (defined('WPPediaPluginDir')) ? WPPediaPluginDir : plugin_dir_path(__FILE__);
	}

}

$WPPedia = WPPedia::getInstance();
$WPPedia->init();

/**
 * Template Hooks
 */
require_once WPPediaPluginDir . 'template-hooks/hooks.php';

/**
 * Enqueue Assets
 */
require_once WPPediaPluginDir . 'core/inc/assets.php';

/**
 * Shortcodes
 */
require_once WPPediaPluginDir . 'core/inc/shortcodes.php';

/**
 * The code that runs during plugin activation.
 */
require_once WPPediaPluginDir . 'core/inc/class.activation.php';
register_activation_hook( __FILE__, [ 'WPPedia\\activation', 'activate' ] );

/**
 * Flush rewrite rules if the previously added flag exists,
 * and then remove the flag.
 */
add_action('init', function() {

	if ( get_option( 'wppedia_flush_rewrite_rules_flag' ) ) {
		flush_rewrite_rules();
		delete_option( 'wppedia_flush_rewrite_rules_flag' );
	}

}, 20);

new WPPedia\WPPediaUpgrade();

/**
 * The code that runs during plugin deactivation.
 */
require_once WPPediaPluginDir . 'core/inc/class.deactivation.php';
register_deactivation_hook( __FILE__, [ 'WPPedia\\deactivation', 'deactivate' ] );
