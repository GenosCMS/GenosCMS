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
 * Valor numérico mayor que...
 *
 * Compara que el valor del campo actual sea mayor al valor especificado.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_greater_than()
 *
 * @param string $str Valor del campo.
 * @param int $min Valor máximo.
 *
 * @return string bool
 */
function form_rule_greater_than($str, $min)
{
    if ( ! is_numeric($str))
    {
        return false;
    }

    return ($str > $min);
}