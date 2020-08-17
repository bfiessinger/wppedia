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

	public $rest_namespace = null;

	public function __construct() {

		$this->rest_namespace = 'wppedia/v/' . helper::getInstance()->get_version();

	}

	public function init_actions() {

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

	}

	function register_rest_routes() {

		register_rest_route( $this->rest_namespace, '/posts', [
			'methods' => 'GET',
			'callback' => function() { 
				return helper::getInstance()->get_wiki_entry_titles(); 
			}
		] );

	}

}
