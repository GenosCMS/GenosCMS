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
 * Input Cookie
 * 
 * Gestiona la entrada de datos mediante $_COOKIE
 * 
 * @package     Framework\Core\Input
 * @since       1.0.0
 */
class Core_Input_Cookie extends Core_Input {
        
    /**
     * Constructor
     * 
     * Reemplaza al de su clase padre.
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('input.cookie.init') : null;
        
        // $_FILES
        $this->data =& $_COOKIE;
        
        // Filtro
        $this->filter = Core::getLib('filter.input');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Establecer una cookie
     * 
     * @param string $name El nombre de la cookie.
     * @param string $value El valor de la cookie.
     * @param string $expire El tiempo en el que expira la cookie.
     * @param string $path Ruta donde la cookie estará disponible.
     * @param string $domain El dominio para el cual la cookie está disponible.
     * @param bool $secure Solo se transmite por una conexión segura HTTPS
     * @param bool $httpOnly TRUE la cookie será accesible sólo a través del protocolo HTTP
     * 
     * @return void
     */
    public function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOlny = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOlny);
        
        $this->data[$name] = $value;
    }
}