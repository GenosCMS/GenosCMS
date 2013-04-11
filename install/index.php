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
* index
*
* Controlador frontal, se encargará de recibir todas las solicitudes.
*
* @package     Polaris
* @subpackage  Core
* @category    Library
* @author      Ivan Molina Pavana <montemolina@live.com>
*/
 
/*
 * ---------------------------------------------------------------
 *  Constantes generales.
 * ---------------------------------------------------------------
 */
 
    // Se usa para separar los directorios.
    define('DS', DIRECTORY_SEPARATOR);

    // Ruta principal del framework.
    define('ROOT', dirname(dirname(__FILE__)));
    
    // Ruta del instalador
    define('INSTALL_PATH', ROOT . DS . 'install' . DS);
    
    // Variable que nos indica que estámos instalando el sistema
    define('CORE_INSTALLER', true);
/*
 * --------------------------------------------------------------------
 * Cargar Bootstrap.
 * --------------------------------------------------------------------
 *
 * Let's go...
 *
 */ 
    require ROOT . DS . 'system' . DS . 'bootstrap.php';
    
    
    /*require INSTALL_PATH . 'include' . DS . 'installer.class.php';
    
    $installer = new Core_Installer();
    $installer->run();*/
    
    require INSTALL_PATH . 'include' . DS . 'loader.class.php';
    
    $loader = new Core_Loader();
    $loader->run();