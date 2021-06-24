<?php

/**
 * Display the description of the static frontpage
 * 
 * @since 1.3.0
 */
if ( ! function_exists( 'wppedia_frontpage_archive_description' ) ) {
	function wppedia_frontpage_archive_description() {
		// Don't display the description on search results page 
		if ( is_search() ) { 
			return; 
		}

		if ( is_wppedia_archive() && 0 === absint( get_query_var( 'paged' ) ) ) { 
			$frontpage = get_post( wppedia_get_page_id( 'front' ) ); 
			if ( $frontpage ) { 
				$description = apply_filters('the_content', $frontpage->post_content); 
				if ( $description ) { 
					echo '<div class="wppedia-page-description">' . $description . '</div>'; 
				} 
			} 
		} 
	}
}

/**
 * Display a taxonomy description
 * 
 * @since 1.3.0
 */
if ( ! function_exists( 'wppedia_taxonomy_archive_description' ) ) {
	function wppedia_taxonomy_archive_description() {
		if ( is_tax() && 0 === absint( get_query_var( 'paged' ) ) ) {
			$description = apply_filters('the_content', term_description());
			if ( $description ) { 
				echo '<div class="wppedia-term-description">' . $description . '</div>';
			}
		} 
	}
}

/**
 * Loop wrapper start
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_postlist_wrapper_start' ) ) {
	function wppedia_postlist_wrapper_start() {
		wppedia_get_template_part('loop/wrapper', 'start');
	}
}

/**
 * Loop wrapper end
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_postlist_wrapper_end' ) ) {
	function wppedia_postlist_wrapper_end() {
		wppedia_get_template_part('loop/wrapper', 'end');
	}
}

/**
 * Loop title
 * 
 * @since 1.2.0
 */
if ( ! function_exists( 'wppedia_loop_item_title' ) ) {
	function wppedia_loop_item_title() {
		wppedia_get_template_part('loop/post', 'title');
	}
}

/**
 * Loop Featured Image
 * 
 * @since 1.2.0
 */
if ( ! function_exists( 'wppedia_loop_featured_image' ) ) {
	function wppedia_loop_featured_image() {
		if (has_post_thumbnail()) {
			wppedia_get_template_part('loop/post', 'featured-image');
		}
	}
}

/**
 * Loop excerpt
 * 
 * @since 1.2.0
 */
if ( ! function_exists( 'wppedia_loop_excerpt' ) ) {
	function wppedia_loop_excerpt() {
		if ('' === get_the_excerpt_wppedia()) {
			return;
		}		

		wppedia_get_template_part('loop/post', 'excerpt');
	}
}

/**
 * Pagination
 * 
 * @since 1.1.3
 */
if ( ! function_exists( 'wppedia_posts_pagination' ) ) {
	function wppedia_posts_pagination() {
		$pagination_args = [
			'type'      => 'list',
			'next_text' => _x( 'Next', 'pagination-args', 'wppedia' ),
			'prev_text' => _x( 'Previous', 'pagination-args', 'wppedia' ),
		];

		$pagination_args = apply_filters('wppedia_posts_pagination_arguments', $pagination_args);

		the_posts_pagination( $pagination_args );
	}
}
