<?php

/**
 * REST API Endpoint
 * 
 * @since 1.2.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class restController extends \WP_REST_Controller {

	// Public namespace
	public $rest_namespace = null;

	// Public Endpoints
	public $rest_endpoint_search = null;

	public function __construct() {

		$this->rest_namespace = 'wppedia/v1';
		$this->rest_endpoint_search = 'search';

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

	}

	function register_rest_routes() {

		register_rest_route( $this->rest_namespace, '/' . $this->rest_endpoint_search, [
			'methods' => 'GET',
			'callback' => function() { 
				return $this->get_wiki_entry_searchables(); 
			},
			'permission_callback' => '__return_true'
		] );

	}

	/**
	 * Simple utility to obtain the Rest endpoint for WPPedia
	 * 
	 * @uses rest_url()
	 * 
	 * @param string {$endpoint}
	 * 
	 * @return string - the final endpoint URL
	 * 
	 * @since 1.0.0
	 */
	public function get_endpoint_url( string $endpoint = '' ) {

		$url = rest_url( $this->rest_namespace . '/' . $endpoint );
		return $url;

	}

	/**
	 * Get Wiki Entry titles
	 * 
	 * @since 1.0.0
	 * 
	 * @return array Array with post titles
	 */
	private function get_wiki_entry_searchables( array $query_args = [] ) {

		$the_query = wppedia_get_posts( $query_args );

		if ( ! $the_query->have_posts() )
			return null;

		$searchables = [];
		while ( $the_query->have_posts() ) {

			$the_query->the_post();

			$post_id = get_the_ID();

			$searchables[] = [
				'post_id'			=> $post_id,
				'post_title'	=> get_the_title(),
				'url'					=> get_permalink(),
				'tags'				=> wppedia_get_post_alternative_terms($post_id)
			];

		}

		return $searchables;

	}

}
