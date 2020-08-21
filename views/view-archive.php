<?php
/**
 * The main template file.
 *
 * This is the default Template for WPPedia Pages.
 * All Template functions are available in inc/tpl-hooks.php
 * 
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wppedia
 */

get_header(); 

/**
 * wppedia_do_template_wrapper_start hook
 *
 * @hooked wppedia_template_wrapper_start -  10
 *
 */
do_action( 'wppedia_do_template_wrapper_start' );

?>

		<?php
		if ( have_posts() ) {

			/**
			 * wppedia_do_template_the_loop hook
			 *
			 * @hooked wppedia_template_the_loop -  10
			 *
			 */
			do_action( 'wppedia_do_template_the_loop' );

    } else {

			get_template_part( 'template-parts/content', 'none' );

    }
		?>

<?php

/**
 * wppedia_do_template_wrapper_end hook
 *
 * @hooked wppedia_template_wrapper_end -  10
 *
 */
do_action( 'wppedia_do_template_wrapper_end' );

get_footer();
