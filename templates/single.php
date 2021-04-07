<?php
/**
 * The default template for displaying singular glossary articles.
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/singular.php
 * 
 * ATTENTION!
 * In case WPPedia needs to make changes to the template files, you (the theme developer)
 * will need to copy these new template files to maintain compatibility.
 * 
 * Whenever we make changes to the template files we will bump the version and list all changes
 * in the CHANGELOG.md file.
 * 
 * Happy editing!
 * 
 * @see https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wppedia
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header('wppedia');

/**
 * Hook: wppedia_before_main_content.
 *
 * @hooked wppedia_wrapper_start - 10 (outputs opening divs for the content)
 */
do_action( 'wppedia_before_main_content' );

if ( have_posts() ) {

	/**
	 * Hook: wppedia_before_single_post.
	 *
	 * @hooked wppedia_entry_content_start - 10 (outputs entry content opening divs)
	 */
	do_action( 'wppedia_before_single_post' );

	while ( have_posts() ) {
		the_post();

		wppedia_get_template_part('content', 'single');

	}

	/**
	 * Hook: wppedia_after_single_post.
	 *
	 * @hooked wppedia_entry_content_end - 10 (outputs entry content closing divs)
	 */
	do_action( 'wppedia_after_single_post' );

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
 * @hooked wppedia_wrapper_end -  10 (outputs closing divs for the content)
 *
 */
do_action( 'wppedia_after_main_content' );

/**
 * Hook: wppedia_sidebar.
 *
 * @hooked wppedia_sidebar - 10
 */
do_action( 'wppedia_sidebar' );

get_footer('wppedia');
