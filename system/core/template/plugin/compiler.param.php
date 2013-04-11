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
 * Muestra un ajuste de configuración.
 * 
 * {param var='core.site_title'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_param()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_param($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
	if (!isset($args['var']))
	{
		return '';
	}

	return 'echo Core::getParam(\'' . $compiler->_removeQuote($args['var']) . '\');';
}