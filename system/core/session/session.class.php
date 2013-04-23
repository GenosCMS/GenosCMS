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
 * Session
 *
 * Se encarga del manejo de las sesiones del usuario.
 *
 * @package     Framework\Core\Form
 * @since       1.0.0
 */
class Core_Session {
    /**
     * Inicializar la sesión
     *
     * @access public
     * @return void
     */
    public function init()
    {
        if ( ! $_SESSION)
        {
            session_start();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set a session
     *
     * @access public
     * @param string $name
     * @param string $value
     * @return void
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    // --------------------------------------------------------------------

    /**
     * Get a session
     *
     * @access public
     * @param string $name
     * @return string
     */
    public function get($name)
    {
        if (isset($_SESSION[$name]))
        {
            return (empty($_SESSION[$name]) ? true : $_SESSION[$name]);
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Remove a session
     *
     * @access public
     * @param array $names
     * @return void
     */
    public function remove($names)
    {
        if ( ! is_array($names))
        {
            $names = array($names);
        }

        foreach ($names as $name)
        {
            unset($_SESSION[$name]);
        }
    }
}