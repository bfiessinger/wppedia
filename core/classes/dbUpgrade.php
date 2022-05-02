<?php

/**
 * Database Upgrade
 *
 * @since 1.3.0
 */

namespace WPPedia;

use WPPedia\options;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class dbUpgrade {

	private $last_version;
	private $cur_version;

	public function _init() {
		$this->cur_version = wppedia_get_version();
		$this->last_version = get_option('wppedia_installed_version');

		add_action('init', [$this, 'maybe_run_upgrade']);
	}

	/**
	 * Check if the database needs to be upgraded
	 *
	 * @since 1.2.0
	 */
	private function is_upgrade_required() {
		if ($this->cur_version !== $this->last_version) {
			return true;
		}
		return false;
	}

	/**
	 * Run upgrade if required
	 *
	 * @since 1.2.0
	 */
	public function maybe_run_upgrade() {
		if (!$this->is_upgrade_required()) {
			return;
		}

		$this->handle_upgrade_options();
		$this->set_version_option();
	}

	/**
	 * Save the current version to the database
	 *
	 * @since 1.2.0
	 */
	private function set_version_option() {
		if (!wppedia_option_exists('wppedia_installed_version')) {
			add_option('wppedia_installed_version', wppedia_get_version(), '', false);
		} else {
			update_option('wppedia_installed_version', wppedia_get_version(), false);
		}
	}

	/**
	 * Handle version upgrades
	 *
	 * @since 1.3.0
	 */
	private function handle_upgrade_options() {
		// Set default options
		$defaults = options::get_option_defaults();
		foreach ($defaults as $option_group => $options) {
			if (is_array($options)) {
				foreach ($options as $key => $value) {
					$this->set_default_option($option_group, $key, $value);
				}
			} else {
				$this->set_default_option(null, $option_group, $options);
			}
		}

		$this->update();
	}

	/**
	 * Upgrade the database for each outdated version
	 *
	 * @since 1.3.0
	 */
	private function update() {
		$current_db_version = $this->cur_version;

		if (version_compare($current_db_version, '1.1.0', '<')) {
			include_once('updates/wppedia-update-1.1.0.php');
		}

		if (version_compare($current_db_version, '1.3.0', '<')) {
			include_once('updates/wppedia-update-1.3.0.php');
		}
	}

	/**
	 * Set default option values if they don't exist
	 *
	 * @param string $option_group
	 * @param string $key
	 * @param mixed $value
	 *
	 * @since 1.3.0
	 */
	private function set_default_option($option_group, $key, $value) {
		if (!wppedia_option_exists('wppedia_settings')) {
			add_option(
				'wppedia_settings',
				array_filter(options::get_option_defaults(), function($value) {
					return is_array($value);
				}),
				'',
				false
			);
			return;
		}

		if (!options::option_exists($option_group, $key)) {
			options::update_option($option_group, $key, $value);
		}
	}
}
