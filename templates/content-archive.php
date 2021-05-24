<?php
/**
 * Manage the content of a WPPedia term in the post loop.
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

?>
<article id="wppedia-post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	/**
	 * Hook: wppedia_before_loop_item.
	 */
	do_action( 'wppedia_before_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked wppedia_loop_postlink_open - 10
	 * @hooked wppedia_loop_featured_image - 20
	 */
	do_action( 'wppedia_before_loop_item_title' );

	/**
	 * Hook: wppedia_loop_item_title.
	 *
	 * @hooked wppedia_loop_item_title - 10
	 */
	do_action( 'wppedia_loop_item_title' );

	/**
	 * Hook: wppedia_after_loop_item_title.
	 *
	 * @hooked wppedia_loop_excerpt - 20
	 * @hooked wppedia_loop_postlink_close - 10
	 */
	do_action( 'wppedia_after_loop_item_title' );

	/**
	 * Hook: wppedia_after_loop_item.
	 */
	do_action( 'wppedia_after_loop_item' );
?>
</article>
