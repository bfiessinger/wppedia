<?php

/**
 * Theme and Plugin compatibilities
 *
 * @since 1.2.4
 */

namespace WPPedia\Compatibilities;

use WPPedia\Compatibilities\Compatibility_Loader;

defined( 'ABSPATH' ) || die();

class Compatibility_Collection {

	private $themeCompatibilities = [];
	private $pluginCompatibilities = [];

	public function _init() {
		$this->setDefaultThemeCompatibilities();
		$this->setDefaultPluginCompatibilities();

		add_action('after_setup_theme', [ $this, 'runThemeCompatibilities' ]);
		add_action('plugins_loaded', [ $this, 'runPluginCompatibilies' ]);
	}

	/**
	 * Add Theme to compatibility Array
	 *
	 * @param string $slug
	 * @param string $name
	 *
	 * @since 1.2.0
	 */
	public function addThemeCompatibility(string $slug, string $name) {
		$this->themeCompatibilities[$slug] = $name;
	}

	/**
	 * Add Plugin to compatibility Array
	 *
	 * @param string $slug
	 * @param string|array $className
	 *
	 * @since 1.2.0
	 */
	public function addPluginCompatibility(string $slug, $className) {
		$this->pluginCompatibilities[$slug] = $className;
	}

	/**
	 * Set default Theme Compatibilities
	 *
	 * @since 1.2.0
	 */
	private function setDefaultThemeCompatibilities() {
		$themes = [
			'twenty-twenty-one' => 'Twenty Twenty-One',
			'twenty-twenty' => 'Twenty Twenty',
			'twenty-nineteen' => 'Twenty Nineteen',
		];

		foreach ($themes as $slug => $name) {
			$this->addThemeCompatibility($slug, $name);
		}
	}

	/**
	 * Set default Plugin Compatibilities
	 *
	 * @since 1.2.0
	 */
	private function setDefaultPluginCompatibilities()  {
		$plugins = [];

		foreach ($plugins as $slug => $className) {
			$this->addThemeCompatibility($slug, $className);
		}
	}

	function runThemeCompatibilities() {
		foreach ($this->themeCompatibilities as $slug => $themeName) {
			$loader = new Compatibility_Loader();
			$loader->setType('theme');
			$loader->setSlug($slug);
			$loader->setThemeName($themeName);

			$loader->load();
		}
	}

	function runPluginCompatibilies() {
		foreach ($this->pluginCompatibilities as $slug => $className) {
			$loader = new Compatibility_Loader();
			$loader->setType('plugin');
			$loader->setSlug($slug);
			$loader->setClassName($className);

			$loader->load();
		}
	}

}
