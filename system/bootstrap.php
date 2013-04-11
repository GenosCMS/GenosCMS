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
 * Bootstrap
 * 
 * Se encarga de inicializar los componentes base del framework.
 * 
 * @package     Framework\Boot
 * @since       1.0.0
 * @filesource
 */
 
/*
 * ---------------------------------------------------------------
 *  Memoria usada por PHP / Tiempo ejecución.
 * ---------------------------------------------------------------
 */
    define('START_MEM', memory_get_usage());
    
    define('START_TIME', microtime());
    
/*
 * ---------------------------------------------------------------
 *  Límites de tiempo y memoria 
 * ---------------------------------------------------------------
 */
    @set_time_limit(300); 
    @ini_set('memory_limit', '64M');
    
/*
 * ---------------------------------------------------------------
 *  Tiempo UNIX
 * ---------------------------------------------------------------
 */
    define('CORE_TIME', time());
    
/*
 * ---------------------------------------------------------------
 *  Se cargan las variables constantes.
 * ---------------------------------------------------------------
 */
    require ROOT . DS . 'include' . DS . 'setting' . DS . 'constants.php';
    
/*
 * ---------------------------------------------------------------
 *  Cargamos las clases base.
 * ---------------------------------------------------------------
 */
    require CORE_PATH . 'core' . DS . 'core.class.php';
    require CORE_PATH . 'error' . DS . 'error.class.php';
    require CORE_PATH . 'module' . DS . 'component.class.php';
    require CORE_PATH . 'module' . DS . 'service.class.php';
    
/*
 * ---------------------------------------------------------------
 *  Reporte de errores
 * ---------------------------------------------------------------
 *
 *  Modificar en el archivo de constantes.
 *
 */
    error_reporting((DEBUG_MODE ? E_ALL | E_STRICT : 0));
    
/*
 * ---------------------------------------------------------------
 *  Debug: Iniciando la carga del sistema
 * ---------------------------------------------------------------
 */
    DEBUG_MODE ? Core::mark('system.init') : null;
    
/*
 * ---------------------------------------------------------------
 *  Inicializar ajustes
 * ---------------------------------------------------------------
 *
 * Esto carga las configuraciones del sitio.
 *
 */
    Core::getLib('setting')->init();
    
/*
 * ---------------------------------------------------------------
 *  Sesión de usuario
 * ---------------------------------------------------------------
 *
 * Carga la sesión del usuario.
 *
 * Lo hacemos desde un archivo de la carpeta 'boot' para evitar
 * modificaciones en el bootstrap.
 *
 */
    require BOOT_PATH . 'session.php';