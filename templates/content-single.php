<?php
/**
 * Manage the content of a singular WPPedia Term.
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/content-single.php
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

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<article id="wppedia-post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	/**
	 * Hook: wppedia_single_post.
	 * 
	 * @hooked wppedia_single_title - 10
	 * @hooked wppedia_single_content - 20
	 * @hooked wppedia_single_link_pages - 30
	 */
	do_action('wppedia_single_post');
?>
</article>
