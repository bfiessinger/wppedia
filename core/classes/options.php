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
			'wppedia_settings_archive', 
			_x('Archive settings', 'options', 'wppedia'), 
			[ $this, 'settings_section_callback' ], 
			'wppedia_settings_general'
		);

		// Settings field: Prefer WPPedia templates for archives
		add_settings_field(
			'wppedia_archive_use_templates',
			_x('use WPPedia Templates', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_archive',
			[
				'id' => 'wppedia_archive_use_templates',
				'switch' => true,
				'desc' => _x('If disabled WPPedia the Layout and content of WPPedia\'s Archive will be defined by your themes templates. Attention: most WPPedia template filters and actions will stop working on Archive pages. This option might help if you encounter any incompatibilities between your theme and WPPedia\'s default templates.', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_archive_use_templates'
		);

		// Settings field: Show navigation on archive pages
		add_settings_field(
			'wppedia_archive_show_navigation',
			_x('Show navigation', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_archive',
			[
				'id' => 'wppedia_archive_show_navigation',
				'switch' => true,
				'desc' => _x('Show or hide WPPedia\'s navigation on archive pages.', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_archive_show_navigation'
		);

		// Settings field: Show searchbar on archive pages
		add_settings_field(
			'wppedia_archive_show_searchbar',
			_x('Show searchbar', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_archive',
			[
				'id' => 'wppedia_archive_show_searchbar',
				'switch' => true,
				'desc' => _x('Show or hide WPPedia\'s searchbar on archive pages.', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_archive_show_searchbar'
		);

		// Settings field: Manage posts per page
		add_settings_field(
			'wppedia_posts_per_page',
			_x('Posts per page', 'options', 'wppedia'),
			[ $this, 'create_basic_input' ],
			'wppedia_settings_general',
			'wppedia_settings_archive',
			[
				'id' => 'wppedia_posts_per_page',
				'type' => 'number',
				'desc' => _x('Manage how much posts should be available per page at a glossary archive', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_posts_per_page'
		);

		// Settings section: Single articles
		add_settings_section(
			'wppedia_settings_singular',
			_x('Single article settings', 'options', 'wppedia'),
			[ $this, 'settings_section_callback' ],
			'wppedia_settings_general'
		);

		// Settings field: Prefer WPPedia templates for single pages
		add_settings_field(
			'wppedia_singular_use_templates',
			_x('use WPPedia Templates', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_singular',
			[
				'id' => 'wppedia_singular_use_templates',
				'switch' => true,
				'desc' => _x('If disabled WPPedia the Layout and content of WPPedia\'s Single pages will be defined by your themes templates. Attention: most WPPedia template filters and actions will stop working on Singular pages. This option might help if you encounter any incompatibilities between your theme and WPPedia\'s default templates.', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_singular_use_templates'
		);
	
		// Settings field: Show navigation on archive pages
		add_settings_field(
			'wppedia_singular_show_navigation',
			_x('Show navigation', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_singular',
			[
				'id' => 'wppedia_singular_show_navigation',
				'switch' => true,
				'desc' => _x('Show or hide WPPedia\'s navigation on single pages.', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_singular_show_navigation'
		);

		// Settings field: Show searchbar on archive pages
		add_settings_field(
			'wppedia_singular_show_searchbar',
			_x('Show searchbar', 'options', 'wppedia'),
			[ $this, 'create_checkbox' ],
			'wppedia_settings_general',
			'wppedia_settings_singular',
			[
				'id' => 'wppedia_singular_show_searchbar',
				'switch' => true,
				'desc' => _x('Show or hide WPPedia\'s searchbar on single pages.', 'options', 'wppedia')
			]
		);

		register_setting(
			'wppedia_settings_general',
			'wppedia_singular_show_searchbar'
		);

	}

	/**
	 * Default settings section callback
	 * show text and other content right before the 
	 * settings section
	 * 
	 * @since 1.0.0
	 */
	function settings_section_callback($section) {
		switch ($section['id']) {
			case 'wppedia_settings_page':
				echo '<p>Setup WPPedia pages</p>';
				break;
			case 'wppedia_settings_crosslinks':
				echo '<p>Modify WPPedia crosslink module settings</p>';
				break;
			case 'wppedia_settings_archive':
				echo '<p>Content on glossary archives.</p>';
				break;
			case 'wppedia_settings_singular':
				echo '<p>Content on glossary single pages.</p>';
				break;
			default:
				break;
		}
		echo '<hr />';
	}

	/**
	 * Create a basic input field
	 * This function works for most basic textual inputs like
	 * email, url, number, ...
	 * 
	 * @property array $args {
	 * 	@param string 'id' - the main option name
	 * 	@param string 'type' - input type allowed values are
	 * 	email, number, password, tel, text and url. If no value is added
	 * 	or the value is not one of these types it will fall back to text
	 * 	@param string 'class' - className
	 * 	@param string 'desc' - field description
	 * }
	 * 
	 * @since 1.0.0
	 */
	function create_basic_input(array $args) {
		if (!isset($args['id']))
			return;

		$allowed_types = [
			'email',
			'number',
			'password',
			'tel',
			'text',
			'url'
		];

		// if type is not supported fall back to text
		if (!isset($args['type']) || !in_array($args['type'], $allowed_types)) {
			$type = 'text';
		} else {
			$type = $args['type'];
		}

		$pro_only = (isset($args['class']) && false !== strpos($args['class'], self::$pro_feature_className)) ? true : false;

		$option = $args['id'];

		// Render the field
		echo '<input type="' . $type . '" name="' . $option . '" id="' . $option . '" value="' . get_option($option) . '"' . $this->restrict_pro($pro_only) . '>';
		
		// Show field description
		if (isset($args['desc']) && '' !== $args['desc']) {
			echo '<div class="wppedia-option-description">';
			echo $args['desc'];
			echo '</div>';
		}

	}

	/**
	 * Create a select field
	 * 
	 * @property array $args {
	 * 	@param string 'id' - the main option name
	 * 	@param array 'options' - select options
	 * 	@param string 'class' - className
	 * 	@param string 'desc' - field description
	 * } 
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
	 * @property array $args {
	 * 	@param string 'id' - the main option name
	 * 	@param string 'key' - if stored as a json serialized array this is used as the key
	 * 	@param string 'class' - className
	 *  @param bool 'switch' - whether or not to render the checkbox as a switch
	 * 	@param string 'desc' - field description
	 * } 
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

	/**
	 * Creates a group of checkbox elements
	 * Items saved using this method share the same option name and will therefor
	 * be saved as json to the database.
	 * 
	 * Most properties from the $args array will be passed over to the
	 * create_checkbox method.
	 * 
	 * @property array $args {
	 * 	@param string 'id' - the main option name
	 * 	@param array 'options' - array of options with key value pairs. Whereas key will be used
	 * 	as the key in the json array where the option is saved and label as a checkbox label		 
	 * 	@param string 'class' - className
	 *  @param bool 'switch' - whether or not to render the checkbox as a switch
	 * 	@param string 'desc' - field description
	 * } 
	 * 
	 * @uses create_checkbox()
	 * 
	 * @since 1.0.0
	 */
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

		foreach($values as $k => $v) {

			$checkbox_args = [
				'id' => $option,
				'key' => $k,
				'desc' => null // null description to avoid duplicates
			];

			$checkbox_args = array_merge($args, $checkbox_args);

			echo '<div class="wppedia-checkbox-group-item">';
			$this->create_checkbox($checkbox_args);
			echo '<label for="' . $option . '[' . $k . ']">' . $v . '</label>';
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
			$return_arr[$pt] = $obj->labels->name;
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
			'wppedia_settings_permalink', // ID
			__( 'WPPedia Permalinks', 'wppedia' ),// Section title
			[ $this, 'wppedia_permalink_settings_cb' ], // Callback for your function
			'permalink' // Location (Settings > Permalinks)
		);

		/**
		 * Glossary permalink base setting
		 */
		add_settings_field( 
			'wppedia_permalink_base', 
			__( 'WPPedia base', 'wppedia' ), 
			[ $this, 'create_basic_input' ], 
			'permalink', 
			'wppedia_settings_permalink',
			[
				'id' => 'wppedia_permalink_base',
				'type' => 'text',
			]
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
			'wppedia_settings_permalink',
			[
				'id' => 'wppedia_permalink_use_initial_character',
				'switch' => true,
				'class' => self::$pro_feature_className,
			]
		);

		// Save options to database
		if ( isset( $_POST['wppedia_permalink_base'] ) || isset( $_POST['wppedia_permalink_use_initial_character'] ) ) {

			check_admin_referer('update-permalink');

			if ( !current_user_can('manage_options') )
				wp_die(__('Cheatin&#8217; uh?'));

			if ( isset( $_POST['wppedia_permalink_base'] ) ) {
				$sanitized_permalink_base = $this->wppedia_permalink_part_sanitize($_POST['wppedia_permalink_base']);
				if ('' !== $sanitized_permalink_base) {
					update_option( 'wppedia_permalink_base', $sanitized_permalink_base );
				} else {
					delete_option( 'wppedia_permalink_base' );
				}
			}

		}

	}

	function wppedia_permalink_settings_cb() {
		echo wpautop( __( 'If you like, you may enter custom structures for your WPPedia URLs here.', 'wppedia' ) );
	}

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