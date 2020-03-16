<?php

/**
 * wpPedia Post Type related
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class wikiPostType {

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

    add_action( 'init', [$this, 'register_wiki_post_type'] );

  }

  /**
   * Register CPT
   * 
   * @since 1.0.0
   */
  public static function register_wiki_post_type() {

    $labels = array(
      'name' => _x( 'wpPedia Entries', 'Post Type General Name', 'wppedia' ),
      'singular_name' => _x( 'wpPedia Entry', 'Post Type Singular Name', 'wppedia' ),
      'menu_name' => _x( 'wpPedia Entries', 'Admin Menu text', 'wppedia' ),
      'name_admin_bar' => _x( 'wpPedia Entry', 'Add New on Toolbar', 'wppedia' ),
      'archives' => __( 'wpPedia Entry Archives', 'wppedia' ),
      'attributes' => __( 'wpPedia Entry Attributes', 'wppedia' ),
      'parent_item_colon' => __( 'Parent wpPedia Entry:', 'wppedia' ),
      'all_items' => __( 'All wpPedia Entries', 'wppedia' ),
      'add_new_item' => __( 'Add New wpPedia Entry', 'wppedia' ),
      'add_new' => __( 'Add New', 'wppedia' ),
      'new_item' => __( 'New wpPedia Entry', 'wppedia' ),
      'edit_item' => __( 'Edit wpPedia Entry', 'wppedia' ),
      'update_item' => __( 'Update wpPedia Entry', 'wppedia' ),
      'view_item' => __( 'View wpPedia Entry', 'wppedia' ),
      'view_items' => __( 'View wpPedia Entries', 'wppedia' ),
      'search_items' => __( 'Search wpPedia Entry', 'wppedia' ),
      'not_found' => __( 'Not found', 'wppedia' ),
      'not_found_in_trash' => __( 'Not found in Trash', 'wppedia' ),
      'featured_image' => __( 'Featured Image', 'wppedia' ),
      'set_featured_image' => __( 'Set featured image', 'wppedia' ),
      'remove_featured_image' => __( 'Remove featured image', 'wppedia' ),
      'use_featured_image' => __( 'Use as featured image', 'wppedia' ),
      'insert_into_item' => __( 'Insert into wpPedia Entry', 'wppedia' ),
      'uploaded_to_this_item' => __( 'Uploaded to this wpPedia Entry', 'wppedia' ),
      'items_list' => __( 'wpPedia Entries list', 'wppedia' ),
      'items_list_navigation' => __( 'wpPedia Entries list navigation', 'wppedia' ),
      'filter_items_list' => __( 'Filter wpPedia Entries list', 'wppedia' ),
    );

    $rewrite = array(
      'slug' => 'wiki',
      'with_front' => true,
      'pages' => true,
      'feeds' => true,
    );

    $args = array(
      'label' => __( 'wpPedia Entry', 'wppedia' ),
      'description' => __( '', 'wppedia' ),
      'labels' => $labels,
      'menu_icon' => 'dashicons-book-alt',
      'supports' => array('title', 'editor', 'excerpt', 'revisions', 'author'),
      'taxonomies' => array(),
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
      'rewrite' => $rewrite,
    );

    register_post_type( 'wp_pedia_term', $args );

  }

}
