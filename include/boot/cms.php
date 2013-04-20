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
 * CMS Setup
 * 
 * En este archivo se encuentran las instrucciones para inicializar el CMS.
 * 
 * IMPORTANTE: Las instrucciones encontradas en este archivo son vitales
 * para cargar los componentes del CMS. Si se decea alterar su contenido
 * se recomienda hacerlo mediante archivos de arranque que serán alojados
 * en: /inclide/boot/ e incluidos dentro de este archivo como "modulos".
 * 
 * @package     Framework\Boot
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Instanciar la clase: Template
 * ---------------------------------------------------------------
 */
    $TPL = Core::getLib('template');
    
/*
 * ---------------------------------------------------------------
 *  Instanciar la clase: Module
 * ---------------------------------------------------------------
 */ 
    $MOD = Core::getLib('module');
    
/*
 * ---------------------------------------------------------------
 *  Instanciar la clase: Language
 * ---------------------------------------------------------------
 */
    $LANG = Core::getLib('language');
    
    
/*
 * ---------------------------------------------------------------
 *  Ejemplo recomendado para modificar este archivo.
 * ---------------------------------------------------------------
 */
    
    //include INC_PATH . 'boot' . DS . 'cms_example.php';

/** 
 * ---------------------------------------------------------------
 *                 INICIAR CARGA DEL CONTROLADOR
 * ---------------------------------------------------------------
 */
    
/*
 * ---------------------------------------------------------------
 *  Determinamos el controlador que fue solicitado
 * ---------------------------------------------------------------
 */
    $MOD->setController();
    
/*
 * ---------------------------------------------------------------
 *  Agregamos parámetros a la plantilla
 * ---------------------------------------------------------------
 * 
 * Ver archivo /include/boot/template.php
 */
    
    require BOOT_PATH . 'template.php';
    
/*
 * ---------------------------------------------------------------
 *  Agregamos parámetros a la plantilla: Custom
 * ---------------------------------------------------------------
 * 
 * Cada tema puede agregar sus propias variables a la plantilla
 * solicitando archivos CSS & JS y otro tipo de configuraciones.
 */

    if (($headerFile = $TPL->getHeaderFile()))
    {
        require_once $headerFile;
    }
    
/*
 * ---------------------------------------------------------------
 *  Cargamos e inicializamos el controlador solicitado.
 * ---------------------------------------------------------------
 */
    $MOD->getController();
    
/*
 * ---------------------------------------------------------------
 *  Sólo nos queda mostrar la plantilla global del sitio.
 * ---------------------------------------------------------------
 */
    $TPL->getLayout($TPL->displayLayout);
    $TPL->clean();
    
/*
 * --------------------------------------------------------------------
 *  Debug Info
 * --------------------------------------------------------------------
 *
 * Información de depuración.
 *
 */
    DEBUG_MODE ? Core::getLib('debug')->getInfo() : null;
    
/*
 * ---------------------------------------------------------------
 *  Usar GZIP para enviar los datos.
 * ---------------------------------------------------------------
 */
    if (Core::getParam('core.use_gzip'))
    {
        $content = ob_get_contents();
        
        ob_clean();
        
        if (function_exists('gzencode'))
        {
            $gzipContent = gzencode($content, Core::getParam('core.gzip_level'), FORCE_GZIP);
        }
        else
        {
            if (function_exists('gzcompress') && function_exists('crc32'))
            {
                $size = strlen($content);
                $crc = crc32($content);
                $gzipContent = "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\xff";
                $gzipContent .= substr(gzcompress($content, Core::getParam('core.gzip_level')), 2, -4);
                $gzipContent .= pack('V', $crc);
                $gzipContent .= pack('V', $size);
            }
        }

        if (isset($gzipContent))
        {
            header("Content-Encoding: gzip");
        }
        
        echo (isset($gzipContent) ? $gzipContent : $content);
    }