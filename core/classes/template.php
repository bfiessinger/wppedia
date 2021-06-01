<?php

/**
 * WP Wiki Template
 * 
 * @since 1.2.0
 */

namespace WPPedia;

use WPPedia\options;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) || die();

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

		/**
		 * Add custom Classes to the body and each post
		 * 
		 * @since 1.0.0
		 */
		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_filter( 'post_class', [ $this, 'post_class' ], 10, 3 );

		/**
		 * Custom Templates
		 * 
		 * @since 1.0.0
		 */
		add_filter( 'template_include', [ $this, 'custom_index_php' ] );
		add_filter( 'template_include', [ $this, 'custom_search_php' ] );

		/**
		 * Load default Templates
		 * 
		 * @since 1.0.0
		 */
		add_filter( 'template_include', [ $this, 'template_include' ] );

	}

	/**
	 * This function adds a new template to the wordpress template hierarchy.
	 * it works like index.php but only if the requested page is related to WPPedia.
	 * 
	 * Use a custom default template for the WP Template Hierarchy.
	 * If no custom template for singular posts, custom post type
	 * archives or taxonomy archives was found try to use the template
	 * index-wppedia.php
	 * 
	 * Usage:
	 * Create a custom index-wppedia.php file in the root of your WordPress Theme.
	 * 
	 * @see https://wphierarchy.com/
	 * 
	 * @since 1.0.0
	 * 
	 * @return string $template - the current Template file in your theme's Root folder
	 */
	public function custom_index_php( $template ) {

		// Return custom index for WPPedia Pages if the file exists
		// and no other template should override it
		if ( locate_template(apply_filters('wppedia_custom_index_file', 'index-wppedia.php')) && ! $this->current_template_exists_in_theme() )
			return get_query_template(apply_filters('wppedia_custom_index_file', 'index-wppedia.php'));

		return $template;

	}

	/**
	 * Check whether or not a custom index-wppedia.php can get loaded
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
	private function current_template_exists_in_theme() {

		if ( 
			// Return the Default Template for all non WPPedia Posts
			is_wppedia_page() ||
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
			return true;

		return false;

	}

	/**
	 * Use a custom search template for the WP Template Hierarchy.
	 * If possible the plugin will try to include the template search-wppedia.php
	 * from your theme structure
	 * 
	 * @see https://wphierarchy.com/
	 * 
	 * @since 1.0.0
	 * 
	 * @return string $template - the current Template file in your theme's Root folder
	 */
	public function custom_search_php( $template ) {

		/**
		 * Return custom search for WPPedia if the file exists
		 * and we are on a wiki search
		 */
		if ( locate_template('search-wppedia.php') && is_wppedia_search() )
			return get_query_template('search-wppedia');

		return $template;

	}

	/**
	 * Load WPPedia default Templates
	 * 
	 * @since 1.1.3
	 * 
	 * @return string $template
	 */
	public function template_include( $template ) {

		if ( ! $this->current_template_exists_in_theme() )
			return $template;

		if ( is_wppedia_archive() && false != get_option('wppedia_archive_use_templates', options::get_option_defaults('wppedia_archive_use_templates')) && false !== wppedia_locate_template( 'archive.php' ) ) {
			// Load default Archive view
			return wppedia_locate_template( 'archive.php' );
		} elseif ( is_wppedia_singular() && false != get_option('wppedia_singular_use_templates', options::get_option_defaults('wppedia_singular_use_templates')) && false !== wppedia_locate_template( 'single.php' ) ) {
			// Load default Single view
			return wppedia_locate_template( 'single.php' );
		}

		return $template;

	}

	/**
	 * Add a body class to WPPedia Pages
	 * 
	 * @since 1.0.0
	 */
	function body_class( $classes ) {

		if ( is_wppedia_page() )
			$classes[] = apply_filters( 'wppedia_body_class', 'wppedia-page wppedia' );

		return $classes;

	}

	/**
	 * Add custom post Classes
	 * 
	 * @since 1.0.0
	 */
	function post_class( $classes, $class, $post_id ) {

		if ( is_admin() || ! is_wppedia_page() )
			return $classes;

		$classes[] = 'wppedia-initial-letter_' . wppedia_get_post_initial_letter( $post_id );

		$classes = apply_filters('wppedia_post_class', $classes);

		return $classes;
			
	}
	
	/**
	 * Template functions related to the Initial char navigation
	 */

	/**
	 * Generate a single Navigation link
	 * 
	 * @param string $term_slug - Initial Character taxonomy slug
	 */
	function generate_char_navigation_link( string $term_slug ) {

		$output = '';

		$link_name = null;
		$link_url = null;
		$link_title = '';

		/**
		 * Filter for common link Classes
		 * 
		 * @param array $link_classes - Array with classes for all link elements
		 */
		$link_classes = apply_filters( 'wppedia_navigation_link__classes', [] );

		/**
		 * Filter for the active link class
		 * 
		 * @param string $active_class - Classname for the active element
		 */
		$active_class = apply_filters( 'wppedia_navigation_link__active_class', 'active' );

		if ( 'home' == $term_slug ) {

			$link_name = __( 'home', 'wppedia' );
			$link_url = ( wppedia_has_static_frontpage() ) ? get_permalink(wppedia_get_page_id('front')) : get_post_type_archive_link( 'wppedia_term' );
			$link_title = __( 'home', 'wppedia' );
			$link_classes[] = 'wppedia_navigation_home';

			if ( is_wppedia_frontpage() )
				$link_classes[] = $active_class;

			$output .= $this->get_char_navigation_link_anchor( $link_name, $link_url, $link_title, $link_classes );

		} else if ( term_exists( $term_slug, 'wppedia_initial_letter' ) ) {

			// Get Information about the current term
			$obj = get_term_by( 'slug', $term_slug, 'wppedia_initial_letter' );

			$link_name = $obj->name;
			$link_url = get_term_link( $obj );
			$link_title = sprintf( __('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $obj->name, $obj->count );

			if ( isset( get_queried_object()->term_id ) && $obj->term_id === get_queried_object()->term_id )
				$link_classes[] = $active_class;

			$output .= $this->get_char_navigation_link_anchor( $link_name, $link_url, $link_title, $link_classes );

		} else  {

			$output .= '<span';

			if ( ! empty( $link_classes ) ) {
				$output .= ' class="';
				$output .= implode( ' ', $link_classes );
				$output .= '"';
			}

			$output .= '>';
			$output .= apply_filters( 'wppedia_navigation_link__name', $term_slug );
			$output .= '</span>';

		}

		return apply_filters( 'wppedia_navigation_link', $output );

	}

	function get_char_navigation_link_anchor( string $name, string $url, string $title = '', array $classes = [] ) {
			
		$link_html = '<a href="' . $url . '"';
		$link_html .= ' title="' . $title . '"';

		if ( ! empty( $classes ) )
			$link_html .= ' class="' . implode( ' ', $classes ) . '"';

		$link_html .= '>';

		$link_html .= apply_filters( 'wppedia_navigation_link__name', $name );

		$link_html .= '</a>';

		return $link_html;

	}

}
