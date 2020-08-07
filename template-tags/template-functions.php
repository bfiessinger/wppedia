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

	if ( term_exists( $term_slug, 'wppedia_initial_letter' ) ) {
		// Get Information about the current term
		$obj = get_term_by( 'slug', $term_slug, 'wppedia_initial_letter' );
		
		$output .= '<a href="' . get_term_link( $obj ) . '"';
		$output .= ' title="' . sprintf(__('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $obj->name, $obj->count) . '"';
		
		if ( isset( get_queried_object()->term_id ) && $obj->term_id === get_queried_object()->term_id )
			$link_classes[] = $active_class;

		if ( ! empty( $link_classes ) )
			$output .= ' class="' . implode( ' ', $link_classes ) . '"';

		$output .= '>';

		$output .= apply_filters( 'wppedia_navigation_link__name', $obj->name );

		$output .= '</a>';

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