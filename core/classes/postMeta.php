<?php

/**
 * WPPedia post meta settings
 * 
 * @since 1.2.0
 */

namespace WPPedia;

use WPPedia\traits\adminFields;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class postMeta {

	use adminFields;

	private $post_meta_config;

	public function __construct() {
		$this->post_meta_config = [
			'title' 			=> 'WPPedia post settings',
			'description' => '',
			'meta_prefix' => "wppedia_post_",
			'class_name' 	=> 'WPPedia_Post_Settings',
			'post-type' 	=> [\wppedia_get_post_type()],
			'context' 		=> 'normal',
			'priority' 		=> 'low',
			'fields' 			=> [
				[
					'id'			=> 'wppedia_post_alt_tags',
					'type' 		=> 'textarea',
					'label' 	=> 'Alternative terms'
				]
			]
		];

		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
	}

	/**
	 * Create WPPedia metaboxes
	 * 
	 * @since 1.1.0
	 */
	public function add_meta_boxes() {
		foreach ( $this->post_meta_config['post-type'] as $screen ) {
			add_meta_box(
				sanitize_title( $this->post_meta_config['title'] ),
				$this->post_meta_config['title'],
				[ $this, 'add_meta_box_callback' ],
				$screen,
				$this->post_meta_config['context'],
				$this->post_meta_config['priority']
			);
		}
	}

	/**
	 * Save custom field values
	 * 
	 * @since 1.1.0
	 */
	public function save_post( $post_id ) {
		foreach ( $this->post_meta_config['fields'] as $field ) {
			switch ( $field['type'] ) {
				default:
					if ( isset( $_POST[ $field['id'] ] ) ) {
						$sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
						update_post_meta( $post_id, $field['id'], $sanitized );
					}
			}
		}
	}

	/**
	 * Print metabox contents
	 * 
	 * @since 1.1.0
	 */
	public function add_meta_box_callback() {
		echo '<div class="wppedia-metabox-description">' . $this->post_meta_config['description'] . '</div>';
		$this->fields_table();
	}

	/**
	 * Wrap individual fields in WordPress option tables
	 * 
	 * @since 1.1.0
	 */
	private function fields_table() {
		?><table class="form-table" role="presentation">
			<tbody><?php
				foreach ( $this->post_meta_config['fields'] as $field ) {
					?><tr>
						<th scope="row"><?php $this->label( $field ); ?></th>
						<td><?php $this->field( $field ); ?></td>
					</tr><?php
				}
			?></tbody>
		</table><?php
	}

	/**
	 * Create field labels
	 * 
	 * @since 1.1.0
	 */
	private function label( $field ) {
		switch ( $field['type'] ) {
			default:
				printf(
					'<label class="" for="%s">%s</label>',
					$field['id'], $field['label']
				);
		}
	}

	/**
	 * Retrieve custom field values
	 * 
	 * @since 1.1.0
	 */
	public function value( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

}
