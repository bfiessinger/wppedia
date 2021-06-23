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
class Kirki_Field_Color_Alpha extends Kirki_Field_Color
{
    /**
     * Sets the $choices
     *
     * @access protected
     */
    protected function set_choices()
    {
        if (!\is_array($this->choices)) {
            $this->choices = array();
        }
        $this->choices['alpha'] = \true;
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
\class_alias('WPPedia\\Container\\Kirki_Field_Color_Alpha', 'Kirki_Field_Color_Alpha', \false);
