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
 * De esta clase heradan los componentes ya sea un Controlador o un Bloque.
 * 
 * Contiene las instrucciones para que el componente pueda funcionar.
 * 
 * @package     Framework\Core\Module
 * @since       1.0.0
 */
class Core_Component {
    
    /**
     * Parámetros que se pueden pasar a un componente aparte de los mediante request.
     * 
     * @var array
     */
    private static $_params = array();
    
    /**
     * Alias de objectos.
     * 
     * Esto nos permite acceder a las librerías más comunes de una manera más cómoda.
     * 
     * @var array
     */
    private $_objects = array(
        'template' => 'template',
        'input' => 'input',
        'url' => 'url'
    );
    
    /**
     * Constructor 
     * 
     * @param array $params Parámetros que asignarémos al componente.
     * 
     * @return void
     */
    public function __construct($params)
    {
        self::$_params = $params['params'];
    }
    
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
    
    // --------------------------------------------------------------------
    
    /**
     * Establecer un parámetro.
     * 
     * Con ayuda de este método, se pueden declarar parámetros desde componentes
     * externos y ser usados en todos los que hereden de esta clase.
     * 
     * @param array|string $params Nombre del parámetro o lista de parámetros que serán asignados.
     * @param string $value Valor del parámetro.
     * 
     * @return void
     */
    public function setParam($params, $value = '')
    {
        if ( ! is_array($params))
        {
            $params = array($params => $value);
        }
        
        foreach ($params as $var => $value)
        {
            self::$_params[$var] = $value;
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener un parámetro para cualquier componente.
     * 
     * @param string $var Nombre del parámetro.
     * @param mixed $default Valor por defecto en caso de no existir.
     * 
     * @return mixed Valor del parámetro.
     */
    public function getParam($var, $default = null)
    {
        return (isset(self::$_params[$var]) ? self::$_params[$var] : $default);
    }
}