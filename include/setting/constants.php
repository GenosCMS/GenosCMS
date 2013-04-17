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
 * Constantes
 * 
 * Definimos las constantes principales del sistema, modificar el valor de estas
 * puede resultar en el mal funcionamiento del framework.
 * 
 * @package     Framework\Setting
 * @since       1.0.0
 * @filesource
 */
 
/*
 * ---------------------------------------------------------------
 *  General
 * ---------------------------------------------------------------
 */

    // Ruta de la aplicación
    define('INC_PATH', ROOT . DS . 'include' . DS);
    
    // Ruta de archivos de arranque
    define('BOOT_PATH', INC_PATH . 'boot' . DS);
    
    // Ruta de las configuraciones
    define('SETTING_PATH', INC_PATH . 'setting' . DS);
    
    // Ruta de los plugins
    define('PLUGIN_PATH', INC_PATH . 'plugin' . DS);
    
/*
 * ---------------------------------------------------------------
 *  Módulos
 * ---------------------------------------------------------------
 */
    
    // Ruta de los módulos
    define('MOD_PATH', ROOT . DS . 'module' . DS);
    
    // Ruta de los lenguajes
    //define('MOD_LANG', 'library' . DS . 'locale' . DS);
    define('MOD_INC', 'include' . DS);
    
    // Componentes de un módulo
    define('MOD_COMPONENT', 'library' . DS . 'component');
    
    // Servicios de un módulo
    define('MOD_SERVICE', 'library' . DS . 'service');
    
    // Plantillas de los módulos
    define('MOD_TPL', 'template' . DS);
    
/*
 * ---------------------------------------------------------------
 *  Plantillas
 * ---------------------------------------------------------------
 */
    
    // Ruta de los temas
    define('TPL_PATH', ROOT . DS . 'theme' . DS);
    
    // Sufijo de las plantillas
    define('TPL_SUFFIX', '.view.tpl');
    
/*
 * ---------------------------------------------------------------
 *  Sistema
 * ---------------------------------------------------------------
 */
 
    // Ruta del sistema.
    define('SYS_PATH', ROOT . DS . 'system' . DS);
    
    // Ruta del núcleo
    define('CORE_PATH', SYS_PATH . 'core' . DS);
    
    // Template Plugins
    define('TPL_PLUGIN', CORE_PATH . 'template' . DS . 'plugin' . DS);
    
/*
 * ---------------------------------------------------------------
 *  Directorio temporal
 * ---------------------------------------------------------------
 */

    // Ruta del directorio temporal
    define('TMP_PATH', ROOT . DS . 'tmp' . DS);
    
    // Directorio del caché
    define('CACHE_PATH', TMP_PATH . 'cache' . DS);