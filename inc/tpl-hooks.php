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

	function wppedia_template_wrapper_start() { 
		
		if ( bf\wpPedia\template::getInstance()->wppedia_has_sidebar() ): ?>

			<div id="wppedia_page_wrap">

		<?php endif; ?>

				<main id="wppedia_primary">
					<div class="wppedia_container">

	<?php }

}
add_action( 'wppedia_do_template_wrapper_start', 'wppedia_template_wrapper_start', 10 );

/**
 * End of the main Wrapper of WPPedia content
 * 
 * @since 1.0.0
 */
if ( ! function_exists( 'wppedia_template_wrapper_end' ) ) {

	function wppedia_template_wrapper_end() { ?>

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

		<aside id="wppedia_secondary" role="complementary">
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
if ( ! function_exists( 'wppedia_template_the_loop' ) ) {

	function wppedia_template_the_loop() {

    while ( have_posts() ) {

      the_post();
    
      if ( is_archive() ) {

				$layout_option = 'list'; // -> MUST BE OPTIONAL

				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php do_action( "wppedia_do_template_archive_layout__{$layout_option}" ); ?>
				</article>
				<?php

			} elseif ( is_singular() ) {

				// Single View

			}

    }

	}

}
add_action( 'wppedia_do_template_the_loop', 'wppedia_template_the_loop', 10 );

if ( ! function_exists( 'wppedia_template_archive_layout__list' ) ) {

	function wppedia_template_archive_layout__list() {

		echo get_the_title();

	}

}
add_action( 'wppedia_do_template_archive_layout__list', 'wppedia_template_archive_layout__list', 10 );
