<?php

/**
 * Admin View
 *
 * @since 1.2.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class Admin {

	private static $colorscheme_css = '';

	public function _init() {

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
		add_action( 'wp_helpers_notification_dismissed', [ $this, 'dismiss_admin_notification' ], 10, 2 );
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
		$actions[] = '<a href="'. esc_url(get_admin_url(null, 'edit.php?post_type=' . wppedia_get_post_type() . '&page=wppedia_settings')) .'">' . __('Settings') . '</a>';
		return $actions;
	}

	/**
	 * Display an admin notice if the WPPedia frontpage slug
	 * does not match the permalink base setting
	 *
	 * @since 1.1.6
	 */
	function frontpage_slug_not_matching_permalink_settings_notice() {
		if (false !== wppedia_get_page_id('front') && get_post_field('post_name', get_post(wppedia_get_page_id('front'))) !== options::get_option('permalinks', 'base')) {
			$notification_content = '<p>' .
					sprintf(
						_x('Attention! Your permalink base %s does not match the slug of your glossary frontpage %s', 'options', 'wppedia'),
						'<code>' . options::get_option('permalinks', 'base') . '</code>',
						'<code>' . get_post_field('post_name', get_post(wppedia_get_page_id('front'))) . '</code>'
					) . '
				</p>
				<p>
					<a class="button" href="' . admin_url('/options-permalink.php') . '" target="_blank">' . __('Manage permalinks') . '</a>
				</p>';

			if (!wppedia_notification_is_dismissed('frontpage_slug_not_matching_permalink_settings')) {
				WPPedia()->notifications->add($notification_content, [
					'id' => 'frontpage_slug_not_matching_permalink_settings',
					'type' => 'warning',
					'dismiss_until' => '+1 week',
				]);
			}
		}
	}

	function dismiss_admin_notification($notification_id, $notification) {
		$dismiss_until = $notification->args('dismiss_until');
		if ($dismiss_until && '' !== $dismiss_until) {
			$dismiss_until = strtotime($dismiss_until);
			if ($dismiss_until > time()) {
				$dismissed_notifications = get_option('wppedia_dismissed_notifications', []);

				// remove all notifications with a dismiss_until date in the past
				foreach ($dismissed_notifications as $notification_id => $dismissed_until) {
					if ($dismissed_until < time()) {
						unset($dismissed_notifications[$notification_id]);
					}
				}

				$dismissed_notifications[$notification_id] = $dismiss_until;
				update_option( 'wppedia_dismissed_notifications', $dismissed_notifications );
			}
		}
	}
}
