<?php

/**
 * Include Styles and Scripts
 *
 * @since 1.3.0
 */

use WPPedia\Options;
use WPPedia\Rest_Controller;
use WPPedia\Inline_Style_Collector;

/**
 * Enqueue Assets
 *
 * @since 1.3.0
 */
function wppedia_enqueue_frontend_assets() {

	if ( ! is_wppedia_page() )
		return;

	wp_enqueue_style( 'wppedia-style', WPPediaPluginUrl . 'dist/css/style.min.css', [], null );

	// Scripts
	if ( is_singular() && Options::get_option('tooltips', 'active') ) {

		// Tooltips
		wp_enqueue_script( 'wppedia_ajax_tooltips', WPPediaPluginUrl . 'dist/js/ajax_tooltip.bundle.js', [], null, true );
		wp_localize_script( 'wppedia_ajax_tooltips', 'wppedia_tooltip_props', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'tooltip_theme' => Options::get_option('tooltips', 'style')
		) );

	}

	if (
		(is_wppedia_archive() && Options::get_option('archive', 'show_searchbar')) ||
		(is_wppedia_singular() && Options::get_option('singular', 'show_searchbar'))
	) {
		$rest_controller = new Rest_Controller();
		wp_enqueue_script( 'wppedia_search', WPPediaPluginUrl . 'dist/js/search.bundle.js', [], null, true );
		wp_localize_script( 'wppedia_search', 'wppedia_search_props', [
			'postlist_url' 		=> $rest_controller->get_endpoint_url( $rest_controller->rest_endpoint_search ),
			'search_options'	=> json_encode( [
				'keys' => [
					'post_title',
					'tags'
				],
				'threshold' => 0.5,
			] ),
			'searchinput_id'	=> apply_filters( 'wppedia_search_input_id', 'wppedia_search_input' )
		] );
	}

	// Print inline styles
	$css_vars = [
		'--wppedia-main-color' => get_theme_mod('wppedia_main_color', '#160351')
	];

	$css_var_string = ':root{' . join( ';', array_map( function( $key, $value ) {
		return $key . ':' . $value;
	}, array_keys( $css_vars ), $css_vars ) ) . '}';

	wppedia_add_inline_style('wppedia-css-vars', $css_var_string);

	global $content_width;
	wppedia_add_inline_style('wppedia-content-width', '.wppedia-page .content-area{width:' . $content_width . 'px;}');

	// Alternative Tooltip Themes for inline usage
	switch (Options::get_option('tooltips', 'style')) {
		case 'light-border':
			wppedia_add_inline_style('tooltip-theme', WPPediaPluginDir . 'dist/css/tooltip-theme-light-border.min.css');
			break;
		case 'material':
			wppedia_add_inline_style('tooltip-theme', WPPediaPluginDir . 'dist/css/tooltip-theme-material.min.css');
			break;
		case 'translucent':
			wppedia_add_inline_style('tooltip-theme', WPPediaPluginDir . 'dist/css/tooltip-theme-translucent.min.css');
			break;
		default:
			break;
	}
}
add_action('wp_enqueue_scripts', 'wppedia_enqueue_frontend_assets');

/**
 * Collect all inline styles and queue them
 *
 * @since 1.3.0
 */
function wppedia_enqueue_inline_styles() {
	$final_css = Inline_Style_Collector::getInstance()->get_final_css();
	if ( '' != $final_css ) {
		wp_register_style( 'wppedia-inline-style', false );
		wp_enqueue_style( 'wppedia-inline-style' );
		wp_add_inline_style( 'wppedia-inline-style', $final_css );
	}
}
add_action('wp_enqueue_scripts', 'wppedia_enqueue_inline_styles', 9999);

/**
 * Enqueue admin assets
 *
 * @since 1.3.0
 */
function wppedia_enqueue_admin_assets($hook) {
	$is_edit = false;
	$is_option = false;

	if (('post.php' === $hook || 'post-new.php' === $hook) && wppedia_get_post_type() === get_post_type()) {
		$is_edit = true;
	}

	if ('wppedia_term_page_wppedia_settings' === $hook || 'options-permalink.php' === $hook) {
		$is_option = true;
	}

	if (!$is_edit && !$is_option) {
		return;
	}

	if ($is_edit) {
		wp_register_style('tagify', WPPediaPluginUrl . 'dist/vendor/tagify/tagify.css', [], '4.12.0');
		wp_enqueue_style('tagify');
		wp_register_script('tagify', WPPediaPluginUrl . 'dist/vendor/tagify/tagify.min.js', [], '4.12.0', true);

		$edit_script_asset_file = WPPediaPluginDir . 'dist/js/edit.bundle.asset.php';
		$edit_script_asset = (file_exists($edit_script_asset_file)) ? require($edit_script_asset_file) : ['dependencies' => [], 'version' => filemtime($edit_script_asset_file)];
		wp_enqueue_script('wppedia_edit', WPPediaPluginUrl . 'dist/js/edit.bundle.js', array_merge($edit_script_asset['dependencies'], ['tagify']), wppedia_get_version(), null, true);
	}

	if ($is_option) {
		wp_register_script('wppedia_option_tabs', '', ['jquery', 'jquery-ui-core', 'jquery-ui-tabs'], null, true);
		wp_enqueue_script('wppedia_option_tabs');
		wp_add_inline_script(
			'wppedia_option_tabs',
			'jQuery("document").ready(function($) {
				var wppedia_tabs = $(".wppedia-settings-tabs");
				var wppedia_tabs_anchor = wppedia_tabs.find(".wppedia-settings-tabs-wrapper > li > a");

				wppedia_tabs.tabs();

				wppedia_tabs_anchor.on("click", function(e) {
					e.preventDefault();
					if(history.pushState) {
						history.pushState(null, null, this.href);
					} else {
						location.hash = this.href;
					}
				});
			});'
		);

		wp_register_style('select2', WPPediaPluginUrl . 'dist/vendor/select2/select2.min.css', [], '4.0.13');
		wp_enqueue_style('select2');
		wp_register_script('select2', WPPediaPluginUrl . 'dist/vendor/select2/select2.min.js', [], '4.0.13', true);

		$options_script_asset_file = WPPediaPluginDir . 'dist/js/options.bundle.asset.php';
		$options_script_asset = (file_exists($options_script_asset_file)) ? require($options_script_asset_file) : ['dependencies' => [], 'version' => filemtime($options_script_asset_file)];
		wp_enqueue_script('wppedia_options', WPPediaPluginUrl . 'dist/js/options.bundle.js', array_merge($options_script_asset['dependencies'], ['select2']), wppedia_get_version(), null, true);
	}

	if ($is_edit || $is_option) {
		wp_enqueue_style( 'wppedia-admin', WPPediaPluginUrl . 'dist/css/admin.min.css', wppedia_get_version(), null );
	}
}
add_action('admin_enqueue_scripts', 'wppedia_enqueue_admin_assets');
