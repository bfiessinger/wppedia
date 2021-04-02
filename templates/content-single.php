<?php

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