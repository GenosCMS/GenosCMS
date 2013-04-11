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
 * Carga un layout desde el tema actual.
 * 
 * {layout file='header'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_layout()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_layout($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
	if (!isset($args['file']))
	{
		return '';
	}
    
	return ' $this->getLayout(' . $args['file'] . ');';
}