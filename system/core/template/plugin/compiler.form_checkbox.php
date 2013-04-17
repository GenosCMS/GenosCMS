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
 * Genera campos de tipo 'checkbox'.
 * 
 * {form_checkbox name='smartphone_iphone' value='yes'}
 * {form_checkbox name='smartphone_galaxy' value='yes'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_form_checkbox()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_form_checkbox($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
    // El nombre es requerido
	if ( ! isset($args['name']))
	{
		return '';
	}
    $name = $args['name'];
    
    // Value
    $value = (isset($args['value']) ? $args['value'] : 'null');
    
    // checked
    $checked = (isset($args['checked']) ? 'true' : 'false');
    
    // Eliminamos atributos
    unset($args['name'], $args['value'], $args['checked']);
    
    $attrs = '';
    if (count($args))
    {
        foreach ($args as $tag => $val)
        {   
            $attrs .= $tag . '="' . $compiler->_removeQuote($val) . '" ';
        }
    }

	return 'echo Core::getLib(\'form.helper\')->formOption(' . $name . ', ' . $value . ', ' . $checked . ', \'checkbox\', \'' . $attrs . '\');';
}