<?php

/**
 * Admin View
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class options {

	/**
	 * Public Variables
	 */
	public static $settings_general_page = 'wppedia_settings_general';

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

		add_action( 'admin_menu', [ $this, 'settings_page' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

		// Admin Page Assets
		add_action( 'admin_enqueue_scripts', [ $this, 'do_admin_scripts' ] );
		
	}
	
	function settings_page() {
		add_submenu_page( 
			'edit.php?post_type=' . \wppedia_get_post_type(), 
			'WPPedia Settings',
			'WPPedia Settings', 
			'manage_options', 
			'wppedia_settings_general', 
			[ $this, 'settings_cb' ],
			null
		);
	}

	function settings_cb() { ?>
		<div class="wrap">
			<div class="wppedia-layout-header">
				<img class="wppedia-logo" src="<?php echo wpPediaPluginUrl; ?>assets/img/wppedia-logo.svg" width="60">
			</div>

			<div class="wppedia-layout-content">

				<h1 class="screen-reader-text"><?php echo _x('WPPedia Settings', 'options', 'wppedia'); ?></h1>

				<?php settings_errors(); ?>

				<form method="post" action="options.php">
					<?php settings_fields( 'wppedia_settings_general' ); ?>
					<?php $this->do_settings_sections_tabbed( 'wppedia_settings_general', true ); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
	<?php }

	function settings_init() {

		// Settings section: Glossary front page
		add_settings_section( 
			'wppedia_settings_page', 
			_x('Page Settings', 'options', 'wppedia'), 
			[ $this, 'settings_section_callback' ], 
			'wppedia_settings_general' 
		);

		// Settings field: Front page
		add_settings_field( 
			'wppedia_frontpage', 
			_x('Glossary frontpage', 'options', 'wppedia'),
			[ $this, 'create_select' ],
			'wppedia_settings_general', 
			'wppedia_settings_page',
			[
				'id' => 'wppedia_frontpage',
				'options' => $this->dropdown_pages(true)
			]
		);

		register_setting(
			'wppedia_settings_general', 
			'wppedia_frontpage'
		);

		// Settings section: Crosslinking
		add_settings_section(
			'wppedia_settings_crosslinks',
			_x('Crosslinking', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings_general'
		);

		// Settings field: Activate crosslinking
		add_settings_field(
			'wppedia_feature_crosslinks',
			_x( 'Activate Crosslinking', 'options', 'wppedia' ),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_crosslinks',
			[
				'id' => 'wppedia_feature_crosslinks', 
				'switch' => true
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_feature_crosslinks'
		);

		// Settings field: prefer single words for crosslinks
		add_settings_field(
			'wppedia_crosslinks_prefer_single_words',
			_x( 'Prefer single words', 'options', 'wppedia' ),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_crosslinks',
			[
				'id' => 'wppedia_crosslinks_prefer_single_words', 
				'switch' => true
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_crosslinks_prefer_single_words'
		);

		// Settings field: Create crosslinks for posttypes
		add_settings_field(
			'wppedia_crosslinks_posttypes',
			_x( 'Create crosslinks to post types', 'options', 'wppedia' ),
			[ $this, 'create_checkbox_group' ],
			'wppedia_settings_general',
			'wppedia_settings_crosslinks',
			[
				'id' => 'wppedia_crosslinks_posttypes',
				'options' => $this->get_public_posttypes()
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_crosslinks_posttypes',
			[ $this, 'sanitize_checkbox_group' ]
		);

		// Settings section: Archive
		add_settings_section( 
			'wppedia_archive_settings', 
			__('Archive settings', 'wppedia'), 
			[ $this, 'settings_section_callback' ], 
			'wppedia_settings_archive'
		);

		// Settings section: Single articles
		add_settings_section(
			'wppedia_settings_singular',
			__('Single article settings', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings_singular'
		);

	}

	function settings_section_callback($section) {
		switch ($section['id']) {
			case 'wppedia_settings_page':
				echo '<p>testing page settings section</p>';
				break;
			case 'wppedia_settings_crosslinks':
				echo '<p>testing crosslinks section</p>';
			default:
				break;
		}		
	}

	/**
	 * Create a select field
	 * 
	 * @param string $option - option name
	 * @param array $values - possible select values
	 * 
	 * @since 1.0.0
	 */
	function create_select(array $args) {
		if (
			!isset($args['id']) || 
			!isset($args['options']) || 
			!count($args['options'])
		)
			return;

		$option = $args['id'];
		$values = $args['options'];
			
		echo '<select name="' . $option . '" id="' . $option . '">';
		foreach ($values as $value => $label) {
			echo '<option value="' . $value . '" ' . selected( get_option($option), $value, true ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Create a checkbox field
	 * 
	 * @param string $option - option name
	 * @param bool $switch - render the checkbox as a switch button
	 * 
	 * @since 1.0.0
	 */
	function create_checkbox(array $args) {
		if (!isset($args['id']) || (!isset($args['key']) && !isset($args['id'])))
			return;

		$option = $args['id'];
		$option_id = (isset($args['id']) && isset($args['key'])) ? $args['id'] . '[' . $args['key'] . ']' : $option;

		$switch = (isset($args['switch']) && false !== $args['switch']) ? true : false;

		$get_option = maybe_unserialize(get_option($option, false));
		if (is_array($get_option)) {
			if (in_array($args['key'], $get_option)) {
				$get_option = true;
			} else {
				$get_option = false;
			}
		}

		echo '<input name="' . $option_id . '" id="' . $option_id . '" type="checkbox" value="1" ' . checked( $get_option, true, false );
		if ($switch) {
			echo ' class="wppedia-switch-button"';
		}
		echo '>';
	}

	function create_checkbox_group(array $args) {
		if (
			!isset($args['id']) || 
			!isset($args['options']) || 
			!count($args['options'])
		)
			return;

		$option = $args['id'];
		$values = $args['options'];

		if (!count($values))
			return;

		echo '<div class="wppedia-checkbox-group">';

		foreach($values as $v) {
			$key = (isset($v['key'])) ? $v['key'] : $v;
			$label = (isset($v['label'])) ? $v['label'] : $v;

			echo '<div class="wppedia-checkbox-group-item">';
			$this->create_checkbox([
				'id' => $option,
				'key' => $key
			]);
			echo '<label for="' . $option . '[' . $key . ']">' . $label . '</label>';
			echo '</div>';
		}

		echo '</div>';
	}

	function sanitize_checkbox_group($data) {
		return array_keys($data);
	} 

	/**
	 * Custom implementation of do_settings_sections for usage
	 * with tabs
	 * 
	 * @see https://developer.wordpress.org/reference/functions/do_settings_sections/
	 * 
	 * @since 1.0.0
	 */
	private function do_settings_sections_tabbed( $page, bool $vertical = false ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}
		
		// Enqueue required scripts
		wp_enqueue_script("jquery");
		wp_enqueue_script("jquery-ui-core");
		wp_enqueue_script("jquery-ui-tabs");
		wp_add_inline_script( 
			'jquery-ui-tabs',
			'jQuery("document").ready(function() {
				jQuery( ".wppedia-settings-tabs" ).tabs();
			});'
		);

		echo '<div class="wppedia-settings-tabs';
		if ($vertical) {
			echo ' ui-tabs-vertical';
		}
		echo '">';

		// Build Tabs HTML
		echo '<ul class="wppedia-settings-tabs-wrapper">';
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {

			echo '<li class="wppedia-settings-tab">';
			echo '<a href="#settings_tab_' . $section['id'] . '">' . $section['title'] . '</a>';
			echo '</li>';

		}
		echo '</ul>';

		// Build Tab content HTML
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			
			echo '<div class="wppedia-settings-tab-content" id="settings_tab_' . $section['id'] . '">';
			
			if ( $section['title'] ) {
				echo "<h2>{$section['title']}</h2>\n";
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}

			echo '<table class="form-table" role="presentation">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
				
			echo '</div>';
			
		}

		echo '</div>';

	}

	/**
	 * Custom options Callback for selecting Pages
	 * 
	 * @since 1.0.0
	 */
	function dropdown_pages(bool $add_option_none = false) {

		$options = [];
		$pages = get_pages();

		if ($add_option_none) {
			$options[] = '-';
		}
	
		foreach ( $pages as $page ) {
			$options[$page->ID] = get_the_title( $page->ID );
		}

		return $options;

	}

	/**
	 * Custom options Callback to get all public posttypes
	 * 
	 * @since 1.0.0
	 */
	function get_public_posttypes() {

		$return_arr = [];
		$post_types = get_post_types( [
			'public' => true,
		] );

		foreach ( $post_types as $pt ) {

			$obj = get_post_type_object( $pt );
			$return_arr[] = [
				'key' => $pt,
				'label' => $obj->labels->name 
			];

		}

		return $return_arr;

	}

	/**
	 * Add admin scripts and styles
	 * 
	 * @since 1.0.0
	 */
	function do_admin_scripts( $hook ) {
		if ( 'wppedia_term_page_wppedia_settings_general' === $hook ) {
			wp_enqueue_style( 'wppedia-admin', wpPediaPluginUrl . 'dist/css/admin.min.css', wppedia_get_version(), null );
		}
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
			'glossary', 
			'', 
			true 
		);

		/**
		 * Glossary permalink base setting
		 */
		register_setting(
			'permalink', 
			'wppedia_permalink_base',
			[ $this, 'wppedia_permalink_part_sanitize' ]
		);

		/**
		 * Settings field for using the initial character in the URL
		 */
		register_setting(
			'permalink',
			'wppedia_use_initial_character_permalink'
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
			'wppedia_permalink_use_initial_character', 
			__( 'use initial character in URL', 'wppedia' ), 
			[ $this, 'wppedia_setting_permalink_use_initial_character_cb' ], 
			'permalink', 
			'wppedia_permalink_structure'
		);

		// Save options to database
		if ( isset( $_POST['wppedia_permalink_base'] ) || isset( $_POST['wppedia_permalink_use_initial_character'] ) ) {

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

	function wppedia_setting_permalink_use_initial_character_cb() { ?>
		<input type="checkbox" name="wppedia_permalink_use_initial_character" checked value="on" />
	<?php }

	function wppedia_permalink_part_sanitize( $input ) {

		// Add leading slash to prevent `esc_url_raw` adding a protocol
		$input = '/' . $input;
		// replace all whitespaces with `-`
		$input = preg_replace( '/\s+/', '-', $input );
		$input = esc_url_raw( $input, null );
		// Remove leading slash
		$input = substr($input, 1);

		return $input;
	}

}