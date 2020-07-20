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
	 * Public Variables
	 */
	public static $settings_general_page = 'wppedia_settings_general';

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

		// Add Text to the glossary archive page
		add_action( 'display_post_states', [ $this, 'wppedia_archive_post_state' ], 10, 2 );

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

		// Create the admin-page for Glossary Settings
    $wiki_settings_page = new_cmb2_box( [
			'id'           		=> 'wppedia_page_settings_general',
			'title'						=> __('Wiki Settings', 'wppedia'),
			'object_types'		=> [ 'options-page' ],
			'option_key'			=> self::$settings_general_page,
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

		$wiki_settings_page->add_field( [
			'name'          => __( 'Glossary Page', 'wppedia' ),
			'desc'          => __( 'Select the page that is used to display the glossary archive.', 'wppedia' ),
			'id'            => 'wppedia_archive_page',
			'type'          => 'ajax_search',
			'tab'  			=> 'content',
			'search'		=> 'post',
			'query_args'	=> [
				'post_type'			=> [ 'page' ],
				'posts_per_page'	=> -1
			]
		] );

		$wiki_settings_page->add_field( [
			'name'			=> __( 'Activate Crosslinking', 'wppedia' ),
			'desc'			=> __( 'Allow WPPedia to automatically generate links to other articles if their name was found on a glossary term.','wppedia' ),
			'id'				=> 'wppedia_crosslinking_active',
			'type'			=> 'switch_button',
			'default'		=> 'on',
			'tab'				=> 'content',
		] );

		$wiki_settings_page->add_field( [
			'name'			=> __( 'Prefer Single Words', 'wppedia' ),
			'desc'			=> __( 'Enabling this option will change the default behaviour of crosslinking and WPPedia tries to link single words instead of multiple if possible. e.g. if there is a post "Lorem" and a post "Lorem Ipsum", the plugin will link only "Lorem" now if "Lorem Ipsum" was found in the content.','wppedia' ),
			'id'				=> 'wppedia_crosslinking_prefer-single-words',
			'type'			=> 'switch_button',
			'tab'				=> 'content',
		] );

		$wiki_settings_page->add_field( [
			'name'			=> __( 'Load base CSS', 'wppedia' ),
			'desc'			=> __( 'Enqueue the base CSS Stylesheet.','wppedia' ),
			'id'				=> 'wppedia_enqueue_base_style',
			'type'			=> 'switch_button',
			'default'		=> 'on',
			'tab'				=> 'style',
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
	 * Modify the posts state for the glossary Archive Page
	 * 
	 * @since 1.0.0
	 */
	function wppedia_archive_post_state( $post_states, $post ) {

		if( $post->ID == wppedia_utils()->get_option( self::$settings_general_page, 'wppedia_archive_page' ) ) {
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
    if( ! is_admin() || ! $query->is_main_query() || $query->query_vars['post_type'] !== 'wp_pedia_term' )
      return;
  
    // Orderby should not be manually modified
    if ( $query->get('orderby') == '' ) {

      $query->set( 'orderby', 'title' );
      $query->set( 'order', 'asc' );

    }

  }

}
