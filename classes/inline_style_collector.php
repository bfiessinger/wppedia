<?php

/**
 * Style loader Class
 * Used to collect all stylesheets and merging them to inline styles
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class inline_style_collector {

	public static $final_css = '';

  /**
   * Static variable for instanciation
   */
  protected static $instance = null;

  /**
   * Get current Instance
	 * 
	 * @since 1.0.0
	 * 
	 * @return self
   */
  public static function getInstance( string $stylesheet = null ) {

		if ( null !== $stylesheet )
			self::add( $stylesheet );

    if ( null === self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;

	}
	
  protected function __clone() {}

	protected function __construct() {}

	/**
	 * Register a new Stylesheet
	 * 
	 * @param string $handle - CSS handle
	 * @param string $data - CSS File or String to register
	 * 
	 * @since 1.0.0
	 */
	private static function add( string $stylesheet ) {

		$css_string = '';

		if ( is_file( $stylesheet ) )
			$css_string = \file_get_contents( $stylesheet );
		else
			$css_string = $stylesheet;

		if ( $css_string !== '' )
			self::merge_inline_styles( $css_string );

	}

	/**
	 * Collect stylesheets for merging on the current view
	 * 
	 * @param string $handle - CSS handle
	 * @param string $css_string - string to add
	 * 
	 * @since 1.0.0
	 */
	private static function merge_inline_styles( string $css_string ) {
		self::$final_css .= $css_string;
	}

	public function get_final_css() {
		return self::$final_css;
	}

}