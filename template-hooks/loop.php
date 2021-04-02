<?php

/**
 * Loop wrapper start
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_postlist_wrapper_start' ) ) {
	function wppedia_postlist_wrapper_start() { ?>
		<div class="wppedia-loop-wrapper">
	<?php }
}

/**
 * Loop wrapper end
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_postlist_wrapper_end' ) ) {
	function wppedia_postlist_wrapper_end() { ?>
		</div>
	<?php }
}

/**
 * Loop item wrapper start
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_item_wrapper_start' ) ) {
	function wppedia_loop_item_wrapper_start() { ?>
		<article id="wppedia-post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php }
}

/**
 * Loop item wrapper end
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_item_wrapper_end' ) ) {
	function wppedia_loop_item_wrapper_end() { ?>
		</article>
	<?php }
}


/**
 * Loop title
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_title' ) ) {
	function wppedia_loop_title() { ?>
		<a href="<?php echo get_the_permalink(); ?>" title="<?php echo esc_html( get_the_title() ); ?>">
		<?php the_title('<h2 class="wppedia-post-title">', '</h2>'); ?>
		</a>
	<?php }
}

/**
 * Loop excerpt
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_loop_excerpt' ) ) {
	function wppedia_loop_excerpt() {
		the_excerpt_wppedia();
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
			'next_text' => _x( 'Next', 'Next post', 'wppedia' ),
			'prev_text' => _x( 'Previous', 'Previous post', 'wppedia' ),
		];

		$pagination_args = apply_filters('wppedia_posts_pagination_arguments', $pagination_args);

		the_posts_pagination( $pagination_args );
	}
}
