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
 * Genera campos de tipo 'radio'.
 * 
 * {form_radio name='gender' value='male' checked='true'}
 * {form_radio name='gender' value='female'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_form_radio()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_form_radio($tagArgs, &$compiler)
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

	return 'echo Core::getLib(\'html.form\')->formOption(' . $name . ', ' . $value . ', ' . $checked . ', \'radio\', \'' . $attrs . '\');';
}