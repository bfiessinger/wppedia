<?php

/**
 * setupMyWP
 *
 * @wordpress-plugin
 * Plugin Name: WP Pedia
 * Description: Simple Glossary for Wordpress
 * Author: Bastian Fießinger
 * AuthorURI: https://github.com/bfiessinger/
 * Version: 1.0.0
 * Text Domain: wppedia
 */

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

// Define Constants
if ( ! defined('wpPediaPluginDir') )
  define('wpPediaPluginDir', plugin_dir_path(__FILE__));

if ( ! defined('wpPediaPluginUrl') )
	define('wpPediaPluginUrl', plugin_dir_url(__FILE__));

// psr4 Autoloader
$loader = require "vendor/autoload.php";
$loader->addPsr4('bf\\wpPedia\\', __DIR__);

/**
 * Instatiate Helper Utils
 * 
 * @since 1.0.0
 */
function wppedia_utils() {

	return bf\wpPedia\helper::getInstance();

}

/**
 * Instantiate Template Utils
 * 
 * @since 1.0.0
 */
function wppedia_template() {

	return bf\wpPedia\template::getInstance();

}
wppedia_template();

/**
 * Instantiate Controller
 * 
 * @since 1.0.0
 */
bf\wpPedia\controller::getInstance();

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
bf\wpPedia\settings::getInstance();

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
$crosslinks_module_active = ( wppedia_utils()->get_option( \bf\wpPedia\settings::$settings_general_page, 'wppedia_crosslinking_active' ) == 'on' ) ? true : false;
$prefer_single_words = ( wppedia_utils()->get_option( \bf\wpPedia\settings::$settings_general_page, 'wppedia_crosslinking_prefer-single-words' ) == 'on' ) ? true : false;

new bf\wpPedia\crosslinks( 
	$crosslinks_module_active,
	$prefer_single_words
);

/**
 * Load Template Tags
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . 'template-tags/template-functions.php';

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

register_activation_hook( __FILE__, 'wppedia_activation_deactivation_hook' );
function wppedia_activation_deactivation_hook() {
	flush_rewrite_rules();
}
