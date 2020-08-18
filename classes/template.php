<?php

/**
 * WP Wiki Template
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\helper;

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

	protected function __construct() {}
	
	public function start() {
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
		 * This function adds a new template to the wordpress template hierarchy.
		 * it works like index.php but only if the requested page is related to WPPedia.
		 * 
		 * Usage:
		 * Create a custom index-wppedia.php file in the root of your WordPress Theme.
		 * 
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
		 * 
		 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
		 */
		if ( 
			// Return the Default Template for all non WPPedia Posts
			! helper::getInstance()->is_wiki_post_type() ||
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
	 * Template functions related to the Searchform
	 */

	/**
	 * Get the template for WPPedia Searchform
	 * 
	 * @return void
	 * 
	 * @since 1.0.0
	 */
	public function get_search_form() {

		// Don't modify the template if specified in the current Theme
		if ( locate_template(['wppedia-searchform.php']) ) {

			locate_template(['wppedia-searchform.php'], true);
			return;

		}

		// print searchform
		$this->get_partial( 'searchform' );		

	}

	/**
	 * Get searchform attributes
	 * 
	 * @param array $attrs - Additional Attributes
	 * @param boolean $tostring - Whether to render the Attributes as a string or return an Array
	 * 
	 * @since 1.0.0
	 */
	public function get_search_form_attrs( array $attrs = [], bool $tostring = true ) {

		$post_type = 'wp_pedia_term';
		$searchUrl = get_post_type_archive_link( $post_type );

		$default = [
			'role'		=> apply_filters( 'wppedia_searchform_attrs__role', 'search' ),
			'method' 	=> apply_filters( 'wppedia_searchform_attrs__method', 'GET' ),
			'class' 	=> apply_filters( 'wppedia_searchform_attrs__class', 'search-form wppedia-search' ),
			'id' 			=> apply_filters( 'wppedia_searchform_attrs__id', 'wppedia_searchform' ),
			'action' 	=> apply_filters( 'wppedia_searchform_attrs__action', $searchUrl )
		];

		$attrs = array_merge( $default, $attrs );

		if ( $tostring ) {

			$final = '';

			$attr_index = 0;
			$attr_count = count( $attrs );
			foreach ( $attrs as $k => $v ) {
				$attr_index++;
				$final .= $k . '="' . $v . '"';
				if ( $attr_index < $attr_count )
					$final .= ' ';
			}

			return $final;

		}

		return $attrs;

	}
	
	/**
	 * Template functions related to the Initial char navigation
	 */

	/**
	 * Get the template for WPPedia Initial char navigation
	 * 
	 * @return void
	 * 
	 * @since 1.0.0
	 */
	function get_char_navigation() {

		// Don't modify the template if specified in the current Theme
		if ( locate_template(['wppedia-navigation.php']) ) {

			locate_template(['wppedia-navigation.php'], true);
			return;

		}

		// print searchform
		$this->get_partial( 'initial-letter-navigation' );		

	}

}
