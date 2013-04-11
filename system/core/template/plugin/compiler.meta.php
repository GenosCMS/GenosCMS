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
 * Genera las meta tags del sitio.
 * 
 * {meta}
 * 
 * @package     Framework\Core\Template\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------
 
/**
 * tpl_compiler_meta()
 * 
 * @param string $tagArgs
 * @param Template_Compiler
 * 
 * @return string Compiled
 */
function tpl_compiler_meta($tagArgs, &$compiler)
{
    return 'echo $this->_getMeta();';
}