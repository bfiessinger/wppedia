<?php

/**
 * WP Wiki Template
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

class template {

  /**
   * Static variable for instanciation
   */
  protected static $instance = null;

  /**
   * Get current Instance
   */
  public static function getInstance() {

    if ( null === self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;

  }

  protected function __clone() {}

	protected function __construct() {}
	
  /**
   * Get a specific View
   * 
   * @since 1.0.0
   */
  public function get_view(string $view, array $args = [], bool $display = true) {

    $view_file = wpPediaPluginDir . '/views/view-' . $view . '.php';

    if ( file_exists( $view_file ) ) {

			if ( $display )
				require_once $view_file;
			else
				return $view_file;

		}

		return false;

  }

  /**
   * Get a partial view
   * 
   * @since 1.0.0
   */
  public function get_partial(string $partial, array $args = [], bool $display = true) {

    $partial_file = wpPediaPluginDir . '/partials/partial-' . $partial . '.php';

    if ( file_exists( $partial_file ) ) {

			if ( $display )
				require_once $partial_file;
			else
				return $partial_file;

		}
			
		return false;

	}
	
	/**
	 * Get the WPPedia Searchform
	 * 
	 * @return void
	 * 
	 * @since 1.0.0
	 */
	public function get_searchform() {

		// Don't modify the template if specified in the current Theme
		if ( locate_template(['searchform-wppedia.php']) ) {

			locate_template(['searchform-wppedia.php'], true);
			return;

		}

		// print searchform
		$this->get_partial( 'searchform' );		

	}
	
}
