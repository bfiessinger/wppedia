<?php

/**
 * WP Wiki Template
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class template {

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
		add_filter( 'template_include', [ $this, 'template_include' ] );
	}
	
	/**
	 * Use a custom default template for the WP Template Hierarchy.
	 * If no custom template for singular posts, custom post type
	 * archives or taxonomy archives was found try to use the template
	 * index-wppedia.php
	 * 
	 * @see https://wphierarchy.com/
	 * 
	 * @since 1.0.0
	 * 
	 * @return string $template - the current Template file in your theme's Root folder
	 */
	public function template_include( $template ) {

		$custom_index_include = true;

		/**
		 * Check for the following Templates first:
		 * 
		 * - Posttype Archive: 
		 * 			archive-wppedia_term.php
		 * - Initial Character Taxonomy: 
		 * 			taxonomy-wppedia_initial_letter.php OR 
		 * 			taxonomy-wppedia_initial_letter-{initial_letter}.php
		 * - Singular Posts: 
		 * 			single-wppedia_term.php OR 
		 * 			single-wppedia_term-{post_name}.php
		 */
		if ( 
			// Return the Default Template for all non WPPedia Posts
			! \wppedia_utils()->is_wiki_post_type() ||
			// Post Type Archive
			( 
				is_post_type_archive( 'wppedia_term' ) && 
				locate_template( 'archive-wppedia_term.php' ) 
			) ||
			// Taxonomy Archive
			(
				is_tax( 'wppedia_initial_letter' ) && 
				( 
					locate_template( 'taxonomy-wppedia_initial_letter.php' ) ||
					locate_template( 'taxonomy-wppedia_initial_letter-' . get_queried_object()->slug . '.php' )
				)
			) ||
			// Singular Posts
			(
				is_singular( 'wppedia_term' ) &&
				(
					locate_template( 'single-wppedia_term.php' ) ||
					locate_template( 'single-wppedia_term-' . get_queried_object()->post_name . '.php' )
				)
			)
		)
			$custom_index_include = false;

		// Return custom index for WPPedia Pages if the file exists
		// and no other template should override it
		if ( locate_template('index-wppedia.php') && $custom_index_include )
			return get_query_template('index-wppedia');

		return $template;

	}

  /**
   * Get a specific View
   * 
   * @since 1.0.0
   */
  public function get_view(string $view, array $args = [], bool $display = true) {

    $view_file = wpPediaPluginDir . 'views/view-' . $view . '.php';

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

    $partial_file = wpPediaPluginDir . 'partials/partial-' . $partial . '.php';

    if ( file_exists( $partial_file ) ) {

			if ( $display )
				require_once $partial_file;
			else
				return $partial_file;

		}
			
		return false;

	}

	/**
	 * Get the WPPedia Searchform
	 * 
	 * @return void
	 * 
	 * @since 1.0.0
	 */
	public function get_searchform() {

		// Don't modify the template if specified in the current Theme
		if ( locate_template(['wppedia-searchform.php']) ) {

			locate_template(['wppedia-searchform.php'], true);
			return;

		}

		// print searchform
		$this->get_partial( 'searchform' );		

	}
	
	function initial_letter_navigation() {

		// Don't modify the template if specified in the current Theme
		if ( locate_template(['wppedia-navigation.php']) ) {

			locate_template(['wppedia-navigation.php'], true);
			return;

		}

		// print searchform
		$this->get_partial( 'initial-letter-navigation' );		

	}

}
