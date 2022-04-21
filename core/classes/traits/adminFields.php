<?php

/**
 * WPPedia admin fields
 * 
 * @since 1.3.0
 */

namespace WPPedia\traits;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) || die();

trait adminFields {

	/**
	 * Evaluate the function to use foreach field type
	 * 
	 * @since 1.3.0
	 */
	public function field($field) {
		switch ($field['type']) {
			case 'number':
			case 'date':
				$this->input_minmax($field);
				break;
			case 'textarea':
				$this->textarea($field);
				break;
			case 'select':
				$this->select($field);
				break;
			case 'checkbox':
			case 'switch':
				$this->checkbox($field);
				break;
			case 'checkbox-group':
				$this->checkbox_group($field);
				break;
			case 'title':
				$this->title($field);
				break;
			default:
				$this->input($field);
		}

		// Show field description
		if (isset($field['args']['desc']) && '' !== $field['args']['desc']) {
			$this->display_field_description($field['args']['desc']);
		}
	}

	/**
	 * Create a regular input field
	 * 
	 * @since 1.3.0
	 */
	protected function input($field) {
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s" %s>',
			$this->field_class_string($field),
			$this->field_id($field),
			$this->field_name($field),
			isset( $field['args']['pattern'] ) ? "pattern='{$field['args']['pattern']}'" : '',
			$field['type'],
			$this->value($field),
			$this->restrict_pro($field)
		);
	}

	/**
	 * Create a numeric input field
	 * 
	 * @since 1.3.0
	 */
	protected function input_minmax($field) {
		printf(
			'<input class="regular-text %s" id="%s" %s %s name="%s" %s type="%s" value="%s" %s>',
			$this->field_class_string($field),
			$this->field_id($field),
			isset( $field['args']['max'] ) ? "max='{$field['args']['max']}'" : '',
			isset( $field['args']['min'] ) ? "min='{$field['args']['min']}'" : '',
			$this->field_name($field),
			isset( $field['args']['step'] ) ? "step='{$field['args']['step']}'" : '',
			$field['type'],
			$this->value($field),
			$this->restrict_pro($field)
		);
	}

	/**
	 * Create a textarea field
	 * 
	 * @since 1.3.0
	 */
	protected function textarea($field) {
		printf(
			'<textarea class="regular-text %s" id="%s" name="%s" rows="%d" %s>%s</textarea>',
			$this->field_class_string($field, [], true),
			$this->field_id($field),
			$this->field_name($field),
			isset( $field['args']['rows'] ) ? $field['args']['rows'] : 4,
			$this->restrict_pro($field),
			$this->value($field)
		);
	}

	/**
	 * Create a select input 
	 * 
	 * @since 1.3.0
	 */
	protected function select($field) {
		printf(
			'<select id="%s" name="%s" %s %s>%s</select>',
			$this->field_id($field),
			$this->field_name($field),
			$this->field_class_string($field, [], true),
			$this->restrict_pro($field),
			$this->select_options($field)
		);
	}

	/**
	 * Evaluate the selected option
	 * 
	 * @since 1.3.0
	 */
	private function select_selected($field, $current) {
		$value = $this->value($field);
		if (strval($value) === strval($current)) {
			return ' selected';
		}
		return '';
	}

	/**
	 * Output options for the select method
	 * 
	 * @since 1.3.0
	 */
	private function select_options($field) {
		$output = [];
		foreach ($field['args']['options'] as $option => $label) {
			$output[] = sprintf(
				'<option%s value="%s">%s</option>',
				$this->select_selected($field, $option),
				$option, 
				$label
			);
		}
		return implode( '<br>', $output );
	}

	/**
	 * Create a checkbox field
	 * 
	 * @since 1.3.0
	 */
	protected function checkbox($field) {
		$is_switch = false;
		$additionalClasses = [];
		if ('switch' === $field['type']) {
			$is_switch = true;
			$additionalClasses[] = 'wppedia-switch-button';
		}

		printf(
			'<input %s %s id="%s" name="%s" type="checkbox" value="1" %s>',
			$this->field_class_string($field, $additionalClasses, true),
			checked($this->value($field), true, false),
			$this->field_id($field),
			$this->field_name($field),
			$this->restrict_pro($field)
		);

		if ($is_switch) {
			printf(
				'<label for="%s" class="wppedia-switch-label" data-on="%s" data-off="%s"></label>',
				$this->field_id($field),
				_x('Yes', 'options', 'wppedia'),
				_x('No', 'options', 'wppedia')
			);
		}
	}

	/**
	 * Create a group of checkbox fields
	 * 
	 * @since 1.3.0
	 */
	protected function checkbox_group($field) {
		foreach ($field['args']['options'] as $option => $label ) {
			$id = $field['id'] . '[' . $option . ']';
			$name = $this->field_name($field) . '[' . $option . ']';
			echo '<div class="wppedia-checkbox-group-item">';
			$this->checkbox(
				array_merge($field, [
					'id' => $id, 
					'name' => $name
				])
			);
			echo '<label for="' . $id . '">' . $label . '</label>';
			echo '</div>';
		}
	}

	/**
	 * Create an arbiatry title field
	 * 
	 * @since 1.3.0
	 */
	protected function title($field) {
		$allowed_h_tags = [
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6'
		];

		$heading_lvl = (!isset($field['args']['heading_level']) || !in_array($field['args']['heading_level'], $allowed_h_tags)) ? 'h2' : $field['args']['heading_level'];

		printf(
			'<%s>%s</%s>',
			$heading_lvl,
			$field['args']['label'],
			$heading_lvl
		);
		echo '<hr>';
	}

	private function field_id($field) {
		return isset($field['settings_section']) ? $field['settings_section'] . '.' . $field['id'] : $field['id'];
	}

	private function field_name($field) {
		return isset($field['name']) ? $field['name'] : $field['id'];
	}

	/**
	 * Print field css classes
	 * classes might be defined as strings or string arrays
	 * 
	 * This method removes all non string values and returns a sanitized
	 * class string
	 * 
	 * @param $field
	 * @param $additionalClasses
	 * @param $withAttribute
	 * 
	 * @return string
	 * 
	 * @since 1.3.0
	 */
	private function field_class_string($field, $additionalClasses = [], $withAttribute = false) {
		if (!isset($field['args']['class']) && (!$additionalClasses || empty($additionalClasses))) {
			return;
		}

		$class = (isset($field['args']['class'])) ? $field['args']['class'] : [];

		$class_string = '';
		if (is_array($class) && !empty($class)) {
			$class_array_sanitized = array_filter($class, 'is_string');
			$class_string = implode(' ', $class_array_sanitized);
		} elseif (is_string($class)) {
			$class_string = trim($class);
		}		

		if (is_array($additionalClasses) && !empty($additionalClasses)) {
			$additional_class_array_sanitized = array_filter($additionalClasses, 'is_string');
			$class_string .= ' ' . implode(' ', $additional_class_array_sanitized);
		} elseif (is_string($additionalClasses)) {
			$class_string .= trim($additionalClasses);
		}

		$class_string = trim($class_string);

		if ($withAttribute) {
			$class_string = 'class="' . $class_string . '"';
		}

		return $class_string;
	}

	/**
	 * Evaluate the current value of a field
	 * 
	 * @since 1.3.0
	 */
	private function value($field) {
		if (get_option('wppedia_settings', false) && isset(maybe_unserialize(get_option('wppedia_settings'))[$field['settings_section']][$field['id']])) {
			$value = maybe_unserialize(get_option('wppedia_settings'))[$field['settings_section']][$field['id']];
		} else if ( isset( $field['args']['default'] ) ) {
			$value = $field['args']['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

	/**
	 * Display an options description
	 * 
	 * @since 1.2.0
	 */
	private function display_field_description($desc) {
		if (is_callable($desc)) {
			$desc = call_user_func($desc);
		}

		if (!is_string($desc)) {
			return;
		}

		echo '<div class="wppedia-option-description">';
		echo $desc;
		echo '</div>';
	}

	private function is_restricted_pro($field) {
		if (isset($field['args']['class']) && false !== strpos($field['args']['class'], self::$pro_feature_className)) {
			return true;
		}
		return false;
	}

	/**
	 * Return disabled attribute for pro features
	 * 
	 * @param bool $disable
	 * 
	 * @since 1.3.0
	 */
	private function restrict_pro($field) {
		if ($this->is_restricted_pro($field)) {
			return ' disabled="disabled"';
		}
		return '';
	}

}
