<?php

/**
 * WP Wiki Helpers
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\post_type;
use bf\wpPedia\options;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class helper {

	/**
	 * Use all special chars or turn them into hashtags
	 */
	protected $use_special_chars = false;

  /**
   * Static variable for instanciation
   */
  protected static $instance = null;

  /**
   * Get current Instance
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
	 * Get initial letters available for the current view
	 * 
	 * @since 1.0.0
	 */
	function get_current_initial_letters() {

		$show = $this->get_wiki_initial_letters();

		if ( is_tax('wppedia_initial_letter') ) {

			$show = [
				get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') )->name
			];
			
		}

		return $show;

	}

  /**
   * Query by initial letters
   * 
   * @since 1.0.0
   * 
   * @param array $args WP_Query args
   * @param int $letter query posts by one or mulitple initial letters
   * 
   * @return WP_Query custom query based on initial letters
   */
  public function wp_query_all_initial_letters(array $args = [], string $letter = null) {

    $found_posts = [];

    if ( ! $letter )
      $letterSet = $this->get_wiki_initial_letters();
    else
      $letterSet = [$letter];

    foreach( $letterSet as $initial_letter ) {

      $query_by_initial_letter = $this->get_wiki_entries(['wppedia_query_initial_letter' => $initial_letter]);

      foreach ( $query_by_initial_letter->posts as $post ) {
        
        $found_posts[] = $post->ID;

      }

    }

    // Build the final Query based on found post ID's
    $final_query_args = [
      'post_type'       => 'wppedia_term',
      'post__in'        => $found_posts,
      'posts_per_page'  => 20,
      'orderby'         => 'post__in'
    ];

    // Manipulate the Query through user input
    $final_query_args = array_merge( $final_query_args, $args );

    $final_query = new \WP_Query( $final_query_args );

    return $final_query;

	}

	/**
	 * Wrapper function around cmb2_get_option
	 * @since  0.1.0
	 * @param  string $key     Options array key
	 * @param  mixed  $default Optional default value
	 * @return mixed           Option value
	 */
	function get_option( $option_key = '', $field_key = '', $default = false ) {

		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return \cmb2_get_option( $option_key, $field_key, $default );
		}

		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = \get_option( $option_key, $default );

		$val = $default;

		if ( 'all' == $field_key ) {
			$val = $opts;
		} elseif ( is_array( $opts ) && array_key_exists( $field_key, $opts ) && false !== $opts[ $field_key ] ) {
			$val = $opts[ $field_key ];
		}

		return $val;

	}

}
