<?php

function testrev() {

	global $wpdb;

	$post_id = get_the_ID();
	$revisions = $wpdb->get_results("select * from {$wpdb->posts} where post_parent={$post_id} and post_type='revision'");

	ob_start();
	echo '<pre>';
	print_r($revisions);
	echo '</pre>';
	return ob_get_clean();


	ob_start();
	wp_list_post_revisions( get_the_ID(), 'revision' );
	$revisions = ob_get_clean();

	return $revisions;

	$output = '';

	if ( ! $revisions = wp_get_post_revisions( get_the_ID() ) ) {
		return $output;
	}

	$rows = '';
	foreach ( $revisions as $revision ) {
		if ( ! current_user_can( 'read_post', $revision->ID ) ) {
			continue;
		}
		$rows .= "\t<li>" . wp_post_revision_title_expanded( $revision ) . "</li>\n";
	}

	$output .= "<ul class='post-revisions hide-if-no-js'>\n";
	$output .= $rows;
	$output .= '</ul>';

	// At the end of your shortcode function, make sure to..
	return $output;
}
add_shortcode( 'wppedia_revisions', 'testrev' );