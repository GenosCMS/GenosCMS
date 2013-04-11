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
 * Component
 * 
 * De esta clase heradan los servicios. Estos se encargan de inter-actuar con
 * la base de datos o realizar tareas más complejas que las de un componente.
 * 
 * @package     Framework\Core\Module
 * @since       1.0.0
 */
class Core_Service {
    
    /**
     * Alias de objectos.
     * 
     * Esto nos permite acceder a las librerías más comunes de una manera más cómoda.
     * 
     * @var array
     */
    private $_objects = array(
        'db' => 'database',
    );
    
    // --------------------------------------------------------------------
    
    /**
     * Método mágico.
     * 
     * Nos permite cargar librerías dentro de variables de la clase.
     * 
     * @param string $name Nombre de la variable solicitada.
     * 
     * @return object Objeto de la librería cargada.
     */
    public function __get($name)
    {
        if (isset($this->_objects[$name]))
        {
            return Core::getLib($this->_objects[$name]);
        }
    }
}