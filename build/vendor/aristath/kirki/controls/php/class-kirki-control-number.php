<?php

namespace WPPedia\Container;

/**
 * Customizer Control: number.
 *
 * @package     Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2020, David Vongries
 * @license     https://opensource.org/licenses/MIT
 * @since       1.0
 */
// Exit if accessed directly.
if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Create a simple number control
 */
class Kirki_Control_Number extends Kirki_Control_Base
{
    /**
     * The control type.
     *
     * @access public
     * @var string
     */
    public $type = 'kirki-number';
}
/**
 * Create a simple number control
 */
\class_alias('WPPedia\\Container\\Kirki_Control_Number', 'Kirki_Control_Number', \false);
