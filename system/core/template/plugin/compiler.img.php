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
 * Genera la etiqueta HTML de una imagen
 * 
 * {img src='logo.png'}
 * {img src='loader.gif' static='true'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_img()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_img($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);
    
	if (!isset($args['src']))
	{
		return '';
	}
	$src = $args['src'];
    unset($args['src']);
    $static = 'false';
    $attrs = '';
    if (count($args))
    {
        foreach ($args as $tag => $value)
        {
            if ($tag == 'static')
            {
                $static = 'true';
                continue;
            }
            
            $attrs .= $tag . '="' . $compiler->_removeQuote($value) . '" ';
        }
    }
    
	return 'echo $this->getImage(' . $src . ', ' . $static . ', \''.$attrs.'\');';
}