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
 * Longitud máxima.
 *
 * Valida que el valor tengo una longitud máxima.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_max_length()
 *
 * @param string $str Valor del campo.
 * @param int $max Valor máximo de caracteres.
 *
 * @return string bool
 */
function form_rule_max_length($str, $max)
{
    // Si no es un número
    if (preg_match("/[^0-9]/", $max))
    {
        return false;
    }

    if (function_exists('mb_strlen'))
    {
        return (mb_strlen($str) > $max) ? false : true;
    }

    return (strlen($str) > $max) ? false : true;
}