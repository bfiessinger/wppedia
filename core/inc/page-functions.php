<?php

defined( 'ABSPATH' ) || die();

/**
 * Get WPPedia Frontpage URL
 * 
 * @param array $query_args array with query args to add
 * 
 * @return string final URL
 */
function get_wppedia_url( array $query_args = [] ) {
	
	$archive_url;
	if ( FALSE === wppedia_has_static_frontpage() )
		$archive_url = get_post_type_archive_link('wppedia_term');
	else
		$archive_url = get_permalink( wppedia_has_static_frontpage() );
	
	if ( ! empty( $query_args ) )
		$archive_url = add_query_arg( $query_args, $archive_url );
	
	return $archive_url;
	
}

/**
 * Determine if the currently viewed page is a wiki page
 * 
 * @uses wppedia_get_post_type
 * 
 * @since 1.0.0
 */
function is_wppedia_page( $query = false ) {
	
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}
	
	/**
	 * No Checks should be performed if the request is 404
	 * or we are not on the main query
	 */
	if ( is_404() || ! $query->is_main_query() )
		return false;
	
	$post_type = wppedia_get_post_type();
	$is_wppedia_post_type = false;
	
	global $wp;
	
	if ( 
		/**
		 * Check for singular and archive pages where there is only
		 * one given post type
		 */
		(
			! $query->is_search() &&
			get_post_type() == $post_type
		) ||
		/**
		 * Check for searches in the archive
		 */
		(
			$query->is_post_type_archive() &&
			rtrim( home_url( $wp->request ), '/' ) == rtrim( get_post_type_archive_link( $post_type ), '/' )
		) ||
		/**
		 * Check for requests to the custom selected static WPPedia front page
		 */
		get_the_ID() === intval( wppedia_has_static_frontpage() )
	)
		$is_wppedia_post_type = $post_type;
		
	return $is_wppedia_post_type;
			
}

/**
 * Determine if the current view is a wiki search
 * 
 * @uses is_wppedia_page
 * 
 * @since 1.0.0
 */
function is_wppedia_search( $query = false ) {
	
	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}
	
	if ( $query->is_search() && is_wppedia_page( $query ) )
		return true;
	
	return false;
	
}

function is_wppedia_archive( $query = false ) {

	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	if ( ! $query->is_post_type_archive() && get_post_type() == wppedia_get_post_type() )
		return true;

	return false;

}

function is_wppedia_singular( $query = false ) {

	if ( ! $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	if ( ! $query->is_singular() && get_post_type() == wppedia_get_post_type() )
		return true;

	return false;

}

/**
 * Determine if the currently viewed page is the wiki homepage
 * 
 * @since 1.0.0
 */
function is_wppedia_frontpage() {
	
	if ( is_post_type_archive( 'wppedia_term' ) || get_the_ID() === intval( wppedia_has_static_frontpage() ) )
		return true;
	
	return false;
	
}

/**
 * Determine if a static page is used for the wpPedia front page
 * 
 * @since 1.0.0
 * 
 * @return boolean|int Returns false if a cpt archive is used or the post ID of the static page
 */
function wppedia_has_static_frontpage() {
	if (false == get_option('wppedia_frontpage', false))
		return false;
		
	return get_option('wppedia_frontpage', false);
}