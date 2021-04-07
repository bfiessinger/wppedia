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
	 * Private variables
	 */
	private static $pro_feature_className = 'wppedia-pro-feature';

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

		// Main Plugin Settings
		add_action( 'admin_menu', [ $this, 'settings_page' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

		// Custom Permalinks Section
		add_action( 'admin_init', [ $this, 'wppedia_permalink_settings' ], 999999 );

		// Admin Page Assets
		add_action( 'admin_enqueue_scripts', [ $this, 'do_admin_scripts' ] );

		// Set flush rewrite rules flag for some options
		add_action( 'update_option_wppedia_frontpage', [ $this, 'set_flush_rewrite_rules_flag' ], 10, 2 );
		
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
				<?php do_action('wppedia_admin_settings_page_header_content'); ?>
			</div>

			<div class="wppedia-layout-wrap">

				<h1 class="screen-reader-text"><?php echo _x('WPPedia Settings', 'options', 'wppedia'); ?></h1>
				<?php settings_errors(); ?>

				<div class="wppedia-layout-flex-container">
					<div class="wppedia-layout-content">

						<form method="post" action="options.php">
							<?php settings_fields( 'wppedia_settings_general' ); ?>
							<?php $this->do_settings_sections_tabbed( 'wppedia_settings_general', true ); ?>
							<?php submit_button(); ?>
						</form>
					</div>

					<div class="wppedia-layout-sidebar">
						<div class="wppedia-sidebar-widget">
							<img src="<?php echo wpPediaPluginUrl; ?>assets/img/WPPedia-pro-teaser.png" width="200" />
						</div>
					</div>
				</div>

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
				'options' => $this->dropdown_pages(true),
				'desc' => _x( 'Select the page that is used to display the glossary archive.', 'options', 'wppedia' ),
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
				'switch' => true,
				'desc' => _x( 'Allow WPPedia to automatically generate links to other articles if their name was found on a glossary term.', 'options', 'wppedia' ),
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
				'switch' => true,
				'desc' => _x( 'Enabling this option will change the default behaviour of crosslinking and WPPedia tries to link single words instead of multiple if possible. e.g. if there is a post "Lorem" and a post "Lorem Ipsum", the plugin will link only "Lorem" now if "Lorem Ipsum" was found in the content.', 'options', 'wppedia' ),
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
				'options' => $this->get_public_posttypes(),
				'class' => self::$pro_feature_className
			]
		);

		// Settings field: Crosslink Index
		add_settings_field(
			'wppedia_crosslinks_index',
			_x('Crosslink Index', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_crosslinks',
			[
				'id' => 'wppedia_crosslinks_index',
				'class' => self::$pro_feature_className,
				'switch' => true,
				'desc' => _x('If enabled WPPedia will create automatic indexes with all links created for each post. This ensures a significant faster loading time!', 'options', 'wppedia')
			]
		);

		// Settings section: Archive
		add_settings_section( 
			'wppedia_archive_settings', 
			_x('Archive settings', 'options', 'wppedia'), 
			[ $this, 'settings_section_callback' ], 
			'wppedia_settings_archive'
		);

		// Settings section: Single articles
		add_settings_section(
			'wppedia_settings_singular',
			_x('Single article settings', 'options', 'wppedia'),
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

		$pro_only = (isset($args['class']) && false !== strpos($args['class'], self::$pro_feature_className)) ? true : false;

		$option = $args['id'];
		$values = $args['options'];
			
		// Render the field
		echo '<select name="' . $option . '" id="' . $option . '"' . $this->restrict_pro($pro_only) . '>';
		foreach ($values as $value => $label) {
			echo '<option value="' . $value . '" ' . selected( get_option($option), $value, true ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';

		// Show field description
		if (isset($args['desc']) && '' !== $args['desc']) {
			echo '<div class="wppedia-option-description">';
			echo $args['desc'];
			echo '</div>';
		}
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

		$pro_only = (isset($args['class']) && false !== strpos($args['class'], self::$pro_feature_className)) ? true : false;

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

		// Render the field
		echo '<input name="' . $option_id . '" id="' . $option_id . '" type="checkbox" value="1" ' . checked( $get_option, true, false );
		if ($switch) {
			echo ' class="wppedia-switch-button"';
		}
		echo $this->restrict_pro($pro_only) . '>';

		// Show field description
		if (isset($args['desc']) && '' !== $args['desc']) {
			echo '<div class="wppedia-option-description">';
			echo $args['desc'];
			echo '</div>';
		}
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

		// Render the field
		echo '<div class="wppedia-checkbox-group">';

		foreach($values as $v) {
			$key = (isset($v['key'])) ? $v['key'] : $v;
			$label = (isset($v['label'])) ? $v['label'] : $v;

			$checkbox_args = [
				'id' => $option,
				'key' => $key,
				'desc' => null // null description to avoid duplicates
			];

			$checkbox_args = array_merge($args, $checkbox_args);

			echo '<div class="wppedia-checkbox-group-item">';
			$this->create_checkbox($checkbox_args);
			echo '<label for="' . $option . '[' . $key . ']">' . $label . '</label>';
			echo '</div>';
		}

		echo '</div>';

		// Show field description
		if (isset($args['desc']) && '' !== $args['desc']) {
			echo '<div class="wppedia-option-description">';
			echo $args['desc'];
			echo '</div>';
		}
	}

	/**
	 * Return disabled attribute for pro features
	 * 
	 * @param bool $disable
	 * 
	 * @since 1.0.0
	 */
	private function restrict_pro(bool $disable = false) {
		if (!$disable)
			return;
		
		return apply_filters('__wppedia_settings_set_disabled_attribute', ' disabled="disabled"');
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
			$options[''] = '-';
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
		if ( $hook === 'wppedia_term_page_wppedia_settings_general' || $hook === 'options-permalink.php' ) {
			wp_enqueue_style( 'wppedia-admin', wpPediaPluginUrl . 'dist/css/admin.min.css', wppedia_get_version(), null );
		}
	}

	/**
	 * Set a flag for flushing rewrite rules on the next
	 * pageload
	 * 
	 * @since 1.0.0
	 */
	function set_flush_rewrite_rules_flag($old_value, $value) {
		if ( $old_value !== $value && ! get_option( 'wppedia_flush_rewrite_rules_flag' ) ) {
			add_option( 'wppedia_flush_rewrite_rules_flag', true );
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

		add_settings_section(
			'wppedia_permalink_structure', // ID
			__( 'WPPedia Permalinks', 'wppedia' ),// Section title
			[ $this, 'wppedia_permalink_settings_cb' ], // Callback for your function
			'permalink' // Location (Settings > Permalinks)
		);

		/**
		 * Glossary permalink base setting
		 */
		add_settings_field( 
			'wppedia_permalink_base_setting', 
			__( 'WPPedia base', 'wppedia' ), 
			[ $this, 'wppedia_setting_permalink_base_cb' ], 
			'permalink', 
			'wppedia_permalink_structure'
		);

		register_setting(
			'permalink', 
			'wppedia_permalink_base',
			[ $this, 'wppedia_permalink_part_sanitize' ]
		);

		/**
		 * Settings field for using the initial character in the URL
		 */
		add_settings_field(
			'wppedia_permalink_use_initial_character', 
			__( 'use initial character in URL', 'wppedia' ), 
			[ $this, 'create_checkbox' ], 
			'permalink', 
			'wppedia_permalink_structure',
			[
				'id' => 'wppedia_permalink_use_initial_character',
				'switch' => true,
				'class' => self::$pro_feature_className,
			]
		);

		register_setting(
			'permalink',
			'wppedia_use_initial_character_permalink'
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