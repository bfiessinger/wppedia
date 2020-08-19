<?php

/**
 * REST Endpoint
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\helper;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class rest extends \WP_REST_Controller {

	// Public namespace
	public $rest_namespace = null;

	// Public Endpoints
	public $rest_endpoint_search = null;

	public function __construct() {

		$this->rest_namespace = 'wppedia/v/' . helper::getInstance()->get_version();

		$this->rest_endpoint_search = 'search';

	}

	public function start() {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	function register_rest_routes() {

		register_rest_route( $this->rest_namespace, '/' . $this->rest_endpoint_search, [
			'methods' => 'GET',
			'callback' => function() { 
				return helper::getInstance()->get_wiki_entry_searchables(); 
			},
			'args' => [
				'permission_callback' => '__return_true'
			]
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

}
