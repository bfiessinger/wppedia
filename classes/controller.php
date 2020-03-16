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
   */
  public static function getInstance() {

    if ( null === self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;

	}
	
  protected function __clone() {}

  protected function __construct() {
  
		add_filter( 'template_include', [ $this, 'template_include' ], 10 );
		
		add_action( 'init', [ $this, 'rewrite_initial_letter' ], 10 );
    
	}
	
	/**
	 * Modify single view
	 */
	function template_include( $template ) {

		global $post;
		$post_type = $post->post_type;

		// Check for single view
		if ( is_singular( $post_type ) ) {

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
		if ( is_archive( $post_type ) ) {

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

	static function rewrite_initial_letter() {

		add_rewrite_rule(
			'^properties/([0-9]+)/?',
			'index.php?pagename=properties&property_id=$matches[1]',
			'top'
		);

	}

}
