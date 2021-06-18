<?php

/**
 * WPPedia Post Type related
 * 
 * @since 1.2.1
 */

namespace WPPedia;

use WPPedia\options;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class postType {

	/**
	 * Public variables
	 */
	public $post_types = [];
	public $taxonomies = [];

	public $post_limit = 500;

	/**
	 * Protected variables
	 */
	protected $permalink_base;

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

		$this->definePostTypes();
		$this->defineTaxonomies();

		add_action( 'init', [ $this, 'set_permalink_base' ], 9 );

		// Create Post type
		add_action( 'init', [ $this, 'register_wppedia_post_type' ], 10 );

		// Create Fake Taxonomy to query by initial letter
		add_action( 'init', [ $this, 'create_wppedia_initial_letter_tax' ], 10 );

		// Create glossary category taxonomy
		add_action( 'init', [ $this, 'create_wppedia_category_tax' ], 10 );

		// Setup a post creation limit
		add_action( 'publish_' . $this->post_types['main'] , [ $this, 'limit_num_posts' ] );
		add_action( 'admin_notices', [ $this, 'limit_reached_msg_notice' ] );

		// Setup rewrite rules
		add_action( 'init', [ $this, 'add_rewrite_rules' ], 11 );
		add_filter( 'post_type_link', [ $this, 'post_type_link' ], 10, 2 );
		add_action( 'save_post_wppedia_term', [ $this, 'set_flush_rewrite_rules_flag' ] );

		// Set Initial Letter Taxonomy on post save
		add_action( 'save_post_wppedia_term', [ $this, 'manage_initial_character_onsave' ], 10, 3 );

	}

	private function definePostTypes() {
		$this->post_types = [
			'main' => 'wppedia_term'
		];
	}

	private function defineTaxonomies() {
		$this->taxonomies = [
			'initial_character' => 'wppedia_initial_letter',
			'category'					=> 'wppedia_category'
		];
	}

	/**
	 * Set permalink base from wp_options
	 * 
	 * @since 1.1.6
	 */
	function set_permalink_base() {
		$this->permalink_base = get_option('wppedia_permalink_base');
		if (!$this->permalink_base) {
			$this->permalink_base = options::get_option_defaults('wppedia_permalink_base');
		}
	}

  /**
   * Register Wiki Custom Post type
   * 
	 * @uses register_post_type
	 * 
   * @since 1.2.3
   */
  public function register_wppedia_post_type() {
    $labels = [
			'name' => _x( 'Glossary', 'Post Type General Name', 'wppedia' ),
			'singular_name' => _x( 'Glossary Term', 'Post Type Singular Name', 'wppedia' ),
			'menu_name' => _x( 'Glossary', 'Admin Menu text', 'wppedia' ),
			'name_admin_bar' => _x( 'Glossary Term', 'Add New on Toolbar', 'wppedia' ),
			'archives' => __( 'Glossary Term Archives', 'wppedia' ),
			'attributes' => __( 'Glossary Term Attributes', 'wppedia' ),
			'parent_item_colon' => __( 'Parent Glossary Term:', 'wppedia' ),
			'all_items' => __( 'All Glossary Terms', 'wppedia' ),
			'add_new_item' => __( 'Add New Glossary Term', 'wppedia' ),
			'new_item' => __( 'New Glossary Term', 'wppedia' ),
			'edit_item' => __( 'Edit Glossary Term', 'wppedia' ),
			'update_item' => __( 'Update Glossary Term', 'wppedia' ),
			'view_item' => __( 'View Glossary Term', 'wppedia' ),
			'view_items' => __( 'View Glossary', 'wppedia' ),
			'search_items' => __( 'Search Glossary Term', 'wppedia' ),
			'insert_into_item' => __( 'Insert into Glossary Term', 'wppedia' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Glossary Term', 'wppedia' ),
			'items_list' => __( 'Glossary list', 'wppedia' ),
			'items_list_navigation' => __( 'Glossary list navigation', 'wppedia' ),
			'filter_items_list' => __( 'Filter Glossary list', 'wppedia' ),
		];

    $rewrite = [
			'slug'				=> ltrim( rtrim( $this->permalink_base, '/' ), '/' ),
      'with_front'	=> false,
      'pages' 			=> true,
      'feeds' 			=> true,
		];

    $args = [
      'label' => __( 'Glossary Term', 'wppedia' ),
      'description' => __( '', 'wppedia' ),
      'labels' => $labels,
      'menu_icon' => 'dashicons-book-alt',
      'supports' => [ 'thumbnail', 'title', 'editor', 'excerpt', 'revisions', 'author' ],
      'taxonomies' => $this->taxonomies,
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_admin_bar' => true,
      'show_in_nav_menus' => true,
      'can_export' => true,
      'has_archive' => true,
      'hierarchical' => false,
      'exclude_from_search' => false,
      'show_in_rest' => true,
      'publicly_queryable' => true,
			'capability_type' => 'post',
			'has_archive' => false,
      'rewrite' => $rewrite
		];

		if ( false == wppedia_get_page_id('front') ) {
			$args['has_archive'] = ltrim( rtrim( $this->permalink_base, '/' ), '/' );
		}
			
		\register_post_type( $this->post_types['main'], $args );

	}

	/**
	 * Setup a post creation limit as the free version does not offer an index which might cause
	 * the crosslink generation to be very slow
	 * 
	 * @since 1.2.0
	 */
	function limit_num_posts( $post_id ) {

		if ( NULL === $this->post_limit )
			return;

		global $wpdb;

		$limit_post_type = $this->post_types['main'];
		
		$query_num_posts = "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = '$limit_post_type' AND post_status = 'publish' ";
		$num_published = $wpdb->get_var($query_num_posts);
			
		// Check the limit
		if( $num_published > $this->post_limit ) {

			$upost = array();
			$upost['ID'] = $post_id;
			$upost['post_status'] = 'draft';	//force it back to draft
			wp_update_post($upost);
				
			//show an error message
			add_filter('redirect_post_location', [ $this, 'do_limit_reached_msg' ] );		

		}

	}

	function do_limit_reached_msg($loc) {

		//message=6 is the published message
		if( strpos( $loc, '&message=6' ) )
			return add_query_arg( [
					'message'=>'1', 
					'wppedia_limit_reached'=>'1' 
				], $loc
			);			
		else
			return add_query_arg('wppedia_limit_reached', '1', $loc);	

	}

	function limit_reached_msg_notice() {

		if( NULL !== $this->post_limit && ! empty( $_REQUEST['wppedia_limit_reached'] ) && $_REQUEST['wppedia_limit_reached'] == '1' ) {
			echo '<div class="wppedia-admin-message wppedia-admin-message-flex notice notice-error is-dismissible">';
			echo '<div>';
			echo '<p>You have reached your post creation limit. <strong>This post has not been published.</strong> Upgrade to PRO to remove limitations.</p>';
			echo '<p><strong>PRO will be available soon!</strong></p>';
			echo '</div>';
			echo '<img class="wppedia-pro-logo" src="' . WPPediaPluginUrl . 'assets/img/wppedia-pro-logo.svg">';
			echo '</div>';
		}

	}

	/**
	 * Register a fake Taxonomy for initial letters
	 * used to query glossary terms by initial letter
	 * 
	 * @uses register_taxonomy
	 * 
	 * @since 1.2.3
	 */
	function create_wppedia_initial_letter_tax() {

		$labels = [
			'name'              => _x( 'Initial Characters', 'taxonomy general name', 'wppedia' ),
			'singular_name'     => _x( 'Initial Character', 'taxonomy singular name', 'wppedia' ),
			'search_items'      => __( 'Search Initial Characters', 'wppedia' ),
			'all_items'         => __( 'All Initial Characters', 'wppedia' ),
			'parent_item'       => __( 'Parent Initial Character', 'wppedia' ),
			'parent_item_colon' => __( 'Parent Initial Character:', 'wppedia' ),
			'edit_item'         => __( 'Edit Initial Character', 'wppedia' ),
			'update_item'       => __( 'Update Initial Character', 'wppedia' ),
			'add_new_item'      => __( 'Add New Initial Character', 'wppedia' ),
			'new_item_name'     => __( 'New Initial Character Name', 'wppedia' ),
			'menu_name'         => __( 'Initial Character', 'wppedia' ),
		];

		$rewrite = [
			'slug' => ltrim( rtrim( $this->permalink_base, '/' ), '/' ),
			'with_front' => false,
			'hierarchical' => false,
		];

		$args = [
			'labels' => $labels,
			'description' => __( '', 'wppedia' ),
			'hierarchical' => false,
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'show_in_quick_edit' => false,
			'show_admin_column' => false,
			'show_in_rest' => true,
			'rewrite' => $rewrite
		];

		\register_taxonomy( $this->taxonomies['initial_character'], [ $this->post_types['main'] ], $args );

	}

	/**
	 * Set and update the initial character fake taxonomy on save
	 * 
	 * @uses wp_insert_term
	 * @uses get_terms
	 * @uses wp_delete_term
	 * 
	 * @since 1.2.1
	 */
	function manage_initial_character_onsave( int $post_ID, \WP_POST $post, bool $update ) {

		$cur_initial = wppedia_get_post_initial_letter( $post_ID );

		$taxonomy = $this->taxonomies['initial_character'];
		$cur_initial_encoded = wppedia_slugify( $cur_initial, _x( 'other', 'wppedia slugs', 'wppedia' ) );
		
		// Create a new term based on the initial letter
		\wp_insert_term( 
			$cur_initial, 
			$taxonomy, 
			[
				'slug' => $cur_initial_encoded
			]
		);

		// Set post term for current post
		\wp_set_post_terms(
			$post_ID,
			[
				$cur_initial_encoded
			],
			$taxonomy,
			false
		);

		// Delete empty taxonomy terms
		$all_terms = \get_terms( [
			'taxonomy' 		=> $taxonomy,
			'hide_empty' 	=> false
		] );

		foreach( $all_terms as $term ) {

			$term_count = $term->count;

			if ( $term_count < 1 )
				\wp_delete_term( $term->term_id, $taxonomy );
			
		}

	}

	/**
	 * Register a term category taxonomy
	 * 
	 * @uses register_taxonomy
	 * 
	 * @since 1.2.1
	 */
	function create_wppedia_category_tax() {

		$labels = array(
			'name'              => _x( 'Glossary categories', 'taxonomy general name', 'wppedia' ),
			'singular_name'     => _x( 'Glossary category', 'taxonomy singular name', 'wppedia' ),
			'search_items'      => __( 'Search Glossary categories', 'wppedia' ),
			'all_items'         => __( 'All Glossary categories', 'wppedia' ),
			'parent_item'       => __( 'Parent Glossary category', 'wppedia' ),
			'parent_item_colon' => __( 'Parent Glossary category:', 'wppedia' ),
			'edit_item'         => __( 'Edit Glossary category', 'wppedia' ),
			'update_item'       => __( 'Update Glossary category', 'wppedia' ),
			'add_new_item'      => __( 'Add New Glossary category', 'wppedia' ),
			'new_item_name'     => __( 'New Glossary category Name', 'wppedia' ),
			'menu_name'         => __( 'Categories' ),
		);
		$rewrite = array(
			'slug' => 'glossary-category',//ltrim( rtrim( $this->permalink_base, '/' ), '/' ) . '/kategorie',
			'with_front' => false,
			'hierarchical' => true,
		);
		$args = array(
			'labels' => $labels,
			'description' => __( '', 'wppedia' ),
			'hierarchical' => true,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => true,
			'show_in_quick_edit' => true,
			'show_admin_column' => false,
			'show_in_rest' => true,
			'rewrite' => $rewrite,
		);
		\register_taxonomy( $this->taxonomies['category'], [ $this->post_types['main'] ], $args );

	}

	/**
	 * Add rewrite rules
	 * 
	 * @since 1.2.3
	 */
	function add_rewrite_rules() {
		$initial_letters = get_terms( [
			'taxonomy' => $this->taxonomies['initial_character'],
			'hide_empty' => false,
		] );

		foreach ($initial_letters as $term) {
			add_rewrite_rule(
				ltrim( rtrim( $this->permalink_base, '/' ), '/' ) . '/(' . $term->slug . ')(?:/|$)(?:(?:[^/]*?/?)([0-9]+))?/?$',
				'index.php?post_type=' . $this->post_types['main'] . '&wppedia_initial_letter=$matches[1]&paged=$matches[2]',
				'top'
			);
		}

		if (false != get_option('wppedia_permalink_use_initial_character', options::get_option_defaults('wppedia_permalink_use_initial_character'))) {
			add_rewrite_rule(
				ltrim( rtrim( $this->permalink_base, '/' ), '/' ) . '/([^/]*)/([^/]*)/?',
				'index.php?post_type=' . $this->post_types['main'] . '&wppedia_initial_letter=$matches[1]&name=$matches[2]',
				'top'
			);
		} else {
			add_rewrite_rule(
				ltrim( rtrim( $this->permalink_base, '/' ), '/' ) . '/([^/]*)/?',
				'index.php?post_type=' . $this->post_types['main'] . '&wppedia_term=$matches[1]&name=$matches[1]',
				'top'
			);
		}
	}

	/**
	 * Change default post link
	 * 
	 * @since 1.2.1
	 */
	function post_type_link( $permalink, $post ) {
    // bail if post type is not wppedia_term
		if (wppedia_get_post_type() !== $post->post_type)
			return $permalink;

		if (false != get_option('wppedia_permalink_use_initial_character', options::get_option_defaults('wppedia_permalink_use_initial_character'))) {
			$terms = wp_get_post_terms($post->ID, $this->taxonomies['initial_character']);
			// set location, if no location is found, provide a default value.
			if ( 0 < count( $terms ))
				$init_char = $terms[0]->slug;
			else
				$init_char = 'other';
	
			$init_char = urlencode( $init_char );
			$permalink = rtrim( get_home_url(), '/' ) . '/' . ltrim( rtrim( $this->permalink_base, '/' ), '/' ) . '/' . $init_char . '/' . $post->post_name . '/';
		} else {
			$permalink = rtrim( get_home_url(), '/' ) . '/' . ltrim( rtrim( $this->permalink_base, '/' ), '/' ) . '/' . $post->post_name . '/';
		}

		return $permalink;
	}

	/**
	 * Set a custom option that tells WPPedia to flush
	 * rewrite rules 
	 * 
	 * @since 1.1.6
	 */
	function set_flush_rewrite_rules_flag() {
    if (!get_option('wppedia_flush_rewrite_rules_flag')) {
			add_option('wppedia_flush_rewrite_rules_flag', true);
		}
	}

}
