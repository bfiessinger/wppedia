<?php

/**
 * Update WPPedia to version 1.1.0
 *
 * @since 1.3.0
 */

defined( 'ABSPATH' ) or die();

/**
 * Options to be updated
 */
if (wppedia_option_exists('wppedia_frontpage') && !wppedia_option_exists('wppedia_front_page_id')) {
    add_option('wppedia_front_page_id', get_option('wppedia_frontpage'), '', false);
    remove_option('wppedia_frontpage');
}
