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

		$post_type = wiki_utils()->is_wiki_post_type();

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

				$single_template = wiki_utils()->get_view(
					'single', 
					[], 
					false
				);
				
				return ( $single_template ) ? $single_template : $template;
				
			}

		}

		// Check for archive view
		if ( is_post_type_archive('wp_pedia_term') ) {

			// Don't modify the template if specified in the current Theme
			if ( locate_template(['archive-' . $post_type . '.php']) )
				return $template;

			// Checkk for the archive Template by Post Type
			if ( $post_type == 'wp_pedia_term' ) {

				$archive_template = wiki_utils()->get_view(
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
