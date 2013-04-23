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
 * Longitud mínima.
 *
 * Valida que el valor tengo una longitud mínima.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_min_length()
 *
 * @param string $str Valor del campo.
 * @param int $min Valor mínimo de caracteres.
 *
 * @return string bool
 */
function form_rule_min_length($str, $min)
{
    // Si no es un número
    if (preg_match("/[^0-9]/", $min))
    {
        return false;
    }

    if (function_exists('mb_strlen'))
    {
        return (mb_strlen($str) < $min) ? false : true;
    }

    return (strlen($str) < $min) ? false : true;
}