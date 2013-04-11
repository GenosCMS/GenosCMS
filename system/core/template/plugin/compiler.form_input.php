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
 * Genera campos 'input'.
 * 
 * {form_input name='email' value='example@mail.com'}
 * {form_input name='pass' type='password'}
 * {form_input name='image' type='file'}
 * {form_input name='message' type='textarea' value=$message}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_form_input()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_form_input($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
    // El nombre es requerido
	if ( ! isset($args['name']))
	{
		return '';
	}
    
    $name = $args['name'];
    
    $value = (isset($args['value']) ? $args['value'] : 'null');
    $type = (isset($args['type']) ? $args['type'] : "'text'");
    
    // Eliminamos atributos
    unset($args['name'], $args['value'], $args['type']);
    
    // Parámetros extra que puede tener el campo
    $attrs = '';
    if (count($args))
    {
        foreach ($args as $tag => $val)
        {   
            $attrs .= $tag . '="' . $compiler->_removeQuote($val) . '" ';
        }
    }

	return 'echo Core::getLib(\'html.form\')->formInput(' . $name . ', ' . $value . ', ' . $type . ', \'' . $attrs . '\');';
}