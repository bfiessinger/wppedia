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
	 * @since 1.4.0
	 */
    function sanitize_array($input) {
        $input = $this->to_array($input);
        foreach ($input as $key => $value) {
            $input[$key] = $this->sanitize_array_value($value);
        }
        return $input;
    }

    /**
     * Convert input to array
     *
     * @param mixed $input - input value
     *
     * @return array
     *
     * @since 1.4.0
     */
    private function to_array($input) {
        if (is_array($input)) {
            return $input;
        }

        $unserialized = @unserialize($input);
        if (is_array($unserialized)) {
            return $unserialized;
        }

        $jsonDecoded = json_decode($input, true);
        if (is_array($jsonDecoded)) {
            return $jsonDecoded;
        }

        return explode(',', $input);
    }

    /**
     * Sanitize single value of an array
     *
     * @param mixed $value - input value
     *
     * @return mixed
     *
     * @since 1.4.0
     */
    private function sanitize_array_value($value) {
        if (is_array($value)) {
            return $this->sanitize_array($value);
        } elseif (is_bool($value)) {
            return $this->sanitize_bool($value);
        } elseif (is_int($value)) {
            return $this->sanitize_int($value);
        } elseif (is_float($value)) {
            return $this->sanitize_float($value);
        } elseif (is_null($value)) {
            return null;
        } else {
            return sanitize_text_field($value);
        }
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
