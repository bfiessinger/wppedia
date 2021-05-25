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
 * Version: 		1.1.5
 * Text Domain: wppedia
 */

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

/**
 * Core WPPedia functions
 */
require_once plugin_dir_path(__FILE__) . 'core/inc/core-functions.php';

use bf\wpPedia\template;
use bf\wpPedia\rest_controller;
use bf\wpPedia\query_control;
use bf\wpPedia\admin;
use bf\wpPedia\options;
use bf\wpPedia\post_meta;
use bf\wpPedia\post_type;
use bf\wpPedia\modules\crosslinks;
use bf\wpPedia\modules\tooltip;

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
	 * @since 1.0.0
	 */
	private function define_constants() {

		wppedia_maybe_define_constant('wpPediaPluginVersion', '1.1.1');

		// Path Constants
		wppedia_maybe_define_constant('wpPediaPluginDir', plugin_dir_path(__FILE__));
		wppedia_maybe_define_constant('wpPediaPluginUrl', plugin_dir_url(__FILE__));
		wppedia_maybe_define_constant('wpPediaPluginBaseName', plugin_basename( __FILE__ ));

		// Env Constants
		wppedia_maybe_define_constant('WPPedia_TEMPLATE_DEBUG_MODE', false);
		
	}

	public function setup() {
		load_plugin_textdomain( 'wppedia', false, dirname( wpPediaPluginBaseName ) . '/languages' );
	}

	public function init() {

		// psr4 Autoloader
		$loader = require "vendor/autoload.php";
		$loader->addPsr4('bf\\wpPedia\\', __DIR__);

		$this->define_constants();

		add_action( 'after_setup_theme', [ $this, 'setup' ] );

		/**
		 * Instantiate Template Utils
		 */
		template::getInstance();

		/**
		 * Instantiate REST API Controller Class
		 */
		new rest_controller();

		/**
		 * Instantiate Query Controller
		 */
		new query_control();

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
		new post_meta();

		/**
		 * Instantiate Post Type
		 * Generates the WPPedia Post type and related taxonomies
		 */
		post_type::getInstance();

		/**
		 * Modify Wiki Content
		 */
		$crosslinks_active = ( get_option('wppedia_feature_crosslinks', options::get_option_defaults('wppedia_feature_crosslinks')) ) ? true : false;
		$prefer_single_words = ( get_option('wppedia_crosslinks_prefer_single_words', options::get_option_defaults('wppedia_crosslinks_prefer_single_words')) ) ? true : false;

		new crosslinks(
			$crosslinks_active,
			$prefer_single_words		
		);

		/**
		 * Tooltips
		 */
		new tooltip();

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
	 * @since 1.0.0
	 */
	public function plugin_path() {
		return (defined('wpPediaPluginDir')) ? wpPediaPluginDir : plugin_dir_path(__FILE__);
	}

}

$WPPedia = WPPedia::getInstance();
$WPPedia->init();

/**
 * Template Hooks
 */
require_once wpPediaPluginDir . 'template-hooks/hooks.php';

/**
 * Enqueue Assets
 */
require_once wpPediaPluginDir . 'core/inc/assets.php';

/**
 * Shortcodes
 */
require_once wpPediaPluginDir . 'core/inc/shortcodes.php';

/**
 * The code that runs during plugin activation.
 */
require_once wpPediaPluginDir . 'core/inc/class.activation.php';
register_activation_hook( __FILE__, [ 'bf\\wpPedia\\activation', 'activate' ] );

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

new bf\wpPedia\WPPedia_Upgrade();

/**
 * The code that runs during plugin deactivation.
 */
require_once wpPediaPluginDir . 'core/inc/class.deactivation.php';
register_deactivation_hook( __FILE__, [ 'bf\\wpPedia\\deactivation', 'deactivate' ] );
