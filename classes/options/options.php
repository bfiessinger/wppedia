<?php

/**
 * WP Options
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class options {

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

		// Custom Permalinks Section
		add_action('admin_init', [ $this, 'wppedia_permalink_settings' ], 999999 );

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

		// Register glossary Base Setting
		register_setting(
			'permalink', 
			'wppedia_permalink_base',
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

		}

	}

	function wppedia_permalink_settings_cb() {
		echo wpautop( __( 'If you like, you may enter custom structures for your WPPedia URLs here.', 'wppedia' ) );
	}

	function wppedia_setting_permalink_base_cb() { ?>
		<input type="text" name="wppedia_permalink_base" value="<?php echo get_option('wppedia_permalink_base'); ?>" class="regular-text code" />
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

}