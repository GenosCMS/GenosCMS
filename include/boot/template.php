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
 * Template Info
 * 
 * Asigna a la plantilla todas las variables base que permiten trabajar
 * correctamente al CMS, así como archivos CSS & JS.
 * 
 * @package     Framework\Boot
 * @since       1.0.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Variables
 * ---------------------------------------------------------------
 * 
 * Agregamos a la plantilla todas las variables "globales".
 */
    $TPL->assign(array(
            'menus' => Core::getLib('html.menu')->getMenu('main'),
        )
    );
    
/*
 * ---------------------------------------------------------------
 *  Archivos CSS
 * ---------------------------------------------------------------
 * 
 * Agregamos los archivos CSS requeridos por el CMS.
 *
 * Por lo general se trata de archivos 'static_style'
 */
    //$TPL->css(array());
    
/*
 * ---------------------------------------------------------------
 *  Archivos JS
 * ---------------------------------------------------------------
 * 
 * Agregamos los archivos JavaScript requeridos por el sistema.
 *
 * Por lo general se trata de archivos => 'static_script'
 */
    $TPL->js(array(
            'jquery.min.js' => 'static_script',
            'core/ajax.js' => 'static_script',
        )
    );