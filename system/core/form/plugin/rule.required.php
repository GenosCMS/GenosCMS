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
 * Validar campo requerido.
 * 
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * rule_required()
 * 
 * @param string $value Valor del campo.
 * @param string $param Parámetro para el plugin.
 * 
 * @return string bool
 */
function form_rule_required($value)
{
    return (trim($value) == '') ? false : true;
}