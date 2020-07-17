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

		// Custom Permalinks Section
		add_action('admin_init', [ $this, 'wppedia_permalink_settings' ], 999999 );

    // Sort Wiki Entries in wp_admin
    add_action( 'pre_get_posts', [ $this, 'default_wiki_entries_orderby' ] );

  }

  /**
   * Create WP Wiki Admin Pages
   *
   * @uses new_cmb2_box()
	 * @see https://cmb2.io/
   *
   * @since 1.0.0
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
	 * Add a custom options section to the permalinks admin screen
	 * 
	 * @uses add_settings_section()
	 * 
	 * @since 1.0.0
	 */
	function wppedia_permalink_settings() {

		add_option( 
			'wppedia_permalink_base', 
			'/glossary/', 
			'', 
			true 
		);

		add_option( 
			'wppedia_permalink_term_base', 
			'/term/', 
			'', 
			true 
		);

		// Register glossary Base Setting
		register_setting(
			'permalink', 
			'wppedia_permalink_base',
			[ $this, 'wppedia_permalink_part_sanitize' ]
		);

		// Register glossary Term Permalink Setting
		register_setting(
			'permalink', 
			'wppedia_permalink_term_base',
			[ $this, 'wppedia_permalink_part_sanitize' ]
		);

		add_settings_section(
			'wppedia_permalink_structure', // ID
			__( 'WPPedia Permalinks', 'wppedia' ),// Section title
			[ $this, 'wppedia_permalink_settings_cb' ], // Callback for your function
			'permalink' // Location (Settings > Permalinks)
		);

		add_settings_field( 
			'wppedia_permalink_base_setting', 
			__( 'WPPedia base', 'wppedia' ), 
			[ $this, 'wppedia_setting_permalink_base_cb' ], 
			'permalink', 
			'wppedia_permalink_structure'
		);

		add_settings_field( 
			'wppedia_permalink_term_base_setting', 
			__( 'WPPedia Term base', 'wppedia' ), 
			[ $this, 'wppedia_setting_permalink_term_base_cb' ], 
			'permalink', 
			'wppedia_permalink_structure'
		);

		// Save options to database
		if ( isset( $_POST['wppedia_permalink_base'] ) || isset( $_POST['wppedia_permalink_term_base'] ) ) {

			check_admin_referer('update-permalink');

			$option_page = 'permalink';

			$capability = 'manage_options';
			$capability = apply_filters( "option_page_capability_{$option_page}", $capability );

			if ( !current_user_can( $capability ) )
				wp_die(__('Cheatin&#8217; uh?'));

			if ( isset( $_POST['wppedia_permalink_base'] ) )
				update_option( 'wppedia_permalink_base', $_POST['wppedia_permalink_base'] );

			if ( isset( $_POST['wppedia_permalink_term_base'] ) )
				update_option( 'wppedia_permalink_term_base', $_POST['wppedia_permalink_term_base'] );

		}

	}

	function wppedia_permalink_settings_cb() {
		echo wpautop( __( 'If you like, you may enter custom structures for your WPPedia URLs here.', 'wppedia' ) );
	}

	function wppedia_setting_permalink_base_cb() { ?>
		<input type="text" name="wppedia_permalink_base" value="<?php echo get_option('wppedia_permalink_base'); ?>" class="regular-text code" />
	<?php	}

	function wppedia_setting_permalink_term_base_cb() { ?>
		<input type="text" name="wppedia_permalink_term_base" value="<?php echo get_option('wppedia_permalink_term_base'); ?>" class="regular-text code" />
	<?php	}

	function wppedia_permalink_part_sanitize( $input ) {

		$sanitized = $input;

		$inputLen = strlen( $input );
		if ( \strpos($input, '/') === false || \strpos($input, '/') != 0 )
			$sanitized = '/' . $sanitized;

		if ( \strpos($input, '/', 1) != $inputLen - 1 )
			$sanitized .= '/';

		return $sanitized;

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
