<?php

/**
 * WP Wiki Helpers
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class helper {

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

  protected function __construct() {
  
    add_filter( 'posts_where', [ $this, 'get_posts_by_initial_letter' ], 10, 2 );
    
  }

  /**
   * Get a specific View
   * 
   * @since 1.0.0
   */
  public function get_view(string $view, array $args = [], bool $display = true) {

    $view_file = wpPediaPluginDir . '/views/view-' . $view . '.php';

    if ( file_exists( $view_file ) ) {

			if ( $display )
				require_once $view_file;
			else
				return $view_file;

		}

		return false;

  }

  /**
   * Get a partial view
   * 
   * @since 1.0.0
   */
  public function get_partial(string $partial, array $args = [], bool $display = true) {

    $partial_file = wpPediaPluginDir . '/partials/partial-' . $partial . '.php';

    if ( file_exists( $partial_file ) ) {

			if ( $display )
				require_once $partial_file;
			else
				return $partial_file;

		}
			
		return false;

  }
  
  /**
   * Find posts beginning with a specific letter
   * 
   * @since 1.0.0 
   */
  function get_posts_by_initial_letter( $where, \WP_Query $query ) {

    $initial_letter = $query->get( 'initial_letter' );

    if ( $initial_letter ) {

      global $wpdb;

      $where .= " AND $wpdb->posts.post_title LIKE '$initial_letter%'";

    }

    return $where;

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
      'post_type' => 'wp_pedia_term',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC'
    ];

    $query_args = array_merge($query_defaults, $query_args);

    $wiki_entries = new \WP_Query($query_args);

    return $wiki_entries;

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

		return strtolower( substr( get_the_title( $post ), 0, 1 ) );

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
      'hide_empty' => true
		];

		// Build final settings array
    $settings = array_merge($defaults, $args);

		$available_initial_chars = [];

		// Get available initial char terms
		$initial_char_terms = get_terms( [
			'taxonomy' => 'initialcharacter',
			'hide_empty' => $settings['hide_empty'],
		] );

		// Loop over all available terms and get their slugs and names
		foreach ( $initial_char_terms as $wp_term ) {
			$available_initial_chars[$wp_term->slug] = $wp_term->name;
		}

		$available_initial_chars = array_unique( $available_initial_chars );

		if ( $settings['hide_empty'] !== false )
			return $available_initial_chars;

		$available_initial_chars = array_unique( array_replace( $available_initial_chars, $this->list_initial_letters() ) );

		// Sort Array and keep indexes
		asort( $available_initial_chars );

		return $available_initial_chars;

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
  public function wp_query_all_initial_letters(array $args = [], int $letter = null) {

    $found_posts = [];

    if ( ! $letter )
      $letterSet = $this->get_wiki_initial_letters();
    else
      $letterSet = [$letter];

    foreach( $letterSet as $initial_letter ) {

      $query_by_initial_letter = $this->get_wiki_entries(['initial_letter' => $initial_letter]);

      foreach ( $query_by_initial_letter->posts as $post ) {
        
        $found_posts[] = $post->ID;

      }

    }

    // Build the final Query based on found post ID's
    $final_query_args = [
      'post_type'       => 'wp_pedia_term',
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
	 * Determine if the currently viewed page is a wiki page
	 * 
	 * @since 1.0.0
	 */
	public function is_wiki_post_type() {

		$post_type = false;

		if ( get_post_type() == 'wp_pedia_term' )
			$post_type = 'wp_pedia_term';
			
		return $post_type;

	}

	/**
	 * Get Wiki URL
	 * 
	 * @param array $query_args array with query args to add
	 * 
	 * @return string final URL
	 */
	public function get_wiki_url( array $query_args = [] ) {

		$archive_url = get_post_type_archive_link('wp_pedia_term');

		if ( ! empty( $query_args ) )
			$archive_url = add_query_arg( $query_args, $archive_url );

		return $archive_url;

	}

}
