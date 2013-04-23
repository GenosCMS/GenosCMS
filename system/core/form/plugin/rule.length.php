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
 * Longitud exacta.
 *
 * Valida que el valor tengo una longitud definida.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_length()
 *
 * @param string $str Valor del campo.
 * @param int $val Valor exacto de caracteres.
 *
 * @return string bool
 */
function form_rule_length($str, $val)
{
    // Si no es un número
    if (preg_match("/[^0-9]/", $val))
    {
        return false;
    }

    if (function_exists('mb_strlen'))
    {
        return (mb_strlen($str) != $val) ? false : true;
    }

    return (strlen($str) != $val) ? false : true;
}