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
class Kirki_Field_Toggle extends Kirki_Field_Checkbox
{
    /**
     * Sets the control type.
     *
     * @access protected
     */
    protected function set_type()
    {
        $this->type = 'kirki-toggle';
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
\class_alias('WPPedia\\Container\\Kirki_Field_Toggle', 'Kirki_Field_Toggle', \false);
