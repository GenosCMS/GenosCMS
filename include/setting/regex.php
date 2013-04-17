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
 * Expresiones regulares.
 * 
 * La lista de expresiones regulares de este archivo son utilizadas por el
 * validador de formularios, más espesífico el plugin rule.is_valid.php
 * 
 * @package     Framework\Setting
 * @since       1.0.0
 * @filesource
 */

/*
 * ---------------------------------------------------------------
 *  Nombre
 * ---------------------------------------------------------------
 *
 * Sólo permitimos nombres que no contengan caracteres especiales.
 */
    $_regex['name'] = '/^([a-zA-ZÀ-ÖØ-öø-ÿ ]+)$/i';

/*
 * ---------------------------------------------------------------
 *  Nombre de usuario
 * ---------------------------------------------------------------
 *
 * Nombre de usuario alfanumérico y guión bajo, sólo de 3 a 26
 * caracteres.
 */
    $_regex['user_name'] = '/^[a-zA-Z0-9_]{3,26}$/';
    
/*
 * ---------------------------------------------------------------
 *  Email
 * ---------------------------------------------------------------
 *
 * Verifica que se trate de una cuenta de email válida.
 */
    $_regex['email'] = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix';

/*
 * ---------------------------------------------------------------
 *  URL
 * ---------------------------------------------------------------
 *
 * Verifica direcciones URL
 */    
    $_regex['url'] = '/^(?:(ftp|http|https):)?(?:\/\/(?:((?:%[0-9a-f]{2}|[\-a-z0-9_.!~*\'\(\);:&=\+\$,])+)@)?(?:((?:[a-z0-9](?:[\-a-z0-9]*[a-z0-9])?\.)*[a-z](?:[\-a-z0-9]*[a-z0-9])?)|([0-9]{1,3}(?:\.[0-9]{1,3}){3}))(?::([0-9]*))?)?((?:\/(?:%[0-9a-f]{2}|[\-a-z0-9_.!~*\'\(\):@&=\+\$,;])+)+)?\/?(?:\?.*)?$/i';