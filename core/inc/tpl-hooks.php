<?php

/**
 * WPPedia Template Hooks
 * 
 * @since 1.0.0
 */

/**
 * Start of the main wrapper of WPPedia content
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_template_wrapper_start' ) ) {

	function wppedia_template_wrapper_start() { ?>

		<div id="wppedia-page-header">
			<?php
				// Search form
				bf\wpPedia\template::getInstance()->get_search_form();
				// Navigation
				bf\wpPedia\template::getInstance()->get_char_navigation();
			?>
		</div>

		<?php if ( bf\wpPedia\template::getInstance()->wppedia_has_sidebar() ): ?>

			<div id="wppedia-layout-wrap">

		<?php endif; ?>

				<main id="primary">
					<div class="wppedia-entry-content">

	<?php }

}
add_action( 'wppedia_do_template_wrapper_start', 'wppedia_template_wrapper_start', 10 );

/**
 * End of the main Wrapper of WPPedia content
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_template_wrapper_end' ) ) {

	function wppedia_template_wrapper_end() {

				/**
				 * Pagination
				 */
				$pagination_args = array(
					'type'      => 'list',
					'next_text' => _x( 'Next', 'Next post', 'wppedia' ),
					'prev_text' => _x( 'Previous', 'Previous post', 'wppedia' ),
				);

				the_posts_pagination( $pagination_args ); ?>

				</div>
			</main><!-- #wppedia_primary -->

		<?php

		if ( bf\wpPedia\template::getInstance()->wppedia_has_sidebar() ) {

			/**
			 * wppedia_do_template_sidebar hook
			 *
			 * @hooked wppedia_template_sidebar -  10
			 *
			 */
			do_action( 'wppedia_do_template_sidebar' ); ?>

		</div><!-- #wppedia_page_wrap -->

		<?php

		}

	}

}
add_action( 'wppedia_do_template_wrapper_end', 'wppedia_template_wrapper_end', 10 );

/**
 * Display the WPPedia Sidebar
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_template_sidebar' ) ) {

	function wppedia_template_sidebar() { ?>

		<aside id="secondary" role="complementary">
			<?php dynamic_sidebar( 'sidebar_wppedia' ); ?>
		</aside>

	<?php }

}
add_action( 'wppedia_do_template_sidebar', 'wppedia_template_sidebar', 10 );

/**
 * WPPedia Post Loop
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_template_archive_layout__default' ) ) {

	function wppedia_template_archive_layout__default() { ?>

		<div class="wppedia-entry-wrapper wppedia-layout-default wppedia-columns wppedia-columns-3">

		<?php while ( have_posts() ) {

			the_post();
			
			?>
			
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<a href="<?php echo get_the_permalink(); ?>" title="<?php echo esc_html( get_the_title() ); ?>">
				<?php 
					// Print the title
					the_title('<h2 class="wppedia-post-title">', '</h2>'); 
					// Print the excerpt
					bf\wpPedia\template::getInstance()->the_excerpt( null, 25, false );
				?>
				</a>
			</article>
			
			<?php

		} ?>

	</div>

		<?php

	}

}
add_action( 'wppedia_do_template_archive_layout__default', 'wppedia_template_archive_layout__default', 10 );

if ( ! function_exists( 'wppedia_template_singular_layout' ) ) {

	function wppedia_template_singular_layout() {

		the_title('<h1 class="wppedia-title">', '</h1>');
	
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'prox' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
				
		wp_link_pages(
			array(
				'before' => '<div class="site-links">',
				'after'  => '</div>',
				'link_before'      => '<div class="site-link">',
				'link_after'       => '</div>',
				'nextpagelink'     => __( 'Next page', 'domino'),
				'previouspagelink' => __( 'Previous page', 'domino' ),
			)
		);
	
	}

}
add_action( 'wppedia_do_template_singular_layout', 'wppedia_template_singular_layout', 10 );
