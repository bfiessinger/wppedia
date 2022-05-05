<?php

/**
 * WPPedia admin fields
 *
 * @since 1.3.0
 */

namespace WPPedia\Traits;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) || die();

trait Has_Admin_Fields {

	/**
	 * Evaluate the function to use foreach field type
	 *
	 * @since 1.3.0
	 */
	public function field($field) {
		$output = null;

		switch ($field['type']) {
			case 'number':
			case 'date':
				$output = $this->input_minmax($field);
				break;
			case 'textarea':
				$output = $this->textarea($field);
				break;
			case 'select':
				$output = $this->select($field);
				break;
			case 'checkbox':
			case 'switch':
				$output = $this->checkbox($field);
				break;
			case 'checkbox-group':
				$output = $this->checkbox_group($field);
				break;
			case 'title':
				$output = $this->title($field);
				break;
			default:
				$output = $this->input($field);
		}

		if ($this->is_restricted_pro($field)) {
			echo preg_replace('/name=("|\').*?("|\')/i', 'disabled', $output);
			echo '<input type="hidden" name="' . $this->field_name($field) . '" value="' . esc_attr($field['args']['default']) . '" />';
		} else {
			echo $output;
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
		return sprintf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
			$this->field_class_string($field),
			$this->field_id($field),
			$this->field_name($field),
			isset( $field['args']['pattern'] ) ? "pattern='{$field['args']['pattern']}'" : '',
			$field['type'],
			$this->value($field)
		);
	}

	/**
	 * Create a numeric input field
	 *
	 * @since 1.3.0
	 */
	protected function input_minmax($field) {
		return sprintf(
			'<input class="regular-text %s" id="%s" %s %s name="%s" %s type="%s" value="%s">',
			$this->field_class_string($field),
			$this->field_id($field),
			isset( $field['args']['max'] ) ? "max='{$field['args']['max']}'" : '',
			isset( $field['args']['min'] ) ? "min='{$field['args']['min']}'" : '',
			$this->field_name($field),
			isset( $field['args']['step'] ) ? "step='{$field['args']['step']}'" : '',
			$field['type'],
			$this->value($field)
		);
	}

	/**
	 * Create a textarea field
	 *
	 * @since 1.3.0
	 */
	protected function textarea($field) {
		return sprintf(
			'<textarea class="regular-text %s" id="%s" name="%s" rows="%d">%s</textarea>',
			$this->field_class_string($field, [], true),
			$this->field_id($field),
			$this->field_name($field),
			isset( $field['args']['rows'] ) ? $field['args']['rows'] : 4,
			$this->value($field)
		);
	}

	/**
	 * Create a select input
	 *
	 * @since 1.3.0
	 */
	protected function select($field) {
		if (isset($field['args']['remote_options']) && is_array($field['args']['remote_options'])) {
			$remote_options = $field['args']['remote_options'];
			return sprintf(
				'<select id="%s" name="%s" %s data-remote-options="%s">%s</select>',
				$this->field_id($field),
				$this->field_name($field),
				$this->field_class_string($field, ['wppedia-select2'], true),
				esc_attr(json_encode($remote_options)),
				'<option value="' . $this->value($field) . '">' . $remote_options['selected_label'] . '</option>'
			);
		}

		return sprintf(
			'<select id="%s" name="%s" %s>%s</select>',
			$this->field_id($field),
			$this->field_name($field),
			$this->field_class_string($field, ['wppedia-select2'], true),
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

		$html = sprintf(
			'<input %s %s id="%s" name="%s" type="checkbox" value="1">',
			$this->field_class_string($field, $additionalClasses, true),
			checked($this->value($field), true, false),
			$this->field_id($field),
			$this->field_name($field)
		);

		if ($is_switch) {
			$html .= sprintf(
				'<label for="%s" class="wppedia-switch-label" data-on="%s" data-off="%s"></label>',
				$this->field_id($field),
				_x('Yes', 'options', 'wppedia'),
				_x('No', 'options', 'wppedia')
			);
		}

		return $html;
	}

	/**
	 * Create a group of checkbox fields
	 *
	 * @since 1.3.0
	 */
	protected function checkbox_group($field) {
		$html = '';
		foreach ($field['args']['options'] as $option => $label ) {
			$name = $this->field_name($field) . '[' . $option . ']';

			$html .= '<label class="wppedia-checkbox-group-item">';
			$html .= $this->checkbox(
				array_merge($field, [
					'pid' => $field['id'],
					'id' => $option,
					'name' => $name
				])
			);
			$html .= '<span>' . $label . '</span>';
			$html .= '</label>';
		}
		return $html;
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

		return sprintf(
			'<%s>%s</%s><hr>',
			$heading_lvl,
			$field['args']['label'],
			$heading_lvl
		);
	}

	private function field_group($field) {
		$field_section = $field['settings_section'];

		switch ($field_section) {
			case 'wppedia_settings_permalink':
				$field_section = 'permalinks';
				break;
			default:
				break;
		}

		return $field_section;
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
		if (get_option('wppedia_settings', false) && isset(maybe_unserialize(get_option('wppedia_settings'))[$this->field_group($field)][$field['id']])) {
			$value = maybe_unserialize(get_option('wppedia_settings'))[$this->field_group($field)][$field['id']];
		} else if (get_option('wppedia_settings', false) && isset($field['pid']) && isset(maybe_unserialize(get_option('wppedia_settings'))[$this->field_group($field)][$field['pid']][$field['id']])) {
			$value = maybe_unserialize(get_option('wppedia_settings'))[$this->field_group($field)][$field['pid']][$field['id']];
		} else if ( isset( $field['args']['default'] ) ) {
			$value = $field['args']['default'];
		} else {
			return '';
		}

		return esc_attr(str_replace( '\u0027', "'", $value ));
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
		if (isset($field['args']['pro']) && false !== $field['args']['pro']) {
			return true;
		}
		return false;
	}
}
