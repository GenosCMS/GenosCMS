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
 * plugin rule.is_valid.php
 * 
 * @package     Framework\Setting
 * @since       1.0.0
 * @filesource
 */
    $_regex = array();
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

/*
 * ---------------------------------------------------------------
 *  Caracteres Alfabéticos
 * ---------------------------------------------------------------
 *
 * Solo permite caracteres del alfabeto inglés.
 */
    $_regex['alpha'] = '/^([a-z])+$/i';

/*
 * ---------------------------------------------------------------
 *  Caracteres Alfanuméricos
 * ---------------------------------------------------------------
 *
 * Solo permite caracteres del alfabeto inglés y números.
 */
    $_regex['alpha_numeric'] = '/^([a-z0-9])+$/i';

/*
 * ---------------------------------------------------------------
 *  Caracteres Alfanuméricos + Guiones
 * ---------------------------------------------------------------
 *
 * Solo permite caracteres del alfabeto inglés, números y guiones.
 */
    $_regex['alpha_dash'] = '/^([-a-z0-9_-])+$/i';

/*
 * ---------------------------------------------------------------
 *  Caracteres numéricos
 * ---------------------------------------------------------------
 *
 * Solo permite caracteres numéricos.
 */
    $_regex['numeric'] = '/^[\-+]?[0-9]*\.?[0-9]+$/';

/*
 * ---------------------------------------------------------------
 *  Números enteros
 * ---------------------------------------------------------------
 *
 * Solo permite números enteros.
 */
    $_regex['integer'] = '/^[\-+]?[0-9]+$/';

/*
 * ---------------------------------------------------------------
 *  Números con decimales.
 * ---------------------------------------------------------------
 *
 * Solo permite números con decimal.
 */
    $_regex['decimal'] = '/^[\-+]?[0-9]+\.[0-9]+$/';

/*
 * ---------------------------------------------------------------
 *  Números naturales.
 * ---------------------------------------------------------------
 *
 * Solo permite números naturales.
 */
    $_regex['natural'] = '/^[0-9]+$/';

/*
 * ---------------------------------------------------------------
 *  Base64
 * ---------------------------------------------------------------
 *
 * Solo permite cadenas con una codificación Base64
 */
    $_regex['base64'] = '/[^a-zA-Z0-9\/\+=]/';