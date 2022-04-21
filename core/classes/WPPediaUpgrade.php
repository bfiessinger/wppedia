<?php

/**
 * Database Upgrade
 * 
 * @since 1.2.0
 */

namespace WPPedia;

use WPPedia\options;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class WPPediaUpgrade {

	private $last_version;
	private $cur_version;

	public function __construct() {
		$this->cur_version = wppedia_get_version();
		$this->last_version = get_option('wppedia_installed_version');

		add_action('init', [$this, 'maybe_run_upgrade']);
	}

	private function is_upgrade_required() {
		if ($this->cur_version !== $this->last_version) {
			return true;
		}
		return false;
	}

	public function maybe_run_upgrade() {
		if (!$this->is_upgrade_required()) {
			return;
		}

		$this->handle_upgrade_options();
		$this->set_version_option();
	}

	private function set_version_option() {
		if (!get_option('wppedia_installed_version')) {
			add_option('wppedia_installed_version', wppedia_get_version(), '', false);
		} else {
			update_option('wppedia_installed_version', wppedia_get_version(), false);
		}
	}

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

		// TODO for version 1.3.0:
		//
		// Handle deprecated options
		// $deprecated = options::get_deprecated_options();
		// foreach ($deprecated as $old => $new) {
		// 	if (false !== $new) {
		// 		$this->replace_option_key($old, $new);
		// 	} else {
		// 		$options = get_option('wppedia_options');
		// 		delete_option($old);
		// 	}
		// }
	}

	private function set_default_option($option_group, $key, $value) {
		if (!get_option('wppedia_settings')) {
			add_option(
				'wppedia_settings', 
				array_filter(options::get_option_defaults(), function($value) {
					return is_array($value);
				}),
				'', 
				false
			);
		}

		// Update single option
		$options = get_option('wppedia_settings');
		if ($option_group && !isset($options[$option_group][$key])) {
			$options[$option_group][$key] = $value;
			update_option('wppedia_settings', $options);
		} else if (!$option_group && !get_option($key)) {
			update_option($key, $value);
		}
		
	}

	private function replace_option_key($oldKey, $newKey) {
		if (get_option($oldKey) && !get_option($newKey)) {
			global $wpdb;
			$is_autoload = $wpdb->get_results("SELECT `autoload` FROM $wpdb->options WHERE `option_name` = '$oldKey' LIMIT 1");
			$autoload = ('yes' === $is_autoload) ? true : false;
			add_option($newKey, get_option($oldKey), '', $autoload);
			delete_option($oldKey);
		}
	}

}
