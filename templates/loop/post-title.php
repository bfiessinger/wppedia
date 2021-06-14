<?php

/**
 * Display the post title in loop templates
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/loop/title.php
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
 * @version 1.2.0
 */

?>
<a href="<?php echo get_the_permalink(); ?>" title="<?php echo esc_html( get_the_title() ); ?>" rel="bookmark">
	<?php the_title('<h2 class="wppedia-post-title entry-title">', '</h2>'); ?>
</a>
