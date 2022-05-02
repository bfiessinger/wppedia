<?php

/**
 * WPPedia admin fields
 *
 * @since 1.3.0
 */

namespace WPPedia\Traits;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) || die();

trait Sanitizes_Data {

	/**
	 * Sanitize boolean values
	 *
	 * @param string $input - input value
	 *
	 * @return boolean
	 *
	 * @since 1.3.0
	 */
	function sanitize_bool($input) {
		return !!$input;
	}

	/**
	 * Sanitize integer values
	 *
	 * @param string $input - input value
	 *
	 * @return integer
	 *
	 * @since 1.3.0
	 */
	function sanitize_int($input) {
		return absint($input);
	}

	/**
	 * Sanitize float values
	 *
	 * @param string $input - input value
	 *
	 * @return float
	 *
	 * @since 1.3.0
	 */
	function sanitize_float($input) {
		return floatval($input);
	}

	/**
	 * Sanitize one dimensional array values
	 *
	 * @param array $input - input value
	 *
	 * @return array
	 *
	 * @since 1.3.0
	 */
	function sanitize_array($input) {
		return array_map('sanitize_text_field', $input);
	}

	/**
	 * Sanitize permalink base option
	 *
	 * @param string $input - input value
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function sanitize_permalink_part( $input ) {
		// Add leading slash to prevent `esc_url_raw` adding a protocol
		$input = '/' . $input;
		// replace all whitespaces with `-`
		$input = preg_replace( '/\s+/', '-', $input );
		$input = esc_url_raw( $input, null );
		// Remove leading slash
		$input = substr($input, 1);

		return $input;
	}
}
