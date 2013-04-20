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
 * Nos permite comparar dos campos.
 *
 * Se usa para saber si el contenido de dos campos es igual.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * rule_matches()
 *
 * @param string $str Valor del campo actual.
 * @param string $field Campo con el cual vamos a comprar.
 * @param object $form Clase principal del formulario.
 *
 * @return string bool
 */
function form_rule_matches($str, $field, &$form)
{
    $value = $form->get($field);

    return (is_null($value) || $str !== $value) ? false : true;
}