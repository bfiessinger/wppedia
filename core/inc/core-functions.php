<?php

/**
 * Public available functions and declarations
 *
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || die();

use WPPedia\Inline_Style_Collector;

require_once __DIR__ . '/template-functions.php';
require_once __DIR__ . '/page-functions.php';
require_once __DIR__ . '/post-functions.php';
require_once __DIR__ . '/utility-functions.php';

/**
 * Common
 */
function WPPedia() {
	return WPPedia::getInstance();
}

/**
 * Return the current version of wpPedia
 *
 * @since 1.2.0
 */
function wppedia_get_version() {
	return WPPediaPluginVersion;
}

/**
 * Add additional inline styles
 *
 * @since 1.2.0
 */
function wppedia_add_inline_style( string $handle, string $stylesheet ) {

	if ( Inline_Style_Collector::getInstance()->add( $handle, $stylesheet ) )
		return true;

	return false;

}

/**
 * Remove a registered inline style
 *
 * @since 1.3.0
 */
function wppedia_remove_inline_style( string $handle ) {

	if ( Inline_Style_Collector::getInstance()->remove( $handle ) )
		return true;

	return false;

}
