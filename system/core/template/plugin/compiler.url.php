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
 * Genera enlaces internos y externos al sitio.
 * 
 * {url link='user.page_one'}
 * {url link='user.'$userName page=$page} // http://www.example.com/core/user_name?page=2
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_url()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_url($tagArgs, &$compiler)
{
	$args = $compiler->_parseArgs($tagArgs);

	if (!isset($args['link']))
	{
		return '';
	}
	$link = $args['link'];
	unset($args['link']);
	$params = '';
	if (count($args))
	{
	   $params = ', array(';
		foreach ($args as $key => $value)
		{
			$params .= '\'' . $key . '\' => ' . $value . ',';
		}
		$params = rtrim($params, ',') . ')';
	}
    
	return 'echo Core::getLib(\'url\')->makeUrl(' . $link . $params . ');';
 }