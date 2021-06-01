<?php

/**
 * Theme and Plugin compatibilities
 * 
 * @since 1.2.0
 */

namespace WPPedia\compatibilities;

defined( 'ABSPATH' ) || die();

class compatibilityLoader {

	// Private variable to check if loading a 
	// compatibility for either a theme or a plugin
	private string $type;

	// Private vars to check if the plugin or theme exists
	private string $slug;
	private $className;
	private string $themeName;

	// Private variable to set the path for loading the
	// compatibility file
	private string $path = WPPediaPluginDir . 'core/inc/compatibilities/';

	/**
	 * Set the compatiblity loader type
	 * either plugin or theme
	 * 
	 * @param string $type
	 * 
	 * @since 1.2.0
	 */
	public function setType(string $type) {
		$this->type = $type;
		return $this->type;
	}

	/**
	 * Set compatibility file slug
	 * 
	 * @param string $slug
	 * 
	 * @since 1.2.0
	 */
	public function setSlug(string $slug) {
		$this->slug = $slug;
		return $this->slug;
	}

	/**
	 * Set classname or classnames to check for
	 * existance before loading the compatibility
	 * file
	 * 
	 * @param string|array $slug
	 * 
	 * @since 1.2.0
	 */
	public function setClassName($className) {
		$this->className = $className;
		return $this->className;
	}

	/**
	 * Alternatively to method `setClassName` set the
	 * name of the compatible theme (many theme don't use a main class)
	 * 
	 * @param string $themeName
	 * 
	 * @since 1.2.0
	 */
	public function setThemeName(string $themeName) {
		$this->themeName = $themeName;
		return $this->themeName;
	}

	/**
	 * Set path to the compatibility file
	 * 
	 * @param string $path
	 * 
	 * @since 1.2.0
	 */
	public function setPath(string $path) {
		$this->path = $path;
		return $this->path;
	}

	private function maybeLoadCompatFile() {
		// Bail early if slug is not set
		if (!$this->slug) {
			return;
		}

		$canLoad = false;
		if ('theme' === $this->type && wp_get_theme()->get('Name') === $this->themeName) {
			$canLoad = true;
		} else if ('plugin' === $this->type && $this->className) {
			if (is_array($this->className)) {
				foreach ($this->className as $className) {
					if (class_exists($className)) {
						$canLoad = true;
					}
				} 
			} else {
				if (class_exists($className)) {
					$canLoad = true;
				}
			}
		}

		if (!$canLoad) {
			return;
		}

		

		$compatFile = trailingslashit($this->path) . 'class.' . $this->type . '-compatibility-' . $this->slug . '.php';
		if (file_exists($compatFile)) {
			require_once $compatFile;
		}
	}

	/**
	 * Load the compatibility file
	 * 
	 * @since 1.2.0
	 */
	public function load() {
		$this->maybeLoadCompatFile();
	}

}
