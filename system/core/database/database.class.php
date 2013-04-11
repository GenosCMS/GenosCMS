<?php
/**
 * Genos CMS
 * 
 * @author      Ivan Molina Pavana <montemolina@live.com>
 * @copyright   Copyright (c) 2013, Ivan Molina Pavana <montemolina@live.com>
 * @license     GNU General Public License, version 3
 */

// ------------------------------------------------------------------------
    Core::getLibClass('core.database.driver');
// ------------------------------------------------------------------------

/**
 * Database
 * 
 * Manejo de la base de datos.
 * 
 * @package     Framework\Core\Database
 * @since       1.0.0
 */
class Core_Database {
    
    /**
     * Driver Object
     * 
     * @var object
     */
    private $_object = null;
    
    /**
     * Constructor
     * 
     * Carga he inicializa el driver que necesitamos.
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('database.init') : null;
        
        if ( ! $this->_object)
        {   
            switch(Core::getParam(array('db', 'driver')))
            {
                default:
                    $driver = 'core.database.driver.mysql';
            }
            
            $this->_object = Core::getLib($driver);
            $this->_object->connect(Core::getParam(array('db', 'host')), Core::getParam(array('db', 'user')), Core::getParam(array('db', 'pass')), Core::getParam(array('db', 'name')), Core::getParam(array('db', 'port')));
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener instancia.
     * 
     * @return object
     */
    public function &getInstance()
    {
        return $this->_object;
    }
}