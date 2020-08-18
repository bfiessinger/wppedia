<?php

/**
 * WP Wiki Template
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\helper;
use bf\wpPedia\post_type;

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
		add_filter( 'body_class', [ $this, 'body_class' ] );
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
	 * Add a body class to WPPedia Pages
	 */
	function body_class( $classes ) {

		if ( helper::getInstance()->is_wiki_post_type() )
			$classes[] = apply_filters( 'wppedia_body_class', 'wppedia-page' );

		return $classes;

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

	public function __filtered_search_input_id() {
		return apply_filters( 'wppedia_search_input_id', 'wppedia_search_input' );
	}

	/**
	 * Print Search input field with autosuggest renderer
	 * 
	 * @since 1.0.0
	 */
	public function render_search_input() { ?>
		<div class="wppedia-search-field-wrapper">
			<input type="search" class="search-field" id="<?php echo $this->__filtered_search_input_id(); ?>" placeholder="<?php _e('Search glossary', 'wppedia'); ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php _e('Search for', 'wppedia'); ?>:" autocomplete="off" />
		</div>
	<?php }

	/**
	 * Print the whole searchform
	 * 
	 * @since 1.0.0
	 */
	function render_searchform() { ?>

		<form <?php echo $this->get_search_form_attrs(); ?>>
			<label class="screen-reader-text"><?php _e('Search glossary', 'wppedia'); ?></label>
			<?php 
			// Render the search input
			$this->render_search_input();
			?>
			<input type="hidden" name="post_type" value="<?php echo post_type::getInstance()->post_type; ?>" />
			<input type="submit" class="search-submit" value="Search" />
		</form>
		
	<?php
	
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

	/**
	 * Get a single Navigation link
	 * 
	 * @param string $term_slug - Initial Character taxonomy slug
	 */
	function get_char_navigation_link( string $term_slug ) {

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
			$link_url = ( helper::getInstance()->has_static_archive_page() ) ? get_permalink( helper::getInstance()->has_static_archive_page() ) : get_post_type_archive_link( 'wppedia_term' );
			$link_title = __( 'home', 'wppedia' );
			$link_classes[] = 'wppedia_navigation_home';

			if ( helper::getInstance()->is_wiki_home() )
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
