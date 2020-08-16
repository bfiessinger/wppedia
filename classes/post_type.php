<?php

/**
 * wpPedia Post Type related
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\helper;
use bf\wpPedia\options\plugin_settings;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class post_type {

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

		// Create Post type
		add_action( 'init', [ $this, 'register_wiki_post_type' ] );

		// Rewrite Rules for initial Characters
		add_filter('generate_rewrite_rules', [ $this, 'wppedia_cpt_generate_rewrite_rules' ] );
		add_filter('post_type_link', [ $this, 'wppedia_cpt_link' ], 10, 2);

		// Create Fake Taxonomy to query by initial letter
		add_action( 'init', [ $this, 'create_wppedia_initial_letter_tax' ] );

		// Set Initial Letter Taxonomy on post save
		add_action( 'save_post_wppedia_term', [ $this, 'manage_initial_character_onsave' ], 10, 3 );

  }

  /**
   * Register Wiki Custom Post type
   * 
	 * @uses register_post_type
	 * 
   * @since 1.0.0
   */
  public static function register_wiki_post_type() {

    $labels = [
			'name' => _x( 'Glossary', 'Post Type General Name', 'wppedia' ),
			'singular_name' => _x( 'Glossary Term', 'Post Type Singular Name', 'wppedia' ),
			'menu_name' => _x( 'Glossary', 'Admin Menu text', 'wppedia' ),
			'name_admin_bar' => _x( 'Glossary Term', 'Add New on Toolbar', 'wppedia' ),
			'archives' => __( 'Glossary Term Archives', 'wppedia' ),
			'attributes' => __( 'Glossary Term Attributes', 'wppedia' ),
			'parent_item_colon' => __( 'Parent Glossary Term:', 'wppedia' ),
			'all_items' => __( 'All Glossary', 'wppedia' ),
			'add_new_item' => __( 'Add New Glossary Term', 'wppedia' ),
			'add_new' => __( 'Add New', 'wppedia' ),
			'new_item' => __( 'New Glossary Term', 'wppedia' ),
			'edit_item' => __( 'Edit Glossary Term', 'wppedia' ),
			'update_item' => __( 'Update Glossary Term', 'wppedia' ),
			'view_item' => __( 'View Glossary Term', 'wppedia' ),
			'view_items' => __( 'View Glossary', 'wppedia' ),
			'search_items' => __( 'Search Glossary Term', 'wppedia' ),
			'not_found' => __( 'Not found', 'wppedia' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'wppedia' ),
			'featured_image' => __( 'Featured Image', 'wppedia' ),
			'set_featured_image' => __( 'Set featured image', 'wppedia' ),
			'remove_featured_image' => __( 'Remove featured image', 'wppedia' ),
			'use_featured_image' => __( 'Use as featured image', 'wppedia' ),
			'insert_into_item' => __( 'Insert into Glossary Term', 'wppedia' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Glossary Term', 'wppedia' ),
			'items_list' => __( 'Glossary list', 'wppedia' ),
			'items_list_navigation' => __( 'Glossary list navigation', 'wppedia' ),
			'filter_items_list' => __( 'Filter Glossary list', 'wppedia' ),
		];

    $rewrite = [
      'with_front'	=> false,
      'pages' 			=> true,
      'feeds' 			=> true,
		];

    $args = [
      'label' => __( 'Glossary Term', 'wppedia' ),
      'description' => __( '', 'wppedia' ),
      'labels' => $labels,
      'menu_icon' => 'dashicons-book-alt',
      'supports' => array('thumbnail', 'title', 'editor', 'excerpt', 'revisions', 'author'),
      'taxonomies' => array('wppedia_initial_letter'),
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

		if ( FALSE === helper::getInstance()->get_option( plugin_settings::$settings_general_page, 'wppedia_archive_page' ) )
			$args['has_archive'] = ltrim( rtrim( get_option( 'wppedia_permalink_base', 'glossary' ), '/' ), '/' );

		\register_post_type( 'wppedia_term', $args );

	}

	/**
	 * Register a fake Taxonomy for initial letters
	 * used to query glossary terms by initial letter
	 * 
	 * @uses register_taxonomy
	 * 
	 * @since 1.0.0
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
			'slug' => ltrim( rtrim( get_option( 'wppedia_permalink_base', 'glossary' ), '/' ), '/' ),
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

		\register_taxonomy( 'wppedia_initial_letter', [ 'wppedia_term' ], $args );

	}

	/**
	 * Set and update the initial character fake taxonomy on save
	 * 
	 * @uses wp_insert_term
	 * @uses get_terms
	 * @uses wp_delete_term
	 * 
	 * @since 1.0.0
	 */
	function manage_initial_character_onsave( int $post_ID, \WP_POST $post, bool $update ) {

		$cur_initial = helper::getInstance()->post_initial_letter( $post_ID );

		$taxonomy = 'wppedia_initial_letter';
		$cur_initial_encoded = helper::getInstance()->slugify( $cur_initial );
		
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

	function wppedia_cpt_generate_rewrite_rules( $wp_rewrite ) {

		$rules = array();

		$terms = get_terms( array(
			'taxonomy' => 'wppedia_initial_letter',
			'hide_empty' => false,
		) );
   
		$post_type = 'wppedia_term';

		foreach ($terms as $term) {    
    	$rules[ ltrim( rtrim( get_option( 'wppedia_permalink_base', 'glossary' ), '/' ), '/' ) . '/' . $term->slug . '/([^/]*)$'] = 'index.php?post_type=' . $post_type. '&name=$matches[1]';
		}

    // merge with global rules
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	
	}

	function wppedia_cpt_link( $permalink, $post ) {

		if( $post->post_type == 'wppedia_term' ) {

			$resource_terms = get_the_terms( $post, 'wppedia_initial_letter' );
			$term_slug = '';

			if( ! empty( $resource_terms ) ) {

				foreach ( $resource_terms as $term ) {
					$term_slug = $term->slug;
					break;
				}

			}

			$permalink = get_home_url() ."/" . ltrim( rtrim( get_option( 'wppedia_permalink_base', 'glossary' ), '/' ), '/' ) . '/' . $term_slug . '/' . $post->post_name;

		}

		return $permalink;

	}

}
