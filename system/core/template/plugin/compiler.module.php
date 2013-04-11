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
 * Cargar un bloque de un módulo y su contenido.
 * 
 * {module name='core.template-menu'} => {menu}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_module()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_module($tagArgs, &$compiler)
{
    $args = $compiler->_parseArgs($tagArgs);
    
    $module = $args['name'];
    unset($args['name']);
    
    $array = '';
    foreach ($args as $key => $value)
    {
        if (substr($value, 0, 1) != '$' && $value !== 'true' && $value !== 'false')
        {
            $value = '\'' . $compiler->_removeQuote($value) . '\'';
        }
        $array .= '\'' . $key . '\' => ' . $value . ',';
    }
    
    return 'Core::getBlock(' . $module . ', array(' . rtrim($array, ',') . '));';
}