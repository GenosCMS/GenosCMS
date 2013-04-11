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
 * Genera campos de tipo 'select'.
 * 
 * {form_select name='country' options=$countries selected='MX'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_form_select()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_form_select($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
    // El nombre es requerido
	if ( ! isset($args['name']))
	{
		return '';
	}
    $name = $args['name'];
    
    // Opciones del select
    $options = (isset($args['options']) ? $args['options'] : 'array()');
    
    // Seleccionados
    $selected = (isset($args['selected']) ? $args['selected'] : 'array()');
    
    // Eliminamos atributos
    unset($args['name'], $args['options'], $args['selected']);
    
    $attrs = '';
    if (count($args))
    {
        foreach ($args as $tag => $val)
        {   
            $attrs .= $tag . '="' . $compiler->_removeQuote($val) . '" ';
        }
    }

	return 'echo Core::getLib(\'html.form\')->formSelect(' . $name . ', ' . $options . ', ' . $selected . ', \'' . $attrs . '\');';
}