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
 * AJAX Setup
 * 
 * En este archivo se encuentran las instrucciones para inicializar una
 * solicitud AJAX.
 * 
 * @package     Framework\Boot
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------


/*
 * ---------------------------------------------------------------
 *  Cargamos la librería AJAX
 * ---------------------------------------------------------------
 * 
 * Esta se encargará de resolver las solicitudes ajax.
 *
 */
    $AJAX = Core::getLib('ajax');

/*
 * ---------------------------------------------------------------
 *  Determinamos solicitud recibida y asignamos un componente.
 * ---------------------------------------------------------------
 */
    $AJAX->setController();
    
/*
 * ---------------------------------------------------------------
 *  Cargamos e inicializamos el controlador solicitado.
 * ---------------------------------------------------------------
 */
    $AJAX->getController();
    
/*
 * ---------------------------------------------------------------
 *  Sólo nos queda mostrar el resultado de la petición.
 * ---------------------------------------------------------------
 */
    echo $AJAX->result();