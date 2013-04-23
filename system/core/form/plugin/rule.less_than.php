<?php
/**
 * Genos CMS
 *
 * @author      Ivan Molina Pavana <montemolina@live.com>
 * @copyright   Copyright (c) 2013, Ivan Molina Pavana <montemolina@live.com>
 * @license     GNU General Public License, version 3
 */

// ------------------------------------------------------------------------

/**
 * Valor numérico menor que...
 *
 * Compara que el valor del campo actual sea menor al valor especificado.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_less_than()
 *
 * @param string $str Valor del campo.
 * @param int $max Valor máximo.
 *
 * @return string bool
 */
function form_rule_less_than($str, $max)
{
    if ( ! is_numeric($str))
    {
        return false;
    }

    return ($str < $max);
}