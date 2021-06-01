<?php

/**
 * Admin View
 * 
 * @since 1.2.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class admin {

	private static $colorscheme_css = '';

  protected function __clone() {}

  public function __construct() {

		// Add Text to the glossary archive page
		add_action( 'display_post_states', [ $this, 'wppedia_archive_post_state' ], 10, 2 );

		// Add body class to WPPedia Admin Pages
		add_filter( 'admin_body_class', [ $this, 'wppedia_admin_body_class' ] );

		// Create admin color scheme css
		add_action( 'admin_head', [ $this, 'create_colorscheme_css_vars' ] );
		add_action( 'admin_footer', [ $this, 'enqueue_colorscheme_css' ] );

		// Show WPPedia Logo in Settings
		add_action( 'wppedia_admin_settings_page_header_content', [ $this, 'settings_header_logo' ], 10 );

		// Add plugin action links
		add_filter( 'plugin_action_links_' . WPPediaPluginBaseName, [ $this, 'plugin_action_links' ] );

		// Admin notices
		add_action( 'admin_notices', [ $this, 'frontpage_slug_not_matching_permalink_settings_notice' ] );
		add_action( 'update_option_wppedia_front_page_id', [ $this, 'unset_admin_notice_state_permalink_base_frontpage_slug_check' ], 10 );

		// dismiss notice handler
		add_action( 'wp_ajax_dismissed_notice_handler', [ $this, 'ajax_dismiss_notice_handler' ] );
		add_action( 'admin_print_footer_scripts', [ $this, 'ajax_dismiss_notice_print_scripts' ] );

  }

	/**
	 * Modify the posts state for the glossary Archive Page
	 * 
	 * @since 1.0.0
	 */
	function wppedia_archive_post_state( $post_states, $post ) {

		if( $post->ID == wppedia_get_page_id('front') ) {
			$post_states[] = __( 'Glossary page', 'wppedia' );
		}
	
		return $post_states;

	}

	/**
	 * Get the current screen/page post type in admin
	 * 
	 * @since 1.0.0
	 */ 
	private function current_screen_post_type() {
		global $post, $typenow, $current_screen;

		if ($post && $post->post_type)
			return $post->post_type;
		elseif ($typenow)
			return $typenow;
		elseif ($current_screen && $current_screen->post_type)
			return $current_screen->post_type;
		elseif (isset($_REQUEST['post_type']))
			return sanitize_key($_REQUEST['post_type']);

		return null;
	}

	/**
	 * Add a body class to all WPPedia Admin pages
	 * 
	 * @since 1.0.0
	 */
	function wppedia_admin_body_class( $classes ) {
		$current_screen_pt = $this->current_screen_post_type();
		if (is_admin() && 'wppedia_term' == $current_screen_pt) {
			$classes .= ' wppedia-page';
		}
		return $classes;
	}

	/**
	 * Setup colorscheme CSS variables based on
	 * the selected colorscheme of the current user
	 * 
	 * @since 1.0.0
	 */
	function create_colorscheme_css_vars() {
		global $current_screen, $_wp_admin_css_colors;

		$current_screen_pt = $this->current_screen_post_type();

		$scheme_name = get_user_meta(get_current_user_id(), 'admin_color', true);
		$scheme = $_wp_admin_css_colors[$scheme_name];

		$colors = $scheme->colors;
		$icon_colors = $scheme->icon_colors;

		$var_prefix = '--wppedia-wp-color';

		if (is_admin() && ('wppedia_term' == $current_screen_pt || 'options-permalink' == $current_screen->id)) {

			$vars = [];

			foreach ($colors as $k => $col) {
				$var = '';

				switch ($k) {
					case 2:
						$var = 'base';
						break;
					case 0:
						$var = 'highlight';
						break;
					case 1:
						$var = 'menu-submenu-background';
						break;
					default:
						break;
				}

				if ('' !== $var) {
					$vars[] = "\t$var_prefix-$var: $col;";
				}
			}

			foreach ($icon_colors as $k => $col) {
				$var = '';

				switch ($k) {
					case 'base':
						$var = 'base';
						break;
					case 'focus':
						$var = 'focus';
						break;
					case 'current':
						$var = 'current';
						break;
					default:
						break;
				}

				if ('' !== $var) {
					$vars[] = "\t$var_prefix-icon-$var: $col;";
				}
			}

			if (!empty($vars)) {
				$css = ":root {".join(PHP_EOL,$vars)."\t}";
				self::$colorscheme_css = $css;
			}
		}
	}

	/**
	 * Enqueue the colorscheme created in
	 * create_colorscheme_css_vars method
	 * 
	 * @since 1.0.0
	 */
	function enqueue_colorscheme_css() {

		if ('' === self::$colorscheme_css)
			return;

		wp_register_style( 'wppedia-admin-colorscheme', false );
		wp_enqueue_style( 'wppedia-admin-colorscheme' );
		wp_add_inline_style( 'wppedia-admin-colorscheme', self::$colorscheme_css );
	}

	/**
	 * Show the WPPedia logo in the header section
	 * of WPPedia Settings screen
	 * 
	 * @since 1.2.0
	 */
	function settings_header_logo() { ?>
		<img class="wppedia-logo" src="<?php echo WPPediaPluginUrl; ?>assets/img/wppedia-logo.svg" width="60">
	<?php }

	/**
	 * Add plugin action links on the plugins
	 * screen
	 * 
	 * @since 1.1.0
	 */
	function plugin_action_links(array $actions) {
		$actions[] = '<a href="'. esc_url(get_admin_url(null, 'edit.php?post_type=' . wppedia_get_post_type())) .'">' . __('Manage glossary', 'wppedia') . '</a>';
		$actions[] = '<a href="'. esc_url(get_admin_url(null, 'edit.php?post_type=' . wppedia_get_post_type() . '&page=wppedia_settings_general')) .'">' . __('Settings') . '</a>';
		return $actions;
	}

	/**
	 * Display an admin notice if the WPPedia frontpage slug
	 * does not match the permalink base setting
	 * 
	 * @since 1.1.6
	 */
	function frontpage_slug_not_matching_permalink_settings_notice() {

		$current_state = $this->get_admin_notice_state('permalink_base_frontpage_slug_check');
		$is_dismissed = (isset($current_state['is_dismissed']) && $current_state['is_dismissed']) ? true : false;

		if (isset($current_state['valid_until']) && strtotime($current_state['valid_until']) > time()) {
			$this->remove_admin_notice_state('permalink_base_frontpage_slug_check');
			$is_dismissed = false;
		}

		if (!$is_dismissed && false !== wppedia_get_page_id('front') && get_post_field('post_name', get_post(wppedia_get_page_id('front'))) !== get_option('wppedia_permalink_base')) {
			echo '<div class="wppedia-admin-message notice notice-warning is-dismissible" data-wppedia_notice="permalink_base_frontpage_slug_check">';
			echo '<p>';
			printf(
				_x('Attention! Your permalink base %s does not match the slug of your glossary frontpage %s', 'options', 'wppedia'),
				'<code>' . get_option('wppedia_permalink_base') . '</code>',
				'<code>' . get_post_field('post_name', get_post(wppedia_get_page_id('front'))) . '</code>'
			);
			echo '</p>';
			echo '<p>';
			echo '<a class="button" href="' . admin_url('/options-permalink.php') . '" target="_blank">' . __('Manage permalinks') . '</a>';
			echo '</p>';
			echo '</div>';
		}

		return false;
	}

	/**
	 * Unset Admin notice state for permalink_base_frontpage_slug_check
	 * after updating the wppedia frontpage option
	 * 
	 * @since 1.1.6
	 */
	function unset_admin_notice_state_permalink_base_frontpage_slug_check() {
		$this->remove_admin_notice_state('permalink_base_frontpage_slug_check');
	}

	/**
	 * Get admin notice state by name
	 * 
	 * @since 1.1.6
	 */
	public function get_admin_notice_state($name) {
		$current_states = get_option('wppedia_admin_notice_states');
		if (!isset($current_states[$name])) {
			return;
		}
		return $current_states[$name];
	}

	/**
	 * Set admin notice state
	 * 
	 * @since 1.1.6
	 */
	public function set_admin_notice_state($name, $state = []) {
		if (!get_option('wppedia_admin_notice_states')) {
			add_option('wppedia_admin_notice_states', [], '', false);
		}

		$default_state = [
			'is_dismissed' => true, 
			'valid_until' => null
		];

		if (!is_array($state) || is_empty($state)) {
			$state = $default_state;
		}

		$current_states = get_option('wppedia_admin_notice_states');

		$current_states[$name] = $state;
		update_option('wppedia_admin_notice_states', $current_states, false);
	}

	/**
	 * Remove admin notice state
	 * 
	 * @since 1.1.6
	 */
	public function remove_admin_notice_state($name) {
		$current_states = get_option('wppedia_admin_notice_states');
		if (!isset($current_states[$name])) {
			return;
		}
		unset($current_states[$name]);
		update_option('wppedia_admin_notice_states', $current_states, false);
	}

	/**
	 * Ajax handler to dismiss an admin notice
	 * 
	 * @since 1.1.6
	 */
	public function ajax_dismiss_notice_handler() {
		check_ajax_referer('process_wppedia_dismiss_notice_nonce', '_ajax_nonce');

		if (isset($_POST['notice'])) {
			$valid_until = (isset($_POST['valid_until']) && $_POST['valid_until']) ? $_POST['valid_until'] : null;
			$this->set_admin_notice_state(
				$_POST['notice'],
				[
					'is_dismissed' => true,
					'valid_until' => $valid_until
				]
			);
		}

		wp_die();
	}

	/**
	 * Print footer scripts to dismiss admin notices
	 * 
	 * @since 1.1.6
	 */
	public function ajax_dismiss_notice_print_scripts() {
		$nonce = wp_create_nonce('process_wppedia_dismiss_notice_nonce');
		?>
		<script type="text/javascript">
			jQuery(function($) {
				$(document).on('click', '.wppedia-admin-message .notice-dismiss', function () {
					var message_element = $(this).closest('.wppedia-admin-message');
					var notice_name = message_element.data('wppedia_notice');
					var valid_until = message_element.data('wppedia_notice_valid_until') || null;
					$.ajax('<?php echo admin_url('admin-ajax.php'); ?>', {
						type: 'POST',
						data: {
							action: 'dismissed_notice_handler',
							_ajax_nonce: '<?php echo $nonce; ?>',
							notice: notice_name,
							valid_until: valid_until
						}
					} );
				} );
			});
		</script>
		<?php
	}

}
