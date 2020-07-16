<?php

/**
 * Admin View
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class admin {

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
		
		// Hacky solution to allow custom hooks for cmb2-extension
		define( 'CMB_EXTENSIONS_ASSETS_ADDITIONAL_HOOKS', [ 'wp_pedia_term_page_wppedia_settings_general' ] );

    // Setup Admin Pages
    add_action( 'cmb2_admin_init', [ $this, 'add_wiki_admin_pages' ] );

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

		$settings_general_page = 'wppedia_settings_general';

		// Create the admin-page for Glossary Settings
    $wiki_settings_page = new_cmb2_box( [
			'id'           		=> 'wppedia_page_settings_general',
			'title'						=> __('Wiki Settings', 'wppedia'),
			'object_types'		=> [ 'options-page' ],
			'option_key'			=> $settings_general_page,
      'parent_slug'			=> 'edit.php?post_type=wp_pedia_term',
      'capability'			=> 'manage_options',
			'position'				=> null,
			'tab_style' 			=> 'default',
			'tabs' 						=> [
				'content' => [
					'label' => __('Content', 'wppedia'),
					'icon' 	=> 'dashicons-text-page', // Dashicon
				],
				'style' => [
					'label' => __('CSS & JavaScript', 'wppedia'),
					'icon' 	=> 'dashicons-admin-customizer', // Dashicon
				],
			],
		] );

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
