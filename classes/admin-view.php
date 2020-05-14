<?php

/**
 * Admin View
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class adminView {

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
    
    // Setup Admin Pages
    add_action( 'admin_menu', [ $this, 'add_wiki_admin_pages' ] );

    // Sort Wiki Entries in wp_admin
    add_action( 'pre_get_posts', [ $this, 'default_wiki_entries_orderby' ] );

  }

  /**
   * Create WP Wiki Admin Pages
   *
   * @uses: add_menu_page()
   * @uses: add_submenu_page()
   *
   * @since: 1.0.0
   */
  function add_wiki_admin_pages() {

    // Create the top level admin-page for Glossary Entries
    $wiki_main_page = add_submenu_page(
      'edit.php?post_type=wp_pedia_term',
      __('Wiki Settings', 'wppedia'), // Page Title
      __('Wiki Settings', 'wppedia'), // Menu Title
      'edit_posts', // Capability
      'wiki_settings', // Menu Slug
      [ $this, 'main_menu_cb' ], // Menu Callback
      null // Position
    );

    // Print Admin Styles
    add_action( 'admin_print_styles-' . $wiki_main_page, [$this, 'add_admin_stylesheets'] );

  }

  /**
   * Callback function for Glossary entry listing page
   * 
   * @since 1.0.0
   */
  public static function main_menu_cb() {

    // Admin Page wrapper start
    wppedia_template()->get_partial('admin-wrap-start');

    // Filter
    wppedia_template()->get_partial('admin-filter');

    // Listing
    wppedia_template()->get_partial('admin-listing');

    // Admin Page wrapper end
    wppedia_template()->get_partial('admin-wrap-end');

  }

  /**
   * Print Admin Styles
   * 
   * @since 1.0.0
   */
  function add_admin_stylesheets() {

    wp_register_style( 'wp-wiki_admin_css', wpPediaPluginUrl . '/assets/css/admin.css', false, '1.0.0' );
    wp_enqueue_style( 'wp-wiki_admin_css' );

  }

  /**
   * Set default sorting for WP List Table on wiki entries
   * 
   * @since 1.0.0
   */
  function default_wiki_entries_orderby( $query ) {

    // Be sure that we are on the Backend
    if( ! is_admin() || ! $query->is_main_query() || $query->query_vars['post_type'] !== 'wp_pedia_term' )
      return;
  
    // Orderby should not be manually modified
    if ( $query->get('orderby') == '' ) {

      $query->set( 'orderby', 'title' );
      $query->set( 'order', 'asc' );

    }

  }

}
