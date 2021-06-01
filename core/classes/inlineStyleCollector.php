<?php

/**
 * Style loader Class
 * Used to collect all stylesheets and merging them to inline styles
 * 
 * @since 1.2.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class inlineStyleCollector {

	public $stylesheets = [];
	public $final_css = '';

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
  public static function getInstance() {

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
	 * @return boolean - returns true if styles where merged, false otherwhise
	 * 
	 * @since 1.2.0
	 */
	public function add( string $handle, string $stylesheet ) {

		$css_string = '';

		if ( is_file( $stylesheet ) )
			$css_string = \file_get_contents( $stylesheet );
		else
			$css_string = $stylesheet;

		if ( $css_string !== '' ) {

			/**
			 * When loading CSS files inline their root is relative to the current page
			 * so the browser will try to load background images from the wrong root
			 */
			$css_file_url_regex = '/url\s*\((?:\'|")?(.*?)(?:\'|")?\)/mi';
			if ( preg_match($css_file_url_regex, $css_string, $matches, PREG_OFFSET_CAPTURE, 0) ) {
				$css_string = preg_replace( $css_file_url_regex, 'url(\'' . WPPediaPluginUrl . 'dist/css/$1\')', $css_string );
			}

			$this->collect_inline_styles(  $handle, $css_string );

			return true;

		}

		return false;

	}

	/** 
	 * Remove a registered Stylesheet
	 * 
	 * @param string $handle - The registered handle
	 * 
	 * @since 1.0.0
	 */
	public function remove( string $handle ) {

		unset( $this->stylesheets[ $handle ] );
		$this->stylesheets = self::$stylesheets;

	}

	/**
	 * Collect stylesheets and save them to a public Array
	 * 
	 * @param string $handle
	 * @param string $css
	 * 
	 * @return array - associative array with handle and CSS
	 * 
	 * @since 1.0.0
	 */
	private function collect_inline_styles( string $handle, string $css ) {

		$this->stylesheets[ $handle ] = $css;

	}

	/**
	 * Merge stylesheets after all modifications
	 * 
	 * @return string final CSS string
	 * 
	 * @since 1.0.0
	 */
	private function merge_inline_styles() {

		$css_string = '';

		foreach ( $this->stylesheets as $handle => $css ) {
			$css_string .= $css;
		}

		$this->final_css = $css_string;
	}


	public function get_final_css() {

		$this->merge_inline_styles();

		return $this->final_css;

	}

}
