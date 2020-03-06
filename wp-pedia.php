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

namespace bf\wpPedia;

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

// DEV: scss compiler
function dev_compile_scss($inputFile, $outputFile) {

  $scss = new \ScssPhp\ScssPhp\Compiler();
  $scss->setImportPaths( wpPediaPluginDir . '/assets/css/scss/' );
  $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Crunched');

  $input = file_get_contents( $inputFile );
  $compiled = $scss->compile( $input );

  $outputHandle = fopen( $outputFile, 'w' ) or die('unable to open file!');
  fwrite( $outputHandle, $compiled );
  fclose( $outputHandle );

}
dev_compile_scss( 
  wpPediaPluginDir . '/assets/css/scss/admin.scss', // Input 
  wpPediaPluginDir . '/assets/css/admin.css' // Output
);
// DEV: scss compiler END

/**
 * Instatiate Helper Utils
 * 
 * @since 1.0.0
 */
function wiki_utils() {

  return helper::getInstance();

}
wiki_utils();

/**
 * Instantiate Post Type
 * 
 * @since 1.0.0
 */
wikiPostType::getInstance();

/**
 * Instatiate Admin View
 * 
 * @since 1.0.0
 */
adminView::getInstance();

/**
 * Modify Wiki Content
 * 
 * @since 1.0.0
 */
new wikiContent();
