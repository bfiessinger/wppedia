<?php
/**
 * The default template for displaying glossary archives.
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/archive.php
 * 
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wppedia
 */

defined( 'ABSPATH' ) || exit;

get_header('wppedia'); 

/**
 * Hook: wppedia_before_main_content.
 *
 * @hooked wppedia_wrapper_start - 10 (outputs opening divs for the content)
 * @hooked wppedia_navigation -  20
 * @hooked wppedia_searchform -  30
 */
do_action( 'wppedia_before_main_content' );

if ( have_posts() ) {

	/**
	 * Hook: wppedia_before_post_loop.
	 *
	 * @hooked wppedia_postlist_wrapper_start - 10 (outputs opening divs for the postlist)
	 */
	do_action( 'wppedia_before_post_loop' );

	while ( have_posts() ) {
		the_post();

		/**
		 * wppedia_loop_content hook
		 *
		 * @hooked wppedia_loop_item_wrapper_start -  10
		 * @hooked wppedia_loop_title -  20
		 * @hooked wppedia_loop_excerpt -  30
		 * @hooked wppedia_loop_item_wrapper_end -  30
		 *
		 */
		do_action( 'wppedia_loop_content' );

	}

	/**
	 * Hook: wppedia_after_post_loop.
	 *
	 * @hooked wppedia_postlist_wrapper_end - 10 (outputs closing divs for the postlist)
	 */
	do_action( 'wppedia_after_post_loop' );

} else {

	/**
	 * Hook: wppedia_no_entries_found.
	 *
	 * @hooked wppedia_no_entries_found - 10
	 */
	do_action( 'wppedia_no_entries_found' );

}

/**
 * wppedia_after_main_content hook
 *
 * @hooked wppedia_loop_pagination -  10
 * @hooked wppedia_wrapper_end -  20 (outputs closing divs for the content)
 */
do_action( 'wppedia_after_main_content' );

/**
 * Hook: wppedia_sidebar.
 *
 * @hooked wppedia_sidebar - 10
 */
do_action( 'wppedia_sidebar' );

get_footer('wppedia');
