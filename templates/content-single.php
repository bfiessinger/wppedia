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