<?php

/**
 * Plugin Deactivation
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class deactivation {

	function __construct() {

		register_deactivation_hook( wpPediaPluginFile, [ $this, 'on_deactivate' ] );

	}

	public function on_deactivate() {

		\flush_rewrite_rules();

	}

}