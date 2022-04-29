<?php

/**
 * Define constants if they are not already defined
 * 
 * @param string $name  Constant name.
 * @param mixed  $value Value.
 * 
 * @since 1.0.0
 */
function wppedia_maybe_define_constant(string $name, $value) {
	if (!defined($name)) {
		define($name, $value);
	}
}

/**
 * Check if an option exists in the database
 * 
 * @param string $option_name Option name.
 * @param bool   $site_wide   Whether to check the site-wide options or not.
 * 
 * @return mixed
 * 
 * @since 1.3.0
 */
function wppedia_option_exists($name, $site_wide = false) {
	global $wpdb; 
	return $wpdb->query("SELECT * FROM ". ($site_wide ? $wpdb->base_prefix : $wpdb->prefix). "options WHERE option_name ='$name' LIMIT 1");
}

/**
 * Generate a valid slug from a string
 * 
 * @param string $str 
 * 
 * @return string - a valid slug
 * 
 * @since 1.2.1
 */
function wppedia_slugify( string $str, string $default ) {
	
	// replace non letter or digits by -
	$str = preg_replace( '~[^\pL\d]+~u', '-', $str );
	
	// transliterate
	$str = iconv( 'utf-8', 'us-ascii//TRANSLIT', $str );
	
	// remove unwanted characters
	$str = preg_replace( '~[^-\w]+~', '', $str );
	
	// trim
	$str = trim( $str, '-' );
	
	// remove duplicate -
	$str = preg_replace( '~-+~', '-', $str );
	
	// lowercase
	$str = strtolower( $str );
	
	if ( empty( $str ) ) {
		return $default;
	}
	
	// run a last urlencode to be sure to get a valid slug
	$str = \rawurlencode( $str );
	
	return $str;
	
}

/**
 * Lists all available initial letters
 * 
 * @since 1.0.0
 */
function wppedia_list_chars() {
	
  $initials = [
    // Default letters
		'a' => 'a',
		'b' => 'b',
		'c' => 'c', 
		'd' => 'd', 
		'e' => 'e', 
		'f' => 'f', 
		'g' => 'g', 
		'h' => 'h', 
		'i' => 'i', 
		'j' => 'j', 
		'k' => 'k', 
		'l' => 'l', 
		'm' => 'm', 
		'n' => 'n', 
		'o' => 'o', 
		'p' => 'p', 
		'q' => 'q', 
		'r' => 'r', 
		's' => 's', 
		't' => 't', 
		'u' => 'u', 
		'v' => 'v', 
		'w' => 'w', 
		'x' => 'x', 
		'y' => 'y', 
		'z' => 'z'
  ];
	
  return apply_filters('wppedia_list_chars', $initials);
	
}
