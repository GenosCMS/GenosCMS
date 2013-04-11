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
 * Database Support
 * 
 * Verificar el soporte para motores de base de datos.
 * 
 * @package     Framework\Core\Database
 * @since       1.0.0
 * @todo        Agregar soporte para otros motores.
 */
class Core_Database_Support {

    /**
     * Lista de motores soportados
     * 
     * @var array
     */
    private $_dbs = array(
        'mysql' => array(
            'label' => 'MySQL',
            'module' => 'mysql',
            'driver' => 'mysql',
            'available' => true,
        ),
        'mysqli' => array(
            'label' => 'MySQL con extensión MySQLi',
            'module' => 'mysqli',
            'driver' => 'mysqli',
            'available' => true
        ),
    );   
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener los motores soportados por el servidor actual
     * 
     * @return array Lista de motores soportados
     */
    public function getSupported()
    {
        foreach ($this->_dbs as $key => $dbs)
        {
            // Buscamos que la extensión esté cargada en el php.ini y que exista soporte por parte de Genos
            if ( ! @extension_loaded($dbs['module']) || ! file_exists(CORE_PATH . 'database' . DS . 'driver' . DS . $dbs['driver'] . '.class.php'))
            {
                $this->_dbs[$key]['available'] = false;
            }
        }
        
        return $this->_dbs;
    }
}