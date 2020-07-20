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

/**
 * Instantiate Controller
 * 
 * @since 1.0.0
 */
$wiki_controller = bf\wpPedia\controller::getInstance();

/**
 * Instatiate Admin View
 * Used for Settings and other Admin Pages
 * 
 * @since 1.0.0
 */
bf\wpPedia\admin::getInstance();

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
new bf\wpPedia\crosslinks();

/**
 * Load Template Tags
 * 
 * @since 1.0.0
 */
require_once wpPediaPluginDir . '/template-tags/template-tags.php';

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
require_once wpPediaPluginDir . '/inc/public-functions.php';

register_activation_hook( __FILE__, 'wppedia_activation_deactivation_hook' );
function wppedia_activation_deactivation_hook() {
	flush_rewrite_rules();
}
