<?php

/**
 * WPPedia module crosslinks
 * 
 * @since 1.2.0
 */

namespace WPPedia\traits;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) || die();

trait adminFields {

	public function field( $field ) {
		switch ( $field['type'] ) {
			case 'number':
			case 'date':
				$this->input_minmax( $field );
				break;
			case 'textarea':
				$this->textarea( $field );
				break;
			case 'select':
				$this->select( $field );
				break;
			case 'checkbox':
				$this->checkbox( $field );
				break;
			case 'checkbox-group':
				$this->checkbox_group( $field );
				break;
			case 'title':
				$this->title( $field );
				break;
			default:
				$this->input( $field );
		}

		// Show field description
		if (isset($field['desc']) && '' !== $field['desc']) {
			$this->display_field_description($field['desc']);
		}
	}

	public function input( $field ) {
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s" %s>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field ),
			$this->restrict_pro($field)
		);
	}

	public function input_minmax( $field ) {
		printf(
			'<input class="regular-text %s" id="%s" %s %s name="%s" %s type="%s" value="%s" %s>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'],
			isset( $field['max'] ) ? "max='{$field['max']}'" : '',
			isset( $field['min'] ) ? "min='{$field['min']}'" : '',
			$field['id'],
			isset( $field['step'] ) ? "step='{$field['step']}'" : '',
			$field['type'],
			$this->value( $field ),
			$this->restrict_pro($field)
		);
	}

	public function textarea( $field ) {
		printf(
			'<textarea class="regular-text %s" id="%s" name="%s" rows="%d" %s>%s</textarea>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['rows'] ) ? $field['rows'] : 4,
			$this->restrict_pro($field),
			$this->value( $field )
		);
	}

	public function select( $field ) {
		printf(
			'<select id="%s" name="%s" %s %s>%s</select>',
			$field['id'], $field['id'],
			($field['class'] && '' !== trim($field['class'])) ? ' class="' . $field['class'] . '"' : "",
			$this->restrict_pro($field),
			$this->select_options( $field )
		);
	}

	public function select_selected( $field, $current ) {
		$value = $this->value( $field );
		if ( strval($value) === strval($current) ) {
			return 'selected';
		}
		return '';
	}

	public function select_options( $field ) {
		$output = [];
		foreach ( $field['options'] as $option => $label ) {
			$output[] = sprintf(
				'<option %s value="%s"> %s</option>',
				$this->select_selected( $field, $option ),
				$option, $label
			);
		}
		return implode( '<br>', $output );
	}

	public function checkbox( $field ) {
		printf(
			'<input %s %s id="%s" name="%s" type="checkbox" value="1" %s>',
			($field['class'] && '' !== trim($field['class'])) ? ' class="' . $field['class'] . '"' : "",
			checked(get_option($field['id'], false), true, false),
			$field['id'], $field['id'],
			$this->restrict_pro($field)
		);
	}

	public function checkbox_group( $field ) {
		foreach ( $field['options'] as $option => $label ) {
			$id = $field['id'] . '[' . $option . ']';
			echo '<div class="wppedia-checkbox-group-item">';
			$this->checkbox( array_merge($field, ['id' => $id]) );
			echo '<label for="' . $id . '">' . $label . '</label>';
			echo '</div>';
		}
	}

	public function title( $field ) {
		$allowed_h_tags = [
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6'
		];

		$heading_lvl = (!isset($field['heading_level']) || !in_array($field['heading_level'], $allowed_h_tags)) ? 'h2' : $field['heading_level'];

		printf(
			'<%s>%s</%s>',
			$heading_lvl,
			$field['label'],
			$heading_lvl
		);
		echo '<hr>';
	}

	public function value( $field ) {
		if (get_option($field['id'], false)) {
			$value = get_option($field['id']);
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

	/**
	 * Display an options description
	 * 
	 * @since 1.1.0
	 */
	public function display_field_description($desc) {
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

	/**
	 * Return disabled attribute for pro features
	 * 
	 * @param bool $disable
	 * 
	 * @since 1.1.0
	 */
	public function restrict_pro($field) {
		if (isset($field['class']) && false !== strpos($field['class'], self::$pro_feature_className)) {
			return ' disabled="disabled"';
		}
		return '';
	}

}