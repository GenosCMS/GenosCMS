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
 * Nos permite obtener una frase en el idioma por defecto o espesificar uno.
 * 
 * {phrase var='core.welcome'}
 * {phrase var='core.welcome_name' name='Ivan'}
 * {phrase var='core.welcome' idiom='en_GB'}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_phrase()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_phrase($tagArgs, &$compiler)
{
    $args = $compiler->_parseArgs($tagArgs);
    if ( ! $args['var'])
    {
        return '';
    }
    $var = $args['var'];
    unset($args['var']);
    $array = '';
    $idiom = '';
    if (count($args))
    {
        $array = ', array(';
        foreach ($args as $key => $value)
        {
            if ($key == 'idiom')
            {
                $idiom = $value;
            }
            $array .= '\'' . $key . '\' => ' . $value . ',';
        }
        $array = rtrim($array, ',') . ')';
    }
    return 'echo Core::getPhrase(' . $var . $array . ($idiom != '' ? ',' . $idiom : '') .');';
}