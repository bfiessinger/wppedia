<?php

/**
 * Global template hooks
 */
require_once __DIR__ . '/global.php';

/**
 * Loop and post template hooks
 */
require_once __DIR__ . '/loop.php';
require_once __DIR__ . '/singular.php';

/**
 * Modules
 */
require_once __DIR__ . '/nav.php';
require_once __DIR__ . '/search.php';

/**
 * Hook: wppedia_before_main_content
 * 
 */
add_action( 'wppedia_before_main_content', 'wppedia_wrapper_start', 10 );
add_action( 'wppedia_before_main_content', 'wppedia_navigation', 20 );
add_action( 'wppedia_before_main_content', 'wppedia_searchform', 30 );

/**
 * Hook: wppedia_before_single_post
 * 
 */
add_action( 'wppedia_before_single_post', 'wppedia_entry_content_start', 10 );
add_action( 'wppedia_single_post', 'wppedia_single_title', 10 );
add_action( 'wppedia_single_post', 'wppedia_single_content', 20 );
add_action( 'wppedia_single_post', 'wppedia_single_link_pages', 30 );

/**
 * Hook: wppedia_loop_content
 * 
 */
add_action( 'wppedia_before_post_loop', 'wppedia_postlist_wrapper_start', 10 );
 
add_action( 'wppedia_before_loop_item_title', 'wppedia_loop_postlink_open', 10 );

add_action( 'wppedia_loop_item_title', 'wppedia_loop_item_title', 10 );

add_action( 'wppedia_after_loop_item_title', 'wppedia_loop_excerpt', 20 );
add_action( 'wppedia_after_loop_item_title', 'wppedia_loop_postlink_close', 10 );

add_action( 'wppedia_after_post_loop', 'wppedia_postlist_wrapper_end', 10 );

/**
 * Hook: wppedia_after_single_post
 * 
 */
add_action( 'wppedia_after_single_post', 'wppedia_entry_content_end', 10 );

/**
 * Hook: wppedia_sidebar
 * 
 */
add_action( 'wppedia_sidebar', 'wppedia_sidebar', 10 );


/**
 * Hook: wppedia_after_main_content
 *
 */
add_action( 'wppedia_after_main_content', 'wppedia_posts_pagination', 20 );
add_action( 'wppedia_after_main_content', 'wppedia_wrapper_end', 10 );
