<?php

/**
 * Register shortcode to display the WPPedia version history
 *
 * @shortcode_name wppedia_version_history
 * @usage [wppedia_version_history post_id="123" limit="10"]
 *
 * @since 1.4.0
 */

defined( 'ABSPATH' ) || die();

function create_wppedia_version_history_shortcode( $atts = [] ) {

	$attributes = shortcode_atts(
		[
			'post_id' => get_the_ID(),
			'limit'   => 10,
		],
		$atts,
		'wppedia_version_history'
	);

	$post_id = (int) $attributes['post_id'];
	$limit = max( 1, (int) $attributes['limit'] );

	if ( $post_id <= 0 ) {
		return '';
	}

	$history = wppedia_get_post_version_history(
		$post_id,
		[
			'posts_per_page' => $limit,
		]
	);

	if ( empty( $history ) ) {
		return '<p>' . esc_html__( 'No version history available for this glossary entry yet.', 'wppedia' ) . '</p>';
	}

	$output = '<ul class="wppedia-version-history">';
	foreach ( $history as $version ) {
		$output .= '<li>';
		$output .= '<span class="wppedia-version-history-date">' . esc_html( $version['modified_human'] ) . '</span>';
		$output .= ' <span class="wppedia-version-history-author">' . sprintf(
			/* translators: %s: Author name */
			esc_html__( 'by %s', 'wppedia' ),
			esc_html( $version['author_name'] )
		) . '</span>';
		$output .= '</li>';
	}
	$output .= '</ul>';

	return $output;
}
add_shortcode( 'wppedia_version_history', 'create_wppedia_version_history_shortcode' );
