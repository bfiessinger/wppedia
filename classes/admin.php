<?php

/**
 * Admin View
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\options\plugin_settings;

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

		// Add Text to the glossary archive page
		add_action( 'display_post_states', [ $this, 'wppedia_archive_post_state' ], 10, 2 );

    // Sort Wiki Entries by postname
    add_action( 'pre_get_posts', [ $this, 'default_wiki_entries_orderby' ] );

  }

	/**
	 * Modify the posts state for the glossary Archive Page
	 * 
	 * @since 1.0.0
	 */
	function wppedia_archive_post_state( $post_states, $post ) {

		if( $post->ID == wppedia_utils()->get_option( plugin_settings::$settings_general_page, 'wppedia_archive_page' ) ) {
			$post_states[] = __( 'Glossary', 'wppedia' );
		}
	
		return $post_states;

	}

  /**
   * Set default sorting for WP List Table on wiki entries
   * 
   * @since 1.0.0
   */
  function default_wiki_entries_orderby( $query ) {

    // Be sure that we are on the Backend
    if( ! is_admin() || ! $query->is_main_query() || $query->query_vars['post_type'] !== 'wppedia_term' )
      return;
  
    // Orderby should not be manually modified
    if ( $query->get('orderby') == '' ) {

      $query->set( 'orderby', 'title' );
      $query->set( 'order', 'asc' );

    }

  }

}
