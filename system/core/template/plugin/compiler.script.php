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
 * Genera la inclusión de los archivos JavaScript solicitados por la plantilla.
 * 
 * {script}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_script()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_script($tagArgs, &$compiler)
{
    return 'echo $this->_getScripts();';
}