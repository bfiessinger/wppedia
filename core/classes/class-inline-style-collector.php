<?php

/**
 * Style loader Class
 * Used to collect all stylesheets and merging them to inline styles
 *
 * @since 1.3.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class Inline_Style_Collector {

	public $stylesheets = [];
	public $removed_stylesheets = [];
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
	 * Remove a registered Stylesheet
	 *
	 * @param string $handle - The registered handle
	 *
	 * @return true
	 *
	 * @since 1.3.0
	 */
	public function remove( string $handle ) {

		$this->removed_stylesheets[] = $handle;
		return true;

	}

	/**
	 * Handle removal of stylesheets
	 *
	 * @since 1.3.0
	 */
	private function handle_remove_stylesheets() {

		foreach ( $this->removed_stylesheets as $handle ) {
			unset( $this->stylesheets[ $handle ] );
		}

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

	/**
	 * Return the final CSS string
	 *
	 * @return string
	 *
	 * @since 1.3.0
	 */
	public function get_final_css() {

		$this->handle_remove_stylesheets();
		$this->merge_inline_styles();

		return $this->final_css;

	}

}
