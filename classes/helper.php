<?php

/**
 * WP Wiki Main Class
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
  public function get_view(string $view, array $args = []) {

    $view_file = wpPediaPluginDir . '/views/view-' . $view . '.php';

    if ( file_exists( $view_file ) )
      require_once $view_file;

  }

  /**
   * Get a partial view
   * 
   * @since 1.0.0
   */
  public function get_partial(string $partial, array $args = []) {

    $partial_file = wpPediaPluginDir . '/partials/partial-' . $partial . '.php';

    if ( file_exists( $partial_file ) )
      require_once $partial_file;

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
      'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
      // Numbers
      '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];

    return $initials;

  }

  /**
   * Get initial letters of current Glossary Entries
   * 
   * @param array $args Arguments to modify the output of initial letters
   * 
   * @since 1.0.0
   */
  public function get_wiki_initial_letters(array $args = []) {

    $defaults = [
      'hide_empty' => true
    ];

    $settings = array_merge($defaults, $args);

    if ( $settings['hide_empty'] !== false ) {

      $available_initial_letters = [];
      $entries_query = $this->get_wiki_entries();

      if ( $entries_query->have_posts() ) {

        while( $entries_query->have_posts() ) {

          $entries_query->the_post();
          $available_initial_letters[] = strtolower( substr( get_the_title(), 0, 1 ) );

        }

      }

      return array_unique( $available_initial_letters );

    }

    return $this->list_initial_letters();

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

}