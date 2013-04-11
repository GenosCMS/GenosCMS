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
 * Ajax
 * 
 * Controlador frontal, se encarga de recibir las solicitudes, cargar el
 * bootstrap y el módulo de arranque principal.
 * 
 * @package     Framework
 * @since       1.0.0
 * @filesource
 */
 
/*
 * ---------------------------------------------------------------
 *  Constantes principales.
 * ---------------------------------------------------------------
 */
 
    // Se usa para separar los directorios.
    define('DS', DIRECTORY_SEPARATOR);

    // Ruta principal del framework.
    define('ROOT', dirname(__FILE__));
    
    // Modo depuración
    define('DEBUG_MODE', false);
    
/*
 * --------------------------------------------------------------------
 *  Cargar Bootstrap.
 * --------------------------------------------------------------------
 *
 * Se encarga de iniciar el Framework
 *
 */
    ob_start();
    
    require ROOT . DS . 'system' . DS . 'bootstrap.php';
    
    
/*
 * ---------------------------------------------------------------
 *  Módulo de arranque: AJAX
 * ---------------------------------------------------------------
 * 
 * Se encarga de inicializar los componentes para solicitudes AJAX
 *
 */ 
    require BOOT_PATH . 'ajax.php';
    
    ob_end_flush();