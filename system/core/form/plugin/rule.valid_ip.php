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
 * Validar dirección IP.
 *
 * Nos permite recibir direcciones IP válidas.
 *
 * @package     Framework\Core\Form\Plugin
 * @since       1.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * form_rule_valid_ip()
 *
 * @param string $str Valor del campo.
 * @param bool $incRange (Opcional) TRUE para incluir rangos privados y reservados.
 *
 * @return string bool
 */
function form_rule_valid_ip($str, $incRange = false)
{
    return (bool) ($incRange ? filter_var($str, FILTER_VALIDATE_IP) !== false : filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE) !== false);
}