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
	 * Return the current version of wpPedia
	 * 
	 * @since 1.0.0
	 */
	public function get_version() {
		return wpPediaPluginVersion;
	}

  /**
   * Get Wiki Entries
   * 
   * @since 1.0.0
   * 
   * @return WP_Query WordPress Query Result
   */
  public function get_wiki_entries(array $query_args = []) {

    $query_defaults = [
      'post_type' => 'wppedia_term',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC'
    ];

    $query_args = array_merge($query_defaults, $query_args);

    $wiki_entries = new \WP_Query($query_args);

    return $wiki_entries;

	}
	
	/**
	 * Get Wiki Entry titles
	 * 
	 * @since 1.0.0
	 * 
	 * @return array Array with post titles
	 */
	public function get_wiki_entry_searchables( array $query_args = [] ) {

		$the_query = $this->get_wiki_entries( $query_args );

		if ( ! $the_query->have_posts() )
			return null;

		$title_array = [];
		while ( $the_query->have_posts() ) {

			$the_query->the_post();
			$title_array[] = [
				'post_id'			=> get_the_ID(),
				'post_title'	=> get_the_title(),
				'url'					=> get_permalink(),
				'tags'				=> []
			];

		}

		return $title_array;

	}

  /**
   * Lists all available initial letters
   * 
   * @since 1.0.0
   */
  public function list_initial_letters() {

    $initials = [
      // Default letters
			'a' => 'a',
			'b' => 'b',
			'c' => 'c', 
			'd' => 'd', 
			'e' => 'e', 
			'f' => 'f', 
			'g' => 'g', 
			'h' => 'h', 
			'i' => 'i', 
			'j' => 'j', 
			'k' => 'k', 
			'l' => 'l', 
			'm' => 'm', 
			'n' => 'n', 
			'o' => 'o', 
			'p' => 'p', 
			'q' => 'q', 
			'r' => 'r', 
			's' => 's', 
			't' => 't', 
			'u' => 'u', 
			'v' => 'v', 
			'w' => 'w', 
			'x' => 'x', 
			'y' => 'y', 
			'z' => 'z'
    ];

    return $initials;

  }

	/**
	 * Get the initial letter from an post
	 * 
	 * @param int|WP_POST $post
	 * 
	 * @return string the first character
	 */
	function post_initial_letter( $post ) {

		$post_initial_letter = \strtolower( substr( get_the_title( $post ), 0, 1 ) );

		if ( $this->use_special_chars !== true && ! in_array( $post_initial_letter, $this->list_initial_letters() )  ) {
			$post_initial_letter = '#';
		}

		return strtolower( $post_initial_letter );

	}

	/**
	 * Generate a valid slug from a string
	 * 
	 * @param string $str 
	 * 
	 * @return string - a valid slug
	 */
	function slugify( string $str ) {

		// replace non letter or digits by -
		$str = preg_replace( '~[^\pL\d]+~u', '-', $str );

		// transliterate
		$str = iconv( 'utf-8', 'us-ascii//TRANSLIT', $str );

		// remove unwanted characters
		$str = preg_replace( '~[^-\w]+~', '', $str );

		// trim
		$str = trim( $str, '-' );

		// remove duplicate -
		$str = preg_replace( '~-+~', '-', $str );

		// lowercase
		$str = strtolower( $str );

		if ( empty( $str ) ) {
			return _x( 'other', 'wppedia slugs', 'wppedia' );
		}

		// run a last urlencode to be sure to get a valid slug
		$str = \rawurlencode( $str );

		return $str;

	}

  /**
   * Get initial letters of current Glossary Entries
   * 
   * @param array $args Arguments to modify the output of initial letters
   * 
   * @since 1.0.0
   */
  public function get_wiki_initial_letters(array $args = []) {

		// Default settings array
    $defaults = [
			'show_option_home' => false,
			'hide_empty' => true
		];

		// Build final settings array
    $settings = array_merge($defaults, $args);

		$available_initial_chars = [];

		// Get available initial char terms
		$initial_char_terms = get_terms( [
			'taxonomy' => 'wppedia_initial_letter',
			'hide_empty' => $settings['hide_empty'],
		] );

		// Loop over all available terms and get their slugs and names
		foreach ( $initial_char_terms as $wp_term ) {
			$available_initial_chars[$wp_term->slug] = $wp_term->name;
		}

		$available_initial_chars = array_unique( $available_initial_chars );

		if ( false !== $settings['hide_empty'] )
			return $available_initial_chars;

		$available_initial_chars = array_unique( array_replace( $available_initial_chars, $this->list_initial_letters() ) );

		// Sort Array and keep indexes
		asort( $available_initial_chars );

		// Add all option after sorting
		if ( false !== $settings['show_option_home'] )
			$available_initial_chars = array_merge( ['home' => 'home'], $available_initial_chars );

		return $available_initial_chars;

	}

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
	 * Determine if a static page is used for the wpPedia front page
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean|int Returns false if a cpt archive is used or the post ID of the static page
	 */
	public function has_static_archive_page() {

		if ( FALSE === $this->get_option( options::$settings_general_page, 'wppedia_archive_page' ) ) {
			return false;
		}

		return $this->get_option( options::$settings_general_page, 'wppedia_archive_page' );

	}
	
	/**
	 * Determine if the currently viewed page is a wiki page
	 * 
	 * @since 1.0.0
	 */
	public function is_wiki_post_type( $query = false ) {

		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}

		/**
		 * No Checks should be performed if the request is 404
		 * or we are not on the main query
		 */
		if ( is_404() || ! $query->is_main_query() )
			return false;

		$post_type = post_type::getInstance()->post_type;
		$is_wppedia_post_type = false;

		global $wp;

		if ( 
			/**
			 * Check for singular and archive pages where there is only
			 * one given post type
			 */
			(
				! $query->is_search() &&
				get_post_type() == $post_type
			) ||
			/**
			 * Check for searches in the archive
			 */
			(
				$query->is_post_type_archive() &&
				rtrim( home_url( $wp->request ), '/' ) == rtrim( get_post_type_archive_link( $post_type ), '/' )
			) ||
			/**
			 * Check for requests to the custom selected static WPPedia front page
			 */
			get_the_ID() === intval( $this->has_static_archive_page() )
		)
			$is_wppedia_post_type = $post_type;
			
		return $is_wppedia_post_type;

	}

	/**
	 * Determine if the current view is a wiki search
	 * 
	 * @uses is_wiki_post_type()
	 * 
	 * @since 1.0.0
	 */
	public function is_wiki_search( $query = false ) {

		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}

		if ( $query->is_search() && $this->is_wiki_post_type( $query ) )
			return true;
		
		return false;

	}

	/**
	 * Determine if the currently viewed page is the wiki homepage
	 * 
	 * @since 1.0.0
	 */
	public function is_wiki_home() {

		if ( is_post_type_archive( 'wppedia_term' ) || get_the_ID() === intval( $this->has_static_archive_page() ) )
			return true;

		return false;

	}

	/**
	 * Get Wiki URL
	 * 
	 * @param array $query_args array with query args to add
	 * 
	 * @return string final URL
	 */
	public function get_wiki_url( array $query_args = [] ) {

		$archive_url;
		if ( FALSE === $this->has_static_archive_page() )
			$archive_url = get_post_type_archive_link('wppedia_term');
		else
			$archive_url = get_permalink( $this->has_static_archive_page() );

		if ( ! empty( $query_args ) )
			$archive_url = add_query_arg( $query_args, $archive_url );

		return $archive_url;

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
