<?php

/**
 * Public available functions and declarations
 * 
 * @since 1.0.0
 */

/**
 * Prints the Template for the Searchform
 * 
 * @uses get_searchform()
 * @see bf\wpPedia\template
 * 
 * @return void
 * 
 * @since 1.0.0
 */
function get_wppedia_searchform() {
	wppedia_template()->get_searchform();
}

/**
 * Returns whether the current view is from the glossary
 * 
 * @uses is_wiki_post_type()
 * @see bf\wpPedia\helper
 * 
 * @return boolean
 * 
 * @since 1.0.0
 */
function is_wppedia() {
	return wppedia_utils()->is_wiki_post_type();
}

function wppedia_add_inline_style( string $stylesheet ) {
	return \bf\wpPedia\inline_style_collector::getInstance( $stylesheet );
}
