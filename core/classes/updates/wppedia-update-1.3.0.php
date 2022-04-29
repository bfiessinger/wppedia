<?php

/**
 * Update WPPedia to version 1.3.0
 *
 * @since 1.3.0
 */

defined( 'ABSPATH' ) or die();

use WPPedia\options;

/**
 * Options to be updated
 */
if (wppedia_option_exists('wppedia_front_page_id') && !options::option_exists('general', 'front_page_id')) {
    options::update_option('general', 'front_page_id', get_option('wppedia_front_page_id'));
    delete_option('wppedia_front_page_id');
}

if (wppedia_option_exists('wppedia_feature_crosslinks') && !options::option_exists('crosslinks', 'active')) {
    options::update_option('crosslinks', 'active', get_option('wppedia_feature_crosslinks'));
    delete_option('wppedia_feature_crosslinks');
}

if (wppedia_option_exists('wppedia_crosslinks_prefer_single_words') && !options::option_exists('crosslinks', 'prefer_single_words')) {
    options::update_option('crosslinks', 'prefer_single_words', get_option('wppedia_crosslinks_prefer_single_words'));
    delete_option('wppedia_crosslinks_prefer_single_words');
}

if (wppedia_option_exists('wppedia_crosslinks_posttypes') && !options::option_exists('crosslinks', 'posttypes')) {
    options::update_option('crosslinks', 'posttypes', get_option('wppedia_crosslinks_posttypes'));
    delete_option('wppedia_crosslinks_posttypes');
}

if (wppedia_option_exists('wppedia_feature_tooltips') && !options::option_exists('tooltips', 'active')) {
    options::update_option('tooltips', 'active', get_option('wppedia_feature_tooltips'));
    delete_option('wppedia_feature_tooltips');
}

if (wppedia_option_exists('wppedia_tooltips_style') && !options::option_exists('tooltips', 'style')) {
    options::update_option('tooltips', 'style', get_option('wppedia_tooltips_style'));
    delete_option('wppedia_tooltips_style');
}

if (wppedia_option_exists('wppedia_singular_use_templates') && !options::option_exists('singular', 'wppedia_templates')) {
    options::update_option('singular', 'wppedia_templates', get_option('wppedia_singular_use_templates'));
    delete_option('wppedia_singular_use_templates');
}

if (wppedia_option_exists('wppedia_singular_show_navigation') && !options::option_exists('singular', 'show_nav')) {
    options::update_option('singular', 'show_nav', get_option('wppedia_singular_show_navigation'));
    delete_option('wppedia_singular_show_navigation');
}

if (wppedia_option_exists('wppedia_singular_show_searchbar') && !options::option_exists('singular', 'show_searchbar')) {
    options::update_option('singular', 'show_searchbar', get_option('wppedia_singular_show_searchbar'));
    delete_option('wppedia_singular_show_searchbar');
}

if (wppedia_option_exists('wppedia_archive_use_templates') && !options::option_exists('archive', 'wppedia_templates')) {
    options::update_option('archive', 'wppedia_templates', get_option('wppedia_archive_use_templates'));
    delete_option('wppedia_archive_use_templates');
}

if (wppedia_option_exists('wppedia_archive_show_navigation') && !options::option_exists('archive', 'show_nav')) {
    options::update_option('archive', 'show_nav', get_option('wppedia_archive_show_navigation'));
    delete_option('wppedia_archive_show_navigation');
}

if (wppedia_option_exists('wppedia_archive_show_searchbar') && !options::option_exists('archive', 'show_searchbar')) {
    options::update_option('archive', 'show_searchbar', get_option('wppedia_archive_show_searchbar'));
    delete_option('wppedia_archive_show_searchbar');
}

if (wppedia_option_exists('wppedia_posts_per_page') && !options::option_exists('archive', 'posts_per_page')) {
    options::update_option('archive', 'posts_per_page', get_option('wppedia_posts_per_page'));
    delete_option('wppedia_posts_per_page');
}

if (wppedia_option_exists('wppedia_permalink_base') && !options::option_exists('permalinks', 'base')) {
    options::update_option('permalinks', 'base', get_option('wppedia_permalink_base'));
    delete_option('wppedia_permalink_base');
}

if (wppedia_option_exists('wppedia_permalink_use_initial_character') && !options::option_exists('permalinks', 'use_initial_character')) {
    options::update_option('permalinks', 'use_initial_character', get_option('wppedia_permalink_use_initial_character'));
    delete_option('wppedia_permalink_use_initial_character');
}
