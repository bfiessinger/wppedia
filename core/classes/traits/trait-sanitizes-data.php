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
		$orig_input = $input;

		/**
		 * try to make input an array if it is not
		 * use unserialize, if it fails, then use json_decode and finally explode
		 */
		if ( ! is_array( $input ) ) {
			$input = @unserialize( $orig_input );
			if ( ! is_array( $input ) ) {
				$input = json_decode( $orig_input, true );
				if ( ! is_array( $input ) ) {
					$input = explode( ',', $orig_input );
				}
			}
		}

		/**
		 * Loop over the array and sanitize values based on
		 * data type
		 */
		foreach ( $input as $key => $value ) {

			/**
			 * If the value is an array, then recursively call this function
			 * to sanitize the values
			 */
			if ( is_array( $value ) ) {
				$input[ $key ] = $this->sanitize_array( $value );
			} else if ( is_bool( $value ) ) {
				$input[ $key ] = $this->sanitize_bool( $value );
			} else if ( is_int( $value ) ) {
				$input[ $key ] = $this->sanitize_int( $value );
			} else if ( is_float( $value ) ) {
				$input[ $key ] = $this->sanitize_float( $value );
			} else if ( is_null( $value ) ) {
				$input[ $key ] = null;
			} else {
				$input[ $key ] = sanitize_text_field( $value );
			}

		}

		return $input;
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
