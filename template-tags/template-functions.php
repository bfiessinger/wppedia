<?php

/**
 * WPPedia Template Hooks and Filters
 */

/**
 * Initial Character Navigation
 * 
 * @param string $term_slug - Initial Character taxonomy slug
 */
function wppedia_navigation_link( string $term_slug ) {

	$output = '';

	$link_name = null;
	$link_url = null;
	$link_title = '';

	/**
	 * Filter for common link Classes
	 * 
	 * @param array $link_classes - Array with classes for all link elements
	 */
	$link_classes = apply_filters( 'wppedia_navigation_link__classes', [] );

	/**
	 * Filter for the active link class
	 * 
	 * @param string $active_class - Classname for the active element
	 */
	$active_class = apply_filters( 'wppedia_navigation_link__active_class', 'active' );

	if ( 'home' == $term_slug ) {

		$link_name = __( 'home', 'wppedia' );
		$link_url = get_post_type_archive_link( 'wppedia_term' );
		$link_title = __( 'home', 'wppedia' );
		$link_classes[] = 'wppedia_navigation_home';

		if ( wppedia_utils()->is_wiki_home() )
			$link_classes[] = $active_class;

		$output .= wppedia_navigation_link_anchor( $link_name, $link_url, $link_title, $link_classes );

	} else if ( term_exists( $term_slug, 'wppedia_initial_letter' ) ) {

		// Get Information about the current term
		$obj = get_term_by( 'slug', $term_slug, 'wppedia_initial_letter' );

		$link_name = $obj->name;
		$link_url = get_term_link( $obj );
		$link_title = sprintf( __('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $obj->name, $obj->count );

		if ( isset( get_queried_object()->term_id ) && $obj->term_id === get_queried_object()->term_id )
			$link_classes[] = $active_class;

		$output .= wppedia_navigation_link_anchor( $link_name, $link_url, $link_title, $link_classes );

	} else  {

		$output .= '<span';

		if ( ! empty( $link_classes ) ) {
			$output .= ' class="';
			$output .= implode( ' ', $link_classes );
			$output .= '"';
		}

		$output .= '>';
		$output .= apply_filters( 'wppedia_navigation_link__name', $term_slug );
		$output .= '</span>';

	}

	return apply_filters( 'wppedia_navigation_link', $output );

}

function wppedia_navigation_link_anchor( string $name, string $url, string $title = '', array $classes = [] ) {
		
	$link_html = '<a href="' . $url . '"';
	$link_html .= ' title="' . $title . '"';

	if ( ! empty( $classes ) )
		$link_html .= ' class="' . implode( ' ', $classes ) . '"';

	$link_html .= '>';

	$link_html .= apply_filters( 'wppedia_navigation_link__name', $name );

	$link_html .= '</a>';

	return $link_html;

}

/**
 * Searchform
 */
function wppedia_searchform() {

	$post_type = 'wp_pedia_term';
  $searchUrl = get_post_type_archive_link( $post_type );

?>

	<form role="search" method="get" class="search-form wppedia-search" action="<?php echo $searchUrl ?>">
		<label class="screen-reader-text"><?php _e('Search glossary', 'wppedia'); ?></label>
		<input type="search" class="search-field" placeholder="<?php _e('Search glossary', 'wppedia'); ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php _e('Search for', 'wppedia'); ?>:" />
		<input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
		<input type="submit" class="search-submit" value="Search" />
	</form>
	
<?php

}