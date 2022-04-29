<?php

/**
 * Include Styles and Scripts
 * 
 * @since 1.3.0
 */

use WPPedia\options;
use WPPedia\inlineStyleCollector;

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
	if ( is_singular() && options::get_option('tooltips', 'active') ) {

		// Tooltips
		wp_enqueue_script( 'wppedia_ajax_tooltips', WPPediaPluginUrl . 'dist/js/ajax_tooltip.bundle.js', [], null, true );
		wp_localize_script( 'wppedia_ajax_tooltips', 'wppedia_tooltip_props', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'tooltip_theme' => options::get_option('tooltips', 'style')
		) );

	}

	if (
		(is_wppedia_archive() && options::get_option('archive', 'show_searchbar')) ||
		(is_wppedia_singular() && options::get_option('singular', 'show_searchbar'))
	) {
		$rest_controller = new WPPedia\restController();
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
	switch (options::get_option('tooltips', 'style')) {
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

function wppedia_enqueue_inline_styles() {
	$final_css = inlineStyleCollector::getInstance()->get_final_css();
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
		wp_register_style('tagify', WPPediaPluginUrl . 'dist/vendor/tagify.css', [], '4.0.5');
		wp_enqueue_style('tagify');
		wp_register_script('tagify', WPPediaPluginUrl . 'dist/vendor/tagify.min.js', [], '4.0.5', true);

		$edit_script_asset_file = WPPediaPluginDir . 'dist/js/edit.bundle.asset.php';
		$edit_script_asset = (file_exists($edit_script_asset_file)) ? require($edit_script_asset_file) : ['dependencies' => [], 'version' => filemtime($edit_script_asset_file)];
		wp_enqueue_script('wppedia_edit', WPPediaPluginUrl . 'dist/js/edit.bundle.js', array_merge($edit_script_asset['dependencies'], ['tagify']), wppedia_get_version(), null, true);
	}

	if ($is_option) {

	}

	if ($is_edit || $is_option) {
		wp_enqueue_style( 'wppedia-admin', WPPediaPluginUrl . 'dist/css/admin.min.css', wppedia_get_version(), null );
	}
}
add_action('admin_enqueue_scripts', 'wppedia_enqueue_admin_assets');
