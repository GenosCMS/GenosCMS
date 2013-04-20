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
 * Nos permite validar campos en base a expresiones regulares predefinidas.
 * 
 * Las expresiones disponibles se encuentran en el archivo: /include/setting/regex.php
 * 
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * rule_required()
 * 
 * @param string $str Valor del campo.
 * @param string $type Tipo de expresión a usar.
 * 
 * @return string bool
 */
function form_rule_is_valid($str, $type)
{
    static $_regex;

    if ( ! is_array($_regex))
    {
        require SETTING_PATH . 'regex.php';
    }

    if ( ! isset($_regex[$type]))
    {
        return false;
    }
    
    return (bool) preg_match($_regex[$type], $str);
}