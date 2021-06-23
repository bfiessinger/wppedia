<?php

namespace WPPedia\Container;

/**
 * Override field methods
 *
 * @package     Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2020, David Vongries
 * @license     https://opensource.org/licenses/MIT
 * @since       2.2.7
 */
/**
 * Field overrides.
 */
class Kirki_Field_Upload extends Kirki_Field
{
    /**
     * Sets the control type.
     *
     * @access protected
     */
    protected function set_type()
    {
        $this->type = 'upload';
    }
    /**
     * Sets the $sanitize_callback
     *
     * @access protected
     */
    protected function set_sanitize_callback()
    {
        // If a custom sanitize_callback has been defined,
        // then we don't need to proceed any further.
        if (!empty($this->sanitize_callback)) {
            return;
        }
        $this->sanitize_callback = 'esc_url_raw';
    }
}
/**
 * Override field methods
 *
 * @package     Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2020, David Vongries
 * @license     https://opensource.org/licenses/MIT
 * @since       2.2.7
 */
/**
 * Field overrides.
 */
\class_alias('WPPedia\\Container\\Kirki_Field_Upload', 'Kirki_Field_Upload', \false);
