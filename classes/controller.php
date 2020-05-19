<?php

/**
 * WP Wiki Controller
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class controller {

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

  protected function __construct() {

		// Include Custom Templates
		add_filter( 'template_include', [ $this, 'template_include' ], 10 );

		// Modify the default query output
		add_filter( 'posts_join', [ $this, 'posts_join_get_posts_by_initial_letter' ], 10, 2 );
		add_filter( 'posts_where', [ $this, 'posts_where_get_posts_by_initial_letter' ], 10, 2 );

	}

	/**
	 * posts_join filter to query posts by initial letter
	 */
	function posts_join_get_posts_by_initial_letter( $join, \WP_Query $query ) {

		$initial_letter = $query->get( 'initial_letter' );

		if ( $initial_letter ) {
			
			global $wpdb;

			$join .= " LEFT JOIN $wpdb->term_relationships as wtr ON ($wpdb->posts.ID = wtr.object_id)";
			$join .= " LEFT JOIN $wpdb->term_taxonomy as wtt ON (wtr.term_taxonomy_id = wtt.term_taxonomy_id)";
			$join .= " LEFT JOIN $wpdb->terms as wt ON (wtt.term_id = wt.term_id)";

		}

		return $join;

	}

  /**
   * posts_where filter to query posts by initial letter
   * 
   * @since 1.0.0 
   */
  function posts_where_get_posts_by_initial_letter( $where, \WP_Query $query ) {

    $initial_letter = $query->get( 'initial_letter' );

    if ( $initial_letter ) {

      global $wpdb;

			$where .= " AND (wtt.taxonomy = 'initialcharacter') AND (wt.name = '$initial_letter')";

		}

    return $where;

  }
	
	/**
	 * Modify single view
	 * 
	 * @param string $template - the default template for the current view
	 * 
	 * @since 1.0.0
	 * 
	 * @return string - final Template to use
	 */
	function template_include( $template ) {

		$post_type = wppedia_utils()->is_wiki_post_type();

		// Bail early if the current page is not a wiki page
		if ( ! $post_type  )
			return $template;

		// Check for single view
		if ( is_singular('wp_pedia_term') ) {

			// Don't modify the template if specified in the current Theme
			if ( locate_template(['single-' . $post_type . '.php']) )
				return $template;

			// Check for the single Template by Post type
			if ( $post_type == 'wp_pedia_term' ) {

				$single_template = wppedia_template()->get_view(
					'single', 
					[], 
					false
				);
				
				return ( $single_template ) ? $single_template : $template;
				
			}

		}

		// Check for archive view
		if ( ( is_tax('initialcharacter') || is_post_type_archive('wp_pedia_term') ) && ! is_search() ) {

			// Don't modify the template if specified in the current Theme
			if ( locate_template(['archive-' . $post_type . '.php']) )
				return $template;

			// Checkk for the archive Template by Post Type
			if ( $post_type == 'wp_pedia_term' ) {

				$archive_template = wppedia_template()->get_view(
					'archive', 
					[], 
					false
				);
				
				return ( $archive_template ) ? $archive_template : $template;

			}

		}

    return $template;

	}

}
