<?php

defined( 'ABSPATH' ) || die();

/**
 * Return a specific page id from a special WPPedia
 * page
 * 
 * Currently available pages:
 * - front
 * 
 * @param string $page
 * 
 * @since 1.1.0
 */
function wppedia_get_page_id(string $page) {
	$page_id = get_option('wppedia_' . $page . '_page_id');
	return ($page_id) ? absint($page_id) : false;
}

/**
 * Get WPPedia Frontpage URL
 * 
 * @param array $query_args array with query args to add
 * 
 * @return string final URL
 * 
 * @since 1.1.0
 */
function get_wppedia_url(array $query_args = []) {
	$archive_url;
	if ( FALSE === wppedia_get_page_id('front') )
		$archive_url = get_post_type_archive_link('wppedia_term');
	else
		$archive_url = get_permalink(wppedia_get_page_id('front'));
	
	if ( ! empty( $query_args ) )
		$archive_url = add_query_arg( $query_args, $archive_url );
	
	return $archive_url;
}

/**
 * Determine if the currently viewed page is a wiki page
 * 
 * @uses wppedia_get_post_type
 * 
 * @since 1.1.6
 */
function is_wppedia_page($query = null) {
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}
	
	/**
	 * No Checks should be performed if the request is 404
	 * or we are not on the main query
	 */
	if ( $query->is_404() || !$query->is_main_query() )
		return false;
	
	if (
		is_wppedia_frontpage($query) || 
		is_wppedia_archive($query) || 
		is_wppedia_singular($query) || 
		is_wppedia_search($query)
	) {
		return true;
	}

	return false;	
}

/**
 * Determine if the current view is a glossary search
 * 
 * @since 1.1.6
 */
function is_wppedia_search($query = null) {
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	/**
	 * No Checks should be performed if the request is 404
	 * or we are not on the main query
	 */
	if ( $query->is_404() || !$query->is_main_query() )
		return false;

	return (is_wppedia_archive() && $query->is_search());
}

/**
 * Determine if the current view is a glossary archive
 * 
 * @since 1.1.6
 */
function is_wppedia_archive($query = null) {
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	/**
	 * No Checks should be performed if the request is 404
	 * or we are not on the main query
	 */
	if ( $query->is_404() || !$query->is_main_query() )
		return false;

	return ($query->is_post_type_archive(wppedia_get_post_type()) || ($query->is_tax() && get_post_type() === wppedia_get_post_type()));
}

/**
 * Determine if the current view is a glossary term
 * 
 * @since 1.1.6
 */
function is_wppedia_singular($query = null) {
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	/**
	 * No Checks should be performed if the request is 404
	 * or we are not on the main query
	 */
	if ( $query->is_404() || !$query->is_main_query() )
		return false;

	return is_singular([wppedia_get_post_type()]);
}

/**
 * Determine if the currently viewed page is the glossary homepage
 * 
 * @since 1.1.6
 */
function is_wppedia_frontpage($query = null) {
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	/**
	 * No Checks should be performed if the request is 404
	 * or we are not on the main query
	 */
	if ( $query->is_404() || !$query->is_main_query() )
		return false;

	return $query->is_page(wppedia_get_page_id('front'));	
}

/**
 * Determine if a static page is used for the wpPedia front page
 * 
 * @since 1.0.0
 * 
 * @return boolean|int Returns false if a cpt archive is used or the post ID of the static page
 */
function wppedia_has_static_frontpage() {
	if (false == wppedia_get_page_id('front'))
		return false;
		
	return true;
}
