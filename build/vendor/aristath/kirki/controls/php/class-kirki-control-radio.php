<?php

namespace WPPedia\Container;

/**
 * Customizer Control: kirki-radio.
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
 * Radio control
 */
class Kirki_Control_Radio extends Kirki_Control_Base
{
    /**
     * The control type.
     *
     * @access public
     * @var string
     */
    public $type = 'kirki-radio';
}
/**
 * Radio control
 */
\class_alias('WPPedia\\Container\\Kirki_Control_Radio', 'Kirki_Control_Radio', \false);
