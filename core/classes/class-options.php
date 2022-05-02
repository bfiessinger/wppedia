<?php

/**
 * Admin View
 *
 * @since 1.3.0
 */

namespace WPPedia;

use WPPedia\Traits\Has_Admin_Fields;
use WPPedia\Traits\Sanitizes_Data;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class Options {

	use Has_Admin_Fields;
	use Sanitizes_Data;

	/**
	 * Private variables
	 */
	private static $pro_feature_className = 'wppedia-pro-feature';
	private $settings_fields = [];

  	public function _init() {

		// Main Plugin Settings
		add_action( 'admin_menu', [ $this, 'settings_page' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

		// Custom Permalinks Section
		add_action( 'admin_init', [ $this, 'wppedia_permalink_settings_save' ], 999999 );

		// Set flush rewrite rules flag for options related to permalinks
		add_action( 'update_option_wppedia_settings', [ $this, 'maybe_set_flush_rewrite_rules_flag' ], 10, 3 );

		add_action( 'update_option_wppedia_settings', [ $this, 'update_option_wppedia_settings' ], 10, 3 );
	}

	function settings_page() {
		add_submenu_page(
			'edit.php?post_type=' . \wppedia_get_post_type(),
			'WPPedia Settings',
			'WPPedia Settings',
			'manage_options',
			'wppedia_settings',
			[ $this, 'settings_cb' ],
			null
		);
	}

	/**
	 * Callback to display the setting page
	 *
	 * @since 1.2.0
	 */
	function settings_cb() { ?>
		<div class="wrap">
			<div class="wppedia-layout-header">
				<?php do_action('wppedia_admin_settings_page_header_content'); ?>
			</div>

			<div class="wppedia-layout-wrap">

				<h1 class="screen-reader-text"><?php echo _x('WPPedia Settings', 'options', 'wppedia'); ?></h1>
				<?php settings_errors(); ?>

				<form method="post" action="options.php" class="wppedia-layout-flex-container">
					<div class="wppedia-layout-content">
						<div>
							<?php settings_fields( 'wppedia_settings' ); ?>
							<?php $this->do_settings_sections_tabbed( 'wppedia_settings', true ); ?>
						</div>
					</div>

					<div class="wppedia-layout-sidebar">
						<div class="wppedia-sidebar-widget">
							<img src="<?php echo WPPediaPluginUrl; ?>assets/img/WPPedia-pro-teaser.png" width="200" />
							<?php submit_button(); ?>
						</div>
					</div>
				</form>

			</div>

		</div>
	<?php }

	/**
	 * Return WPPedia default option as an array
	 * If $option parameter is set return the option value
	 * associated with the option name.
	 *
	 * @param string $option - option name
	 *
	 * @since 1.1.6
	 */
	static function get_option_defaults(string $option_group = null, string $option = null) {
		$defaults = [
			'general' => [
				'front_page_id' => 0,
			],
			'archive' => [
				'wppedia_templates' => true,
				'show_nav' => true,
				'show_searchbar' => true,
				'posts_per_page' => 25,
			],
			'singular' => [
				'wppedia_templates' => true,
				'show_nav' => false,
				'show_searchbar' => false,
			],
			'crosslinks' => [
				'active' => true,
				'prefer_single_words' => false,
				'posttypes' => [
					\wppedia_get_post_type() => true
				]
			],
			'tooltips' => [
				'active' => true,
				'style' => 'light'
			],
			'permalinks' => [
				'base' => 'glossary',
				'use_initial_character' => false
			],
		];

		if (!$option_group && !$option) {
			return $defaults;
		} else if ($option_group && !$option) {
			return $defaults[$option_group];
		}

		return isset($defaults[$option_group][$option]) ? $defaults[$option_group][$option] : null;
	}

	public static function get_option(string $option_group, string $option) {
		if (false == get_option('wppedia_settings', false) || !isset(get_option('wppedia_settings')[$option_group]) || !isset(get_option('wppedia_settings')[$option_group][$option])) {
			return self::get_option_defaults($option_group, $option);
		}

		return get_option('wppedia_settings')[$option_group][$option];
	}

	public static function option_exists(string $option_group, string $option) {
		return isset(get_option('wppedia_settings')[$option_group][$option]);
	}

	public static function default_option_exists(string $option_group, string $option) {
		$defaults = self::get_option_defaults();
		return isset($defaults[$option_group][$option]);
	}

	public static function update_option(string $option_group, string $option, $value) {
		$options = get_option('wppedia_settings');

		if (!self::default_option_exists($option_group, $option)) {
			return;
		}

		$options[$option_group][$option] = $value;
		update_option('wppedia_settings', $options);
	}

	public static function remove_option(string $option_group, string $option) {
		$options = get_option('wppedia_settings');

		if (!self::default_option_exists($option_group, $option)) {
			return;
		}

		unset($options[$option_group][$option]);
		update_option('wppedia_settings', $options);
	}

	/**
	 * Initialize settings sections and fields
	 *
	 * @since 1.3.0
	 */
	function settings_init() {

		/**
		 * General settings section
		 */

		add_settings_section(
			'general',
			_x('General Settings', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings'
		);

		register_setting(
			'wppedia_settings',
			'wppedia_settings',
			[
				'type'		=> 'array',
				'default' 	=> self::get_option_defaults(),
				'sanitize_callback' => [ $this, 'wppedia_settings_save' ],
			]
		);

		/**
		 * Archive settings section
		 */

		add_settings_section(
			'archive',
			_x('Archive Settings', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings'
		);

		/**
		 * Singular settings section
		 */

		add_settings_section(
			'singular',
			_x('Singular Settings', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings'
		);

		/**
		 * Crosslink settings section
		 */

		add_settings_section(
			'crosslinks',
			_x('Crosslinking', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings'
		);

		/**
		 * Tooltip settings section
		 */

		add_settings_section(
			'tooltips',
			_x('Tooltips', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings'
		);

		/**
		 * Permalink Settings
		 */

		// Settings Section: Permalinks
		add_settings_section(
			'wppedia_settings_permalink',
			_x( 'WPPedia Permalinks', 'options', 'wppedia' ),
			[ $this, 'settings_section_callback' ],
			'permalink'
		);

		$this->settings_fields = [
			/**
			 * General Settings
			 */

			// WPPedia frontpage
			[
				'id'								=> 'front_page_id',
				'label'								=> _x('Glossary frontpage', 'options', 'wppedia'),
				'type'								=> 'select',
				'desc'								=> sprintf(_x('By default, WPPedia creates a simple archive page using the slug defined at %s. Using this option allows you to have more control over the frontpage of your glossary.', 'options', 'wppedia'), '<a href="' . admin_url('/options-permalink.php') . '" target="_blank">' . __('Permalinks') . '</a>'),
				'options'							=> $this->dropdown_pages(true),
				'settings_section'					=> 'general',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_int' ],
			],

			/**
			 * Archive Settings
			 */

			// Use WPPedia templates in archives
			[
				'id'								=> 'wppedia_templates',
				'label'								=> _x('use WPPedia Templates', 'options', 'wppedia'),
				'type'								=> 'switch',
				'desc'								=> _x('If disabled WPPedia the Layout and content of WPPedia\'s Archive will be defined by your themes templates. Attention: most WPPedia template filters and actions will stop working on Archive pages. This option might help if you encounter any incompatibilities between your theme and WPPedia\'s default templates.', 'options', 'wppedia'),
				'settings_section'					=> 'archive',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Show navigation in archives
			[
				'id'								=> 'show_nav',
				'label'								=> _x('Show navigation', 'options', 'wppedia'),
				'type'								=> 'switch',
				'desc'								=> _x('Show or hide WPPedia\'s navigation on archive pages.', 'options', 'wppedia'),
				'settings_section'					=> 'archive',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Show searchbar in archives
			[
				'id'								=> 'show_searchbar',
				'label'								=> _x('Show searchbar', 'options', 'wppedia'),
				'type'								=> 'switch',
				'desc'								=> _x('Show or hide WPPedia\'s searchbar on archive pages.', 'options', 'wppedia'),
				'settings_section'					=> 'archive',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Default posts_per_page
			[
				'id'								=> 'posts_per_page',
				'label'								=> _x('Posts per page', 'options', 'wppedia'),
				'type'								=> 'number',
				'desc'								=> _x('Manage how much posts should be available per page at a glossary archive', 'options', 'wppedia'),
				'settings_section'					=> 'archive',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_int' ],
			],

			/**
			 * Singular Settings
			 */

			// Use WPPedia templates in single articles
			[
				'id'								=> 'wppedia_templates',
				'label'								=> _x('use WPPedia Templates', 'options', 'wppedia'),
				'type'								=> 'switch',
				'desc'								=> _x('If disabled WPPedia the Layout and content of WPPedia\'s Single pages will be defined by your themes templates. Attention: most WPPedia template filters and actions will stop working on Singular pages. This option might help if you encounter any incompatibilities between your theme and WPPedia\'s default templates.', 'options', 'wppedia'),
				'settings_section'					=> 'singular',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Show navigation in single articles
			[
				'id'								=> 'show_nav',
				'label'								=> _x('Show navigation', 'options', 'wppedia'),
				'type'								=> 'switch',
				'desc'								=> _x('Show or hide WPPedia\'s navigation on single pages.', 'options', 'wppedia'),
				'settings_section'					=> 'singular',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Show searchbar in single articles
			[
				'id'								=> 'show_searchbar',
				'label'								=> _x('Show searchbar', 'options', 'wppedia'),
				'type'								=> 'switch',
				'desc'								=> _x('Show or hide WPPedia\'s searchbar on single pages.', 'options', 'wppedia'),
				'settings_section'					=> 'singular',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],

			/**
			 * Crosslink settings
			 */

			// Activate crosslinking
			[
				'id'								=> 'active',
				'label'								=> _x( 'Activate Crosslinking', 'options', 'wppedia' ),
				'type'								=> 'switch',
				'desc'								=> _x( 'Allow WPPedia to automatically generate links to other articles if their name was found on a glossary term.', 'options', 'wppedia' ),
				'settings_section'					=> 'crosslinks',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Prefer single words for crosslinks
			[
				'id'								=> 'prefer_single_words',
				'label'								=> _x( 'Prefer single words', 'options', 'wppedia' ),
				'type'								=> 'switch',
				'desc'								=> _x( 'Enabling this option will change the default behaviour of crosslinking and WPPedia tries to link single words instead of multiple if possible. e.g. if there is a post "Lorem" and a post "Lorem Ipsum", the plugin will link only "Lorem" now if "Lorem Ipsum" was found in the content.', 'options', 'wppedia' ),
				'settings_section'					=> 'crosslinks',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Crosslink posttypes
			[
				'id'								=> 'posttypes',
				'label'								=> _x( 'Create crosslinks to post types', 'options', 'wppedia' ),
				'type'								=> 'checkbox-group',
				'options'							=> $this->get_public_posttypes(),
				'pro'								=> true,
				'settings_section'					=> 'crosslinks',
				'settings_page'						=> 'wppedia_settings',
				'register_setting'					=> false,
				'sanitize_callback'					=> [ $this, 'sanitize_array' ],
			],
			// Crosslink index
			[
				'id'								=> 'build_index',
				'label'								=> _x('Crosslink Index', 'options', 'wppedia'),
				'type'								=> 'switch',
				'pro'								=> true,
				'desc'								=> _x('If enabled WPPedia will create automatic indexes with all links created for each post. This ensures a significant faster loading time!', 'options', 'wppedia'),
				'settings_section'					=> 'crosslinks',
				'settings_page'						=> 'wppedia_settings',
				'register_setting'					=> false,
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],

			/**
			 * Tooltip settings
			 */

			// Activate tooltip feature
			[
				'id'								=> 'active',
				'label'								=> _x( 'Enable tooltip feature', 'options', 'wppedia' ),
				'type'								=> 'switch',
				'desc'								=> _x( 'Enable / Disable the tooltip feature for WPPedia Crosslinks.', 'options', 'wppedia' ),
				'settings_section'					=> 'tooltips',
				'settings_page'						=> 'wppedia_settings',
				'sanitize_callback'					=> [ $this, 'sanitize_bool' ],
			],
			// Select tooltip style
			[
				'id'								=> 'style',
				'label'								=> _x( 'Tooltip style', 'options', 'wppedia' ),
				'type'								=> 'select',
				'options'							=> [
					'light'			=> 'Light',
					'light-border' 	=> 'Light with border',
					'material'		=> 'Material',
					'translucent'	=> 'Translucent'
				],
				'desc'								=> _x( 'Select your preferred tooltip style.', 'options', 'wppedia' ),
				'settings_section'					=> 'tooltips',
				'settings_page'						=> 'wppedia_settings',
			],

			/**
			 * Permalink settings
			 */

			// Glossary permalink base setting
			[
				'id'								=> 'base',
				'label'								=> _x('WPPedia base', 'options', 'wppedia'),
				'type'								=> 'text',
				'settings_section'					=> 'wppedia_settings_permalink',
				'settings_page'						=> 'permalink',
				'register_setting'					=> [ $this, 'sanitize_permalink_part' ]
			],
			[
				'id'								=> 'use_initial_character',
				'label'								=> _x('use initial character in URL', 'options', 'wppedia'),
				'type'								=> 'switch',
				'settings_section'					=> 'wppedia_settings_permalink',
				'settings_page'						=> 'permalink'
			]
		];

		foreach ($this->settings_fields as $field) {
			$field_arguments = $this->setFieldArguments($field);
			$field_name = 'wppedia_settings[' . $this->field_group($field) . '][' . $field['id'] . ']';
			$field_type = (isset($field['type'])) ? $field['type'] : 'text';

			add_settings_field(
				$field['id'],
				$field['label'],
				[ $this, 'field' ],
				$field['settings_page'],
				$field['settings_section'],
				[
					'id' 				=> $field['id'],
					'name'				=> $field_name,
					'settings_section' 	=> $field['settings_section'],
					'type'				=> $field_type,
					'class'				=> isset($field_arguments['class']) ? $field_arguments['class'] : '',
					'args'				=> $field_arguments,
				]
			);
		}

	}

	/**
	 * Set Arguments for fields
	 *
	 * @since 1.3.0
	 */
	private function setFieldArguments(array $field) {
		$args = [];

		// General field data
		$args['label'] = $field['label'];

		if (isset($field['class']) && $field['class'] !== '') {
			$args['class'] = $field['class'];
		}

		if (isset($field['desc']) && $field['desc'] !== '') {
			$args['desc'] = $field['desc'];
		}

		if (isset($field['default'])) {
			$args['default'] = $field['default'];
		}

		// Options field data
		if (isset($field['options']) && !empty($field['options'])) {
			$args['options'] = $field['options'];
		}

		// Arbitrary title field data
		if ('title' === $field['type']) {
			$args['heading_level'] = (isset($field['heading_level'])) ? $field['heading_level'] : 'h2';
		}

		$args['pro'] = isset($field['pro']) && $field['pro'] === true;

		return $args;
	}

	/**
	 * Default settings section callback
	 * show text and other content right before the
	 * settings section
	 *
	 * @since 1.1.0
	 */
	function settings_section_callback($section) {
		$output = false;
		switch ($section['id']) {
			case 'wppedia_settings_page':
				$output = _x( 'Setup WPPedia pages.', 'options', 'wppedia' );
				break;
			case 'wppedia_settings_crosslinks':
				$output = _x( 'Modify WPPedia crosslink module settings.', 'options', 'wppedia' );
				break;
			case 'wppedia_settings_permalink':
				$output = _x( 'If you like, you may enter custom structures for your WPPedia URLs here.', 'options', 'wppedia' );
			default:
				break;
		}
		echo wpautop($output);
		echo '<hr />';
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
				echo '</div>';
				continue;
			}

			echo '<table class="form-table" role="presentation">';
			$this->do_settings_fields( $page, $section['id'] );
			echo '</table>';
			submit_button();

			echo '</div>';

		}

		echo '</div>';

	}

	/**
	 * Custom implementation of do_settings_sections for usage
	 * with tabs
	 *
	 * @see https://developer.wordpress.org/reference/functions/do_settings_sections/
	 *
	 * @since 1.3.0
	 */
	private function do_settings_fields( $page, $section ) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}

	  	foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {

			$single_column = false;
			$tdColspan = '';
			if (isset($field['args']['type']) && $field['args']['type'] === 'title') {
				$single_column = true;
				$tdColspan = ' colspan="2"';
			}

			$class = '';

			if ( isset( $field['args']['args']['pro'] ) && $field['args']['args']['pro'] ) {
				$class = ' class="' . self::$pro_feature_className . '"';
			}

			echo "<tr{$class}>";

			if (!$single_column) {
				if ( ! empty( $field['args']['label_for'] ) ) {
					echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
				} else {
					echo '<th scope="row">' . $field['title'] . '</th>';
				}
			}

			echo "<td{$tdColspan}>";
			call_user_func( $field['callback'], $field['args'] );
			echo '</td>';
			echo '</tr>';
	  }
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
			$return_arr[$pt] = $obj->labels->name;
		}

		return $return_arr;

	}

	/**
	 * Check if the updated options require flushing the rewrite rules
	 *
	 * @param array $old_value
	 * @param array $new_value
	 * @param string $option_name
	 *
	 * @return void
	 *
	 * @since 1.3.0
	 */
	function maybe_set_flush_rewrite_rules_flag($old_value, $new_value, $option) {
		$options_to_be_checked = [
			'general'		=> [
				'front_page_id'
			],
			'permalinks' 	=> [
				'base',
				'use_initial_character'
			],
		];

		foreach ( $options_to_be_checked as $option_group => $options ) {
			foreach ( $options as $option_name ) {
				if ( isset( $new_value[$option_group][$option_name] ) && $old_value[$option_group][$option_name] != $new_value[$option_group][$option_name] ) {
					$this->set_flush_rewrite_rules_flag($old_value[$option_group][$option_name], $new_value[$option_group][$option_name]);
					break;
				}
			}
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
	 * Do stuff while changing plugin options
	 *
	 * @param array $old_value
	 * @param array $new_value
	 * @param string $option_name
	 *
	 * @since 1.3.0
	 */
	function update_option_wppedia_settings($old_value, $new_value, $option_name) {
		// unset `frontpage_slug_not_matching_permalink_settings` notification
		if ( $old_value['general']['front_page_id'] != $new_value['general']['front_page_id'] ) {
			wppedia_undismiss_notification( 'wppedia_frontpage_slug_not_matching_permalink_settings' );
		}
	}

	/**
	 * Sanitize input array and merge them with the already saved options
	 *
	 * @param array $input
	 *
	 * @since 1.3.0
	 */
	function wppedia_settings_save($input_array) {
		// Sanitize each input key based on settings field type
		$sanitized_input = [];
		foreach ( $input_array as $option_group => $options ) {
			if ( ! isset( self::get_option_defaults()[$option_group] ) ) {
				continue;
			}

			$sanitized_input[$option_group] = $this->sanitize_option_group($option_group, $options);
		}

		$settings = get_option( 'wppedia_settings' );
		$settings = wp_parse_args( $sanitized_input, $settings );
		return $settings;
	}

	/**
	 * Sanitize each option group
	 *
	 * @param string $option_group
	 * @param array $options
	 *
	 * @since 1.3.0
	 */
	function sanitize_option_group($option_group, $options) {
		$sanitized_input = [];

		foreach ( $options as $option_name => $value ) {
			if ( ! isset( self::get_option_defaults()[$option_group][$option_name] ) ) {
				continue;
			}

			$sanitized_input[$option_name] = $this->sanitize_option($option_group, $option_name, $value);
		}

		return $sanitized_input;
	}

	/**
	 * Sanitize each option
	 *
	 * @param string $option_group
	 * @param string $option_name
	 * @param mixed $value
	 *
	 * @since 1.3.0
	 *
	 * @return mixed
	 */
	function sanitize_option($option_group, $option_name, $value) {
		$sanitized_input = null;
		$sanitize_callback = $this->settings_fields[$option_group][$option_name]['sanitize_callback'];

		if (!empty($sanitize_callback)) {
			$sanitized_input = call_user_func($sanitize_callback, $value);
		} else {
			$sanitized_input = sanitize_text_field($value);
		}

		return $sanitized_input;
	}

	/**
	 * Add a custom options section to the permalinks admin screen
	 *
	 * @uses add_settings_section()
	 *
	 * @since 1.2.0
	 */
	function wppedia_permalink_settings_save() {

		// Save options to database
		if ( isset( $_POST['wppedia_settings']['permalinks']['base'] ) || isset( $_POST['wppedia_settings']['permalinks']['use_initial_character'] ) ) {

			check_admin_referer('update-permalink');

			if (!current_user_can('manage_options'))
				wp_die(__('Cheatin&#8217; uh?'));

			if (isset( $_POST['wppedia_settings']['permalinks']['base'] )) {
				$permalink_base = $_POST['wppedia_settings']['permalinks']['base'];
				$sanitized_permalink_base = $this->sanitize_permalink_part($permalink_base);
				if ('' !== $sanitized_permalink_base && $sanitized_permalink_base !== self::get_option('permalinks', 'base')) {
					self::update_option( 'permalinks', 'base', $sanitized_permalink_base );
				} else if ('' === $sanitized_permalink_base) {
					self::remove_option( 'permalinks', 'base' );
				}
			}

			if (isset( $_POST['wppedia_settings']['permalinks']['use_initial_character'] ) && $_POST['wppedia_settings']['permalinks']['use_initial_character']) {
				self::update_option( 'permalinks', 'use_initial_character', true );
			} else {
				self::update_option( 'permalinks', 'use_initial_character', false );
			}

		}

	}
}
