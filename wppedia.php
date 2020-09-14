<?php

/**
 * WPPedia - The most advanced Glossary solution for WordPress! 
 * 
 * @since				1.0.0
 * 
 * @wordpress-plugin
 * 
 * Plugin Name: WPPedia
 * Description: The most advanced Glossary solution for WordPress!
 * Author: 			Bastian Fießinger
 * AuthorURI: 	https://github.com/bfiessinger/
 * Version: 		1.0.0
 * Text Domain: wppedia
 */

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

use bf\wpPedia\template;
use bf\wpPedia\rest;
use bf\wpPedia\query_control;
use bf\wpPedia\admin;
use bf\wpPedia\options;
use bf\wpPedia\post_type;
use bf\wpPedia\helper;
use bf\wpPedia\modules\crosslinks;
use bf\wpPedia\modules\tooltip;

class WPPedia {

	function __construct() {

		// psr4 Autoloader
		$loader = require "vendor/autoload.php";
		$loader->addPsr4('bf\\wpPedia\\', __DIR__);

		$this->define_constants();

		add_action( 'after_setup_theme', [ $this, 'setup' ] );

		$this->init();

	}

	/**
	 * Define Plugin Constants
	 * 
	 * @since 1.0.0
	 */
	private function define_constants() {

		if ( ! defined('wpPediaPluginVersion') )
			define('wpPediaPluginVersion', '1.0.0');

		if ( ! defined('wpPediaPluginDir') )
			define('wpPediaPluginDir', plugin_dir_path(__FILE__));

		if ( ! defined('wpPediaPluginUrl') )
			define('wpPediaPluginUrl', plugin_dir_url(__FILE__));

	}

	public function setup() {

		load_plugin_textdomain( 'wppedia', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	}

	public function init() {

		/**
		 * Instantiate Template Utils
		 * 
		 * @since 1.0.0
		 */
		$template_utils = template::getInstance();
		$template_utils->start();

		/**
		 * Instantiate REST Class
		 * 
		 * @since 1.0.0
		 */
		$rest_utils = rest::getInstance();
		$rest_utils->start();

		/**
		 * Instantiate Query Controller
		 * 
		 * @since 1.0.0
		 */
		query_control::getInstance();

		/**
		 * Instatiate Admin View
		 * Used to edit post or edit views in wp_admin
		 * 
		 * @since 1.0.0
		 */
		admin::getInstance();

		/**
		 * Options
		 * Setup options and settings pages
		 * 
		 * @since 1.0.0
		 */
		options::getInstance();

		/**
		 * Instantiate Post Type
		 * Generates the WPPedia Post type and related taxonomies
		 * 
		 * @since 1.0.0
		 */
		post_type::getInstance();

		/**
		 * Modify Wiki Content
		 * 
		 * @since 1.0.0
		 */
		$crosslinks_module_active = ( helper::getInstance()->get_option( options::$settings_general_page, 'wppedia_crosslinking_active' ) == 'on' ) ? true : false;
		$prefer_single_words = ( helper::getInstance()->get_option( options::$settings_general_page, 'wppedia_crosslinking_prefer-single-words' ) == 'on' ) ? true : false;

		$module_crosslinks = crosslinks::getInstance();
		$module_crosslinks->init(
			$crosslinks_module_active,
			$prefer_single_words		
		);

		/**
		 * Tooltips
		 * 
		 * @since 1.0.0
		 */
		tooltip::getInstance();

	}

}

new WPPedia();

/**
 * Load Template Tags
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'core/inc/tpl-hooks.php';

/**
 * Enqueue Assets
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'core/inc/assets.php';

/**
 * Public functions
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'core/inc/public-functions.php';

/**
 * Shortcodes
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'core/inc/shortcodes.php';

/**
 * The code that runs during plugin activation.
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'core/inc/class.activation.php';
register_activation_hook( __FILE__, [ 'bf\\wpPedia\\activation', 'activate' ] );

/**
 * Flush rewrite rules if the previously added flag exists,
 * and then remove the flag.
 */
add_action( 'init', function() {

	if ( get_option( 'wppedia_flush_rewrite_rules_flag' ) ) {
		flush_rewrite_rules();
		delete_option( 'wppedia_flush_rewrite_rules_flag' );
	}

}, 20 );



/**
 * The code that runs during plugin deactivation.
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'core/inc/class.deactivation.php';
register_deactivation_hook( __FILE__, [ 'bf\\wpPedia\\deactivation', 'deactivate' ] );
