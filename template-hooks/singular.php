<?php

/**
 * Starting tag for entry content wrapper
 * 
 * @since 1.0.0
 */
if (!function_exists('wppedia_entry_content_start')) {
  function wppedia_entry_content_start() { ?>
		<div class="entry-content">
	<?php }
}

/**
 * Closing tag for entry content wrapper
 * 
 * @since 1.0.0
 */
if (!function_exists('wppedia_entry_content_end')) {
	function wppedia_entry_content_end() { ?>
		</div>
	<?php }
}

/**
 * Singular Featured Image
 * 
 * @since 1.1.3
 */
if ( ! function_exists( 'wppedia_single_featured_image' ) ) {
	function wppedia_single_featured_image() {
		if (has_post_thumbnail()) {
			echo '<div class="wppedia-featured-image-wrapper">';
			the_post_thumbnail('post-thumbnail', ['class' => 'wppedia-post-thumbnail']);
			echo '</div>';
		}
	}
}

/**
 * Singular title
 * 
 * @since 1.0.0
 */
if (!function_exists('wppedia_single_title')) {
	function wppedia_single_title() {
		the_title('<h1 class="wppedia-title entry-title">', '</h1>');
	}
}

/**
 * Singular content
 * 
 * @since 1.0.0
 */
if (!function_exists('wppedia_single_content')) {
	function wppedia_single_content() {
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'wppedia' ),
					[
						'span' => [
							'class' => [],
						],
					]
				),
				get_the_title()
			)
		);
	}
}

/**
 * Link pages
 * 
 * @since 1.0.0
 */
if (!function_exists('wppedia_single_link_pages')) {
	function wppedia_single_link_pages() {
		wp_link_pages(
			[
				'before' 						=> '<div class="site-links">',
				'after'  						=> '</div>',
				'link_before'      	=> '<div class="site-link">',
				'link_after'       	=> '</div>',
				'nextpagelink'     	=> __( 'Next page', 'wppedia'),
				'previouspagelink' 	=> __( 'Previous page', 'wppedia' ),
			]
		);
	}
}
