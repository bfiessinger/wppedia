<?php

/**
 * WPPedia Template Hooks and Filters
 */

function wppedia_navigation_link( string $term_slug, string $title = null, array $classes = [], string $active_class = null ) {

	$output = '';

	if ( ! $active_class )
		$active_class = 'active';

	if ( term_exists( $term_slug, 'wppedia_initial_letter' ) ) {
		// Get Information about the current term
		$obj = get_term_by( 'slug', $term_slug, 'wppedia_initial_letter' );
		
		$output .= '<a href="' . get_term_link( $obj ) . '"';
		$output .= ' title="' . sprintf(__('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $obj->name, $obj->count) . '"';
		
		if ( $obj->term_id === get_queried_object()->term_id )
			$classes[] = $active_class;

		if ( ! empty( $classes ) )
			$output .= ' class="' . implode( ' ', $classes ) . '"';

		$output .= '>';

		$output .= $obj->name;

		$output .= '</a>';

	} else  {

		$output .= '<span';

		if ( ! empty( $classes ) ) {
			$output .= ' class="';
			$output .= implode( ' ', $classes );
			$output .= '"';
		}

		$output .= '>';
		$output .= $term_slug;
		$output .= '</span>';

	}

	return apply_filters( 'wppedia_navigation_link', $output, $term_slug, $title, $classes, $active_class );

}
