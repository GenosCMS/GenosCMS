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
 * Nos permite asignar variables a la plantilla en tiempo de ejecución.
 * 
 * {assign var='Var1' value='Valor'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_assign()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_assign($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
	if (!isset($args['var']))
	{
		return '';
	}
	if (!isset($args['value']))
	{
		return '';
	}
	return '$this->assign(\'' . $compiler->_removeQuote($args['var']) . '\', ' . $args['value'] . ');';
}