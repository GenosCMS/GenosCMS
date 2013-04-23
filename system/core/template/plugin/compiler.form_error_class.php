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
 * Muestra la clase del error.
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_form_error_class()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_form_error_class($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
    // El nombre es requerido
	if ( ! isset($args['name']))
	{
		return '';
	}
    
    $name = $args['name'];
    $class = (isset($args['class']) ? $compiler->_removeQuote($args['class']) : 'error');

    return 'if (Core::isLoaded(\'form\') && Core::getLib(\'form\')->error(' . $name . ') != \'\') echo \' ' . $class . '\';';
}