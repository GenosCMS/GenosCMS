<?php
/**
* Polaris Framework
*
* HMVC Framework
*
* @package     Polaris
* @author      Ivan Molina Pavana <montemolina@live.com>
* @copyright   Copyright (c) 2013
* @version     1.0
*/

// ------------------------------------------------------------------------

/**
 * Module Info
 * 
 * Esta clase contiene la información del módulo.
 * 
 * @package     Polaris
 * @subpackage  Core
 * @category    Library
 * @author      Ivan Molina Pavana <montemolina@live.com>
 */
class Module_Core {
    
    public $tables = array(
		'menu',
		'module',
        'product',
        'rewrite',
        'setting',
		'setting_group',
    );
    
    public $fileWritable = array(
        'tmp/cache',
        'include/setting/server.php'
    );
}