<?php

/**
 * WPPedia Customizer
 * 
 * @since 1.3.0
 */

namespace WPPedia;


use \WPPedia_Vendor\Kirki;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class customize {

	protected function __clone() {}

  public function __construct() {
		add_action( 'init', [ $this, 'kirki_init' ] );
	}

	/**
	 * Add Customizer Panels
	 * 
	 * @since 1.3.0
	 */
	public function kirki_init() {

	}

}
