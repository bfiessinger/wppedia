<?php

/**
 * Get the default post type for WPPedia Posts
 * 
 * @return string
 * 
 * @since 1.0.0
 */
function wppedia_get_post_type() {
	return WPPedia\postType::getInstance()->post_types['main'];
}

/**
 * Get Wiki Entries
 * 
 * @since 1.2.0
 * 
 * @return WP_Query WordPress Query Result
 */
function wppedia_get_posts(array $query_args = []) {

  $query_defaults = [
    'post_type' => 'wppedia_term',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC'
  ];
	
  $query_args = array_merge($query_defaults, $query_args);
	
  $posts = new \WP_Query($query_args);
	
  return $posts;
	
}

/**
 * Retrieve the alternative terms supplied for a
 * given post_id
 * 
 * @since 1.1.0
 */
function wppedia_get_post_alternative_terms(int $post_id) {
	$alt_terms_meta = json_decode(get_post_meta($post_id, 'wppedia_post_alt_tags', true));
	if (is_array($alt_terms_meta)) {
		return array_column($alt_terms_meta, 'value');
	} elseif (is_string($alt_terms_meta)) {
		return [esc_attr($alt_terms_meta)];
	}
	return null;
}

/**
 * Get the initial letter from an post
 * 
 * @uses wppedia_list_chars
 * 
 * @param int|WP_POST $post
 * 
 * @return string the first character
 */
function wppedia_get_post_initial_letter( $post ) {
	
	$post_initial_letter = \strtolower( substr( get_the_title( $post ), 0, 1 ) );
	
	if ( ! in_array( $post_initial_letter, wppedia_list_chars() ) ) {
		$post_initial_letter = '#';
	}
	
	return strtolower( $post_initial_letter );
	
}

/**
 * Get initial letters of current Glossary Entries
 * 
 * @param array $args Arguments to modify the output of initial letters
 * 
 * @since 1.0.0
 */
function wppedia_get_posts_initial_letter_list(array $args = []) {
	
	// Default settings array
  $defaults = [
		'show_option_home' => false,
		'hide_empty' => true
	];
	
	// Build final settings array
  $settings = array_merge($defaults, $args);
	
	$available_initial_chars = [];
	
	// Get available initial char terms
	$initial_char_terms = get_terms( [
		'taxonomy' => 'wppedia_initial_letter',
		'hide_empty' => $settings['hide_empty'],
	] );
		
	// Loop over all available terms and get their slugs and names
	foreach ( $initial_char_terms as $wp_term ) {
		$available_initial_chars[$wp_term->slug] = $wp_term->name;
	}
	
	$available_initial_chars = array_unique( $available_initial_chars );
	
	if ( false !== $settings['hide_empty'] )
		return $available_initial_chars;
	
	$available_initial_chars = array_unique( array_replace( $available_initial_chars, wppedia_list_chars() ) );
	
	// Sort Array and keep indexes
	asort( $available_initial_chars );
	
	// Add all option after sorting
	if ( false !== $settings['show_option_home'] )
		$available_initial_chars = array_merge( ['home' => 'home'], $available_initial_chars );
	
	return $available_initial_chars;
	
}
