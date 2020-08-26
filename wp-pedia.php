<?php

/**
 * @wordpress-plugin
 * Plugin Name: WPPedia
 * Description: The most advanced Glossary solution for WordPress!
 * Author: Bastian Fießinger
 * AuthorURI: https://github.com/bfiessinger/
 * Version: 1.0.0
 * Text Domain: wppedia
 */

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

// Define Constants
if ( ! defined('wpPediaPluginVersion') )
	define('wpPediaPluginVersion', '1.0.0');

if ( ! defined('wpPediaPluginDir') )
  define('wpPediaPluginDir', plugin_dir_path(__FILE__));

if ( ! defined('wpPediaPluginUrl') )
	define('wpPediaPluginUrl', plugin_dir_url(__FILE__));

// psr4 Autoloader
$loader = require "vendor/autoload.php";
$loader->addPsr4('bf\\wpPedia\\', __DIR__);

/**
 * Instantiate Template Utils
 * 
 * @since 1.0.0
 */
$template_utils = bf\wpPedia\template::getInstance();
$template_utils->start();

/**
 * Instantiate REST Class
 * 
 * @since 1.0.0
 */
$rest_utils = new bf\wpPedia\rest();
$rest_utils->start();

/**
 * Instantiate Query Controller
 * 
 * @since 1.0.0
 */
bf\wpPedia\query_control::getInstance();

/**
 * Instatiate Admin View
 * Used to edit post or edit views in wp_admin
 * 
 * @since 1.0.0
 */
bf\wpPedia\admin::getInstance();

/**
 * Options
 * Setup options and settings pages
 * 
 * @since 1.0.0
 */
bf\wpPedia\options::getInstance();

/**
 * Instantiate Post Type
 * Generates the WPPedia Post type and related taxonomies
 * 
 * @since 1.0.0
 */
bf\wpPedia\post_type::getInstance();

/**
 * Modify Wiki Content
 * 
 * @since 1.0.0
 */
$crosslinks_module_active = ( bf\wpPedia\helper::getInstance()->get_option( bf\wpPedia\options::$settings_general_page, 'wppedia_crosslinking_active' ) == 'on' ) ? true : false;
$prefer_single_words = ( bf\wpPedia\helper::getInstance()->get_option( bf\wpPedia\options::$settings_general_page, 'wppedia_crosslinking_prefer-single-words' ) == 'on' ) ? true : false;

new bf\wpPedia\modules\crosslinks( 
	$crosslinks_module_active,
	$prefer_single_words
);

/**
 * Tooltips
 * 
 * @since 1.0.0
 */
new bf\wpPedia\modules\tooltip();

/**
 * Load Template Tags
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'inc/tpl-hooks.php';

/**
 * Enqueue Assets
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'inc/assets.php';

/**
 * Public functions
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'inc/public-functions.php';

/**
 * Shortcodes
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'inc/shortcodes.php';

/**
 * The code that runs during plugin activation.
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'inc/class.activation.php';
register_activation_hook( __FILE__, [ 'bf\\wpPedia\\activation', 'activate' ] );

/**
 * The code that runs during plugin deactivation.
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'inc/class.deactivation.php';
register_deactivation_hook( __FILE__, [ 'bf\\wpPedia\\deactivation', 'deactivate' ] );
