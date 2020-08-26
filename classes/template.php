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

		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_filter( 'post_class', [ $this, 'post_class' ], 10,3 );

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
		//add_filter( 'template_include', [ $this, 'template_include' ] );

		/**
		 * WPPedia Sidebar
		 * 
		 * @since 1.0.0
		 */
		add_action( 'widgets_init', [ $this, 'register_sidebar' ] );

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
		if ( locate_template('index-wppedia.php') && ! $this->current_template_exists_in_theme() )
			return get_query_template('index-wppedia');

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
			helper::getInstance()->is_wiki_post_type() ||
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
		if ( locate_template('search-wppedia.php') && helper::is_wiki_search() )
			return get_query_template('search-wppedia');

		return $template;

	}

	/**
	 * Load WPPedia default Templates
	 * 
	 * @since 1.0.0
	 * 
	 * @return string $template
	 */
	public function template_include( $template ) {

		if ( 
			! $this->current_template_exists_in_theme() || 
			( 
				locate_template('search-wppedia.php') && 
				helper::is_wiki_search() 
			) ||
			locate_template('index-wppedia.php')
		)
			return $template;

		// TODO: Add an Option to determine if the default Theme templates should be used over the WPPedia Archive styles
		if ( is_archive() && false !== $this->get_view( 'archive', [], false ) ) {
			// Load default Archive view
			return $this->get_view( 'archive', [], false );
		} elseif ( is_singular() && false !== $this->get_view( 'single', [], false ) ) {
			// Load default Single view
			return $this->get_view( 'single', [], false );
		}

		return $template;

	}

	/**
	 * Add a body class to WPPedia Pages
	 * 
	 * @since 1.0.0
	 */
	function body_class( $classes ) {

		if ( helper::getInstance()->is_wiki_post_type() )
			$classes[] = apply_filters( 'wppedia_body_class', 'wppedia-page' );

		return $classes;

	}

	/**
	 * Add custom post Classes
	 * 
	 * @since 1.0.0
	 */
	function post_class( $classes, $class, $post_id ) {

		if ( is_admin() || ! helper::getInstance()->is_wiki_post_type() )
			return $classes;

		$classes[] = 'wppedia-initial-letter_' . helper::getInstance()->post_initial_letter( $post_id );

		return $classes;
			
	}

	/**
	 * Determine if WPPedia is using a sidebar
	 * 
	 * @return bool
	 * 
	 * @since 1.0.0
	 */
	public function wppedia_has_sidebar() {

		// Determine if a sidebar can be used
		if ( ! is_active_sidebar( 'sidebar_wppedia' ) && helper::getInstance()->is_wiki_post_type() )
			return false;

		return true;

	}

	/**
	 * Register WPPedia Sidebar
	 * 
	 * @since 1.0.0
	 */
	public function register_sidebar() {

		register_sidebar( [
			'name'					=> __( 'WPPedia Sidebar', 'wppedia' ),
			'id'						=> 'sidebar_wppedia',
			'description'		=> __( 'Widgets in this area will be shown on Single WPPedia Entries', 'wppedia' ),
			'before_widget'	=> apply_filters( 'wppedia_sidebar_widget_before', '<div id="%1$s" class="wppedia_widget widget %2$s">' ),
			'after_widget'	=> apply_filters( 'wppedia_sidebar_widget_after', '</div>' ),
			'before_title'	=> apply_filters( 'wppedia_sidebar_widget_title_before', '<div class="wppedia_widget_title widget-title">' ),
			'after_title'		=> apply_filters( 'wppedia_sidebar_widget_title_after', '</div>' )
		] );

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
	 * Get the excerpt with fallback generated from the content
	 * 
	 * @param WP_Post|int $post - Post ID or object
	 * @param bool $force_balanced_tags - If true try to keep bold or other formatting
	 * 
	 * @since 1.0.0
	 */
	public function get_the_excerpt( $post = null, int $excerpt_length = 40, bool $force_balanced_tags = false ) {

		$str = '';

		// setup Postdata
    $post = get_post( $post );
    if ( empty( $post ) )
      return;

		if ( ! has_excerpt( $post ) ) {

			// Get the Post Content (formatted)
			setup_postdata( $post );
			$str = get_the_content( null, false, $post );
			wp_reset_postdata( $post );

			// Check if Text is not empty
			if ( '' != $str && $str ) {

				// Add some filters to the text
				$str = \strip_shortcodes( $str );
				$str = str_replace(']]&gt;', ']]&gt;', $str);

				// Trim and format the string
				if ( $force_balanced_tags )
					$str = force_balance_tags( html_entity_decode( wp_trim_words( htmlentities( $str ), $excerpt_length, null ) ) );
				else
					$str = wp_trim_words( $str, $excerpt_length, null );

			}

		} else {

			// If an excerpt was specified just add some p tags
			$str = wpautop( $post->post_excerpt );

		}

		return apply_filters( 'wppedia_tooltip_get_excerpt', $str );

	}

	/**
	 * Display the autogenerated excerpt
	 * 
	 * @param WP_Post|int $post - Post ID or object
	 * 
	 * @since 1.0.0
	 */
	public function the_excerpt( $post = null, int $excerpt_length = 40, bool $force_balanced_tags = false) {

		echo apply_filters( 'wppedia_tooltip_excerpt', $this->get_the_excerpt( $post, $excerpt_length, $force_balanced_tags ) );

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

		$post_type = post_type::getInstance()->post_type;
		$searchUrl = get_post_type_archive_link( $post_type );

		/**
		 * Predefined attributes
		 */
		$_attrs = [
			'role'		=> apply_filters( 'wppedia_searchform_attrs__role', 'search' ),
			'method' 	=> apply_filters( 'wppedia_searchform_attrs__method', 'GET' ),
			'class' 	=> apply_filters( 'wppedia_searchform_attrs__class', 'search-form wppedia-search' ),
			'id' 			=> apply_filters( 'wppedia_searchform_attrs__id', 'wppedia_searchform' ),
			'action' 	=> $searchUrl
		];

		$attrs = array_merge( $attrs, $_attrs );

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
			<?php
			/**
			 * If any nice search is active try to get around this and
			 * add a query parameter.
			 */
			global $wp_rewrite;
			if ( isset( $wp_rewrite->search_structure ) ): ?>
			<input type="hidden" name="WPPedia" value="true" />
			<?php endif; ?>
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
