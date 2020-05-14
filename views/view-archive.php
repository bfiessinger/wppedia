<?php

get_header();

/**
 * wppedia_tpl_initial_nav hook
 *
 * @hooked wppedia_tpl_initial_nav -  10
 *
 */
do_action( 'wppedia_tpl_initial_nav' );

/**
 * wppedia_tpl_list_entries hook
 * 
 * @hooked wppedia_tpl_list_entries - 10
 * 
 */
do_action( 'wppedia_tpl_list_entries' );

get_footer();
