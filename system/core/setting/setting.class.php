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
 * Setting
 * 
 * Obtener y gestionar las configuraciones del sistema.
 * 
 * @package     Framework\Core\Setting
 * @since       1.0.0
 * @final
 */
class Core_Setting {
    
    /**
     * Lista de parámetros de configuración
     * 
     * @var array
     */
    private $_params = array();
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('setting.init') : null;
                
        $_CONF = array();
        $message = 'Oops! Genos CMS no est&aacute; instalado. Ejecute el <a href="install/index.php">script de instalaci&oacute;n</a> para configurar su sitio.';
        
        if ( !defined('CORE_INSTALLER') && !file_exists(SETTING_PATH . 'server.php') && file_exists(ROOT . DS . 'install' . DS . 'index.php'))
        {
            exit($message);
        }
        
        $filePath = SETTING_PATH . 'server.php';
        
        if ( file_exists($filePath))
        {
            require $filePath;
            
            if ( ! defined('CORE_INSTALLER'))
            {
                if ( ! isset($_CONF['core.is_installed']) || ! $_CONF['core.is_installed'])
                {
                    exit($message);
                }
            }
        }
        
		if ((!isset($_CONF['core.host'])) || (isset($_CONF['core.host']) && $_CONF['core.host'] == 'HOST_NAME'))
		{
			$_CONF['core.host'] = $_SERVER['HTTP_HOST'];
		}
			
		if ((!isset($_CONF['core.folder'])) || (isset($_CONF['core.folder']) && $_CONF['core.folder'] == 'SUB_FOLDER'))
		{
			$_CONF['core.folder'] = '/';
		}
        
        require SETTING_PATH . 'common.php';
        
        $this->_params =& $_CONF;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Inicializar y cargar ajustes
     * 
     * @return void
     */
    public function init()
    {
        $cache = Core::getLib('cache');
        
        $id = $cache->set('setting');
        
        if ( ! ($rows = $cache->get($id)))
        {
            $rows = Core::getLib('database')->select('s.type_id, s.var_name, s.value_actual, s.module_id AS module_name')
                ->from('setting', 's')
                ->join('module', 'm', 'm.module_id = s.module_id = 1 AND m.is_active = 1')
                ->exec('rows');
                
            foreach($rows as $key => $row)
            {
                // Definimos el tipo de variable
                switch($row['type_id'])
                {
                    case 'boolean':
						if (strtolower($rows[$key]['value_actual']) == 'true' || strtolower($rows[$key]['value_actual']) == 'false')
						{
							$rows[$key]['value_actual'] = (strtolower($rows[$key]['value_actual']) == 'true' ? '1' : '0');
						}						
						settype($rows[$key]['value_actual'], 'bool');
                    break;
                    case 'integer':
                        settype($rows[$key]['value_actual'], 'int');
                    break;
                }
            }
            
            $cache->save($id, $rows);
        }
        
        // Asignamos
        foreach ($rows as $row)
        {
            $this->_params[$row['module_name'] . '.' . $row['var_name']] = $row['value_actual'];
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Añadir un parámetro de configuración.
     * 
     * @param array $name Nombre del parámetro.
     * @param mixed $value Valor del parámetro.
     * 
     * @return void
     */
    public function set($param, $value = null)
    {
        if (is_string($param))
        {
            $this->_params[$param] = $value;
        }
        else
        {
            foreach($param as $key => $value)
            {
                $this->_params[$key] = $value;
            }
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar un parámetro de configuración
     * 
     * @param string $name Nombre del parámetro.
     * 
     * @return mixed Contenido del parámetro.
     */
    public function get($name)
    {
        if (is_string($name))
        {
            return (isset($this->_params[$name]) ? $this->_params[$name] : Core_Error::trigger('Falta el parámetro: ' . $name));
        }
        else
        {
            return (isset($this->_params[$name[0]][$name[1]]) ? $this->_params[$name[0]][$name[1]] : Core_Error::trigger('Falta el parámetro: ' . $name[0] . ' => ' . $name[1]));
        }
    }
}