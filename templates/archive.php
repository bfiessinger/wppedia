<?php
/**
 * The default template for displaying glossary archives.
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/archive.php
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
 * @version 1.1.3
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

?>
<header class="wppedia-archive-header">
	<?php if ( apply_filters('wppedia_show_page_title', true) ) : ?>
		<h1 class="wppedia-archive-header__title page-title"><?php wppedia_page_title(); ?></h1>
	<?php endif; ?>
	
	<?php
		/**
		 * Hook: wppedia_archive_description.
		 *
		 * @hooked wppedia_taxonomy_archive_description - 10
		 * @hooked wppedia_frontpage_archive_description - 10
		 */
		do_action( 'wppedia_archive_description' );
	?>
</header>

<?php

if ( have_posts() ) {

	/**
	 * Hook: wppedia_before_post_loop.
	 *
	 * @hooked wppedia_postlist_wrapper_start - 10 (outputs opening divs for the postlist)
	 */
	do_action( 'wppedia_before_post_loop' );

	while ( have_posts() ) {
		the_post();
		wppedia_get_template_part('loop/char', 'index');
		wppedia_get_template_part('content', 'archive');
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
