<?php

/**
 * Plugin Activation
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class activation {

	function __construct() {

		register_activation_hook( wpPediaPluginFile, [ $this, 'on_activate' ] );

	}

	public function on_activate() {

		\flush_rewrite_rules();

	}

}