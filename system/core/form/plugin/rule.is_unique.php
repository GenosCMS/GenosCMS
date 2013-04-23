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
 * Valor único.
 *
 * Comprueba la existencia de un valor en la base de datos.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_is_unique()
 *
 * @param string $str Valor del campo.
 * @param int $field Campo de la base de datos en formato: table.field_name
 *
 * @return string bool
 */
function form_rule_is_unique($str, $field)
{
    list($table, $field) = explode('.', $field);

    $exists = Core::getLib('database')->select('COUNT(*)')->from($table)->where(array($field => $str))->exec('field');

    return ($exists == 0) ? true : false;
}