<?php

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
 * Loop item link open
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_postlink_open' ) ) {
	function wppedia_loop_postlink_open() { ?>
		<a href="<?php echo get_the_permalink(); ?>" title="<?php echo esc_html( get_the_title() ); ?>" rel="bookmark">
	<?php }
}

/**
 * Loop item link close
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_postlink_close' ) ) {
	function wppedia_loop_postlink_close() { ?>
		</a>
	<?php }
}

/**
 * Loop title
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_item_title' ) ) {
	function wppedia_loop_item_title() {
		the_title('<h2 class="wppedia-post-title entry-title">', '</h2>');
	}
}

/**
 * Loop excerpt
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_excerpt' ) ) {
	function wppedia_loop_excerpt() {
		if ('' === get_the_excerpt_wppedia()) {
			return;
		}		

		echo '<div class="entry-content">';
		the_excerpt_wppedia();
		echo '</div>';
	}
}

/**
 * Pagination
 * 
 * @since 1.0.0
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
