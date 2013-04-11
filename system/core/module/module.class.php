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
 * Module
 * 
 * Se encarga de la gestión de los módulos, el punto fuerte de este framework.
 * 
 * @package     Framework\Core\Module
 * @since       1.0.0
 */
class Core_Module {
    
    /**
     * Lista de los módulos instalados
     * 
     * @var array
     */
    private $_modules = array();
    
    /**
     * Módulo que será ejecutado.
     * 
     * @var string
     */
    private $_module = '';
    
    /**
     * Controlador que será ejecutado.
     * 
     * @var string
     */
    private $_controller = 'index';
    
    /**
     * Lista de componentes cargados.
     * 
     * @var array
     */
    private $_components = array();
    
    /**
     * Lista de los servicios activos.
     * 
     * @var array
     */
    private $_services = array();
    
    /**
     * Lista de resultados devueltos por los componentes.
     * 
     * @var array
     */
    private $_return = array();
    
    /**
     * Objecto del controlador activo.
     * 
     * @var object
     */
    private $_object = null;
    
    /**
     * Constructor
     * 
     * Se encargará de cachear los módulos instalados.
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('module.init') : null;
        
        $this->_cacheModules();
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Checa si un módulo es válido o no.
     * 
     * @param string $module Nombre del módulo
     * 
     * @return bool TRUE si el módulo existe y está activo.
     */
    public function isModule($module)
    {
        $module = strtolower($module);
        
        return (isset($this->_modules[$module]) ? true : false);
    }
    
    // --------------------------------------------------------------------
 
    /**
     * Determina el controlador de la página en la que estámos.
     * 
     * @param string $controller Opcionalmente se puede definir un controlador para ser cargado.
     * 
     * @return void
     */
    public function setController($controller = '')
    {
        if ($controller)
        {
            $parts = explode('.', $controller);
            $this->_module = $parts[0];
            $this->_controller = substr_replace($controller, '', 0, strlen($this->_module . '_'));
            
            $this->getController();
            
            return;
        }
        
        // Analizamos la ruta
        $url = Core::getLib('url');
        $url->setRouting();
        // Definiendo el módulo
        $this->_module = ($url->segment(1)) ? $url->segment(1) : Core::getParam('core.default_module');
        
        // Directorio del módulo
        $dir = MOD_PATH . $this->_module . DS;
        
        // Buscamos el controlador
        if ($url->segment(2) && file_exists($dir . MOD_COMPONENT . DS . 'controller' . DS . $url->segment(2) . '.controller.php'))
        {
            $this->_controller = $url->segment(2);
        }
        elseif ($this->_module != Core::getParam('admin.admin_folder') && $url->segment(3) && file_exists($dir . MOD_COMPONENT . DS . 'controller' . DS . $url->segment(2) . DS . $url->segment(3) . '.controller.php'))
        {
            $this->_controller = $url->segment(2) . '.' . $url->segment(3);
        }
        elseif ($this->_module != Core::getParam('admin.admin_folder') && $url->segment(2) && file_exists($dir . MOD_COMPONENT . DS . 'controller' . DS . $url->segment(2) . DS . 'index.controller.php'))
        {
            $this->_controller = $url->segment(2) . '.index';
        }
        
        // Tal vez se cambió el folder del panel de administración.
        if (Core::getParam('admin.admin_folder') != 'admin' && $url->segment(1) == Core::getParam('admin.admin_folder'))
        {
            $this->_module = 'admin';
        }

        // Checamos la existencia del módulo
        if ( ! isset($this->_modules[$this->_module]))
        {
            $this->_module = 'error';
            $this->_controller = '404';
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Carga el controlador principal (solicitado desde el navegador).
     * 
     * @return void
     */
    public function getController()
    {
        return $this->getComponent($this->_module . '.' . $this->_controller, array('noTemplate' => false), 'controller');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener plantilla del controlador principal.
     * 
     * Esta es la función usada en la etiqueta {content}
     * 
     * @return void
     */
    public function getControllerTemplate()
    {
        $class = $this->_module . '.controller.' . $this->_controller;
        
        if (isset($this->_return[$class]) && $this->_return[$class] === false)
        {
            return false;
        }
        
        // Obtener la plantilla y mostrar su contenido para el controlador específico.
        Core::getLib('template')->getTemplate($class);
        
        // TODO:: _clean() Method
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar el componente de un módulo.
     * 
     * Los componentes son los bloques que construyen el sitio. Un componente
     * puede ser un "bloque" o un "controlador".
     * 
     * @param string $class Nombre del componente a cargar.
     * @param array $params Parámetros que se pueden pasar al componente.
     * @param string $type Identificar si este componente es un bloque o un controlador.
     * @param bool $templateParams Si se establece como TRUE los parámetros $params serán asignados a la plantilla.
     * 
     * @return mixed Devuelve el objeto componente si existe, FALSE de lo contrario.
     */
    public function getComponent($class, $params = array(), $type = 'block', $templateParams = false)
    {
        // Debug point
        DEBUG_MODE ? Core::mark($type . '.start (' . $class . ')') : null;

        $parts = explode('.', $class);
        $module = $parts[0];
        $component = $type . DS . implode(DS, array_slice($parts, 1));
        
        // Si es un controlador lo asiganmos...
        /*if ($type == 'controller')
        {
            $this->_module = $module;
            $this->_controller = substr_replace(str_replace('.', DS, $class), '', 0, strlen($module . DS));
        }*/
        
        // Clave del componente
        $hash = md5($class . $type);
        
        // Ya existe?
        if (isset($this->_components[$hash]))
        {
            $this->_components[$hash]->__construct(array('params' => $params));
        }
        else
        {
            $classFile = MOD_PATH . $module . DS . MOD_COMPONENT . DS . $component . '.' . $type . '.php';
            
            if ( ! file_exists($classFile) && isset($parts[1]))
            {
                // Buscamos un subdirectorio
                $classFile = MOD_PATH . $module . DS . MOD_COMPONENT . DS . $component . DS . $parts[1] . '.' . $type . '.php';
            }
            
            if ( ! file_exists($classFile))
            {
                Core_Error::trigger('Error al cargar el componente: ' . str_replace(MOD_PATH, '', $classFile));
            }
            
            require $classFile;
            
            // Cargamos el componente
            $this->_components[$hash] = Core::getObject($module . '_component_' . str_replace(DS, '_', $component), array('params' => $params));
        }
        
        // Ejecutamos el método principal del componente solicitado.
        if ($type == 'controller')
        {
            $return = call_user_func_array(array(&$this->_components[$hash], 'main'), Core::getLib('url')->params($class));   
        }
        else
        {
            $return = $this->_components[$hash]->main();
        }
        
        // Respuesta
        $this->_return[$class] = $return;
        
        // Si devolvemos en el componente FALSE, entonces no hay necesidad de mostrar su plantilla.
        if (is_bool($return) && ! $return)
        {
            return $this->_components[$hash];
        }
        
        // Pasarémos los parámetros a la plantilla?
        if ($templateParams == true)
        {
            Core::getLib('template')->assign($params);
        }
        
        // Vamos a mostrar la plantilla del componente?
        if ( ! isset($params['noTemplate']))
        {
            $componentTemplate = $module . '.' . str_replace(DS, '.', $component);
            
            Core::getLib('template')->getTemplate($componentTemplate);
            
            // TODO: Limpiar variables
        }
        
        // Debug point
        DEBUG_MODE ? Core::mark($type . '.end (' . $class . ')') : null;
        
        return $this->_components[$hash];
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Carga una clase de servicio.
     * 
     * Las clases de servicio se encargan de interactuar con la base de datos
     * o ejecutar instrucciones más complejas.
     * 
     * @param string $class Nombre de la clase de servicio.
     * @param array $params Parámetros a pasar a la clase.
     * 
     * @return object Objeto del servicio cargado.
     */
    public function getService($class, $params = array())
    {
        // Debug point
        DEBUG_MODE ? Core::mark('service.start (' . $class . ')') : null;
        
        //
        if (isset($this->_services[$class]))
        {
            return $this->_services[$class];
        }
        
		if (preg_match('/\./', $class) && ($parts = explode('.', $class)) && isset($parts[1]))
		{
			$module = $parts[0];
			$service = $parts[1];			
		}
		else 
		{
			$module = $class;
			$service = $class;
		}
        
        $file = MOD_PATH . $module . DS . MOD_SERVICE . DS . $service . '.service.php';
        
        if ( ! file_exists($file))
        {
            if (isset($parts[2]))
            {
                $file = MOD_PATH . $module . DS . MOD_SERVICE . DS . $service . DS . $parts[2] . '.service.php';
                $service .= '_' . $parts[2];
            }
            else
            {
                $file = MOD_PATH . $module . DS . MOD_SERVICE . DS . $service . DS . $service . '.service.php';
            }
        }
        
        if ( ! file_exists($file))
        {
            Core_Error::trigger('No se puede cargar el servicio: ' . $service);
        }
        
        require $file;
        
        $this->_services[$class] = Core::getObject($module . '_service_' . $service);
        
        return $this->_services[$class];
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener todos los módulos alojados en el directorio de módulos MOD_PATH
     * 
     * @return array Lista de arreglos.
     */
    public function getModuleFiles()
    {
        $folders = array('core' => array(), 'external' => array());
        $coreId = 0;
        $modules = Core::getLib('file')->getFiles(MOD_PATH);
        
        foreach ($modules as $key => $module)
        {
            // Sólo módulos instalables
            if ( ! file_exists(MOD_PATH . $module . DS . MOD_INC . 'install.xml'))
            {
                continue;
            }
            
            // Separar módulos del núcleo de los externos
            $content = file_get_contents(MOD_PATH . $module . DS . MOD_INC . 'install.xml');
            
            $type = (preg_match("/<is_core>1<\/is_core>/i", $content) ? 'core' : 'external');
            
            $folders[$type][$key] = array(
                'name' => $module
            );
        }
        
        return $folders;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener información del módulo.
     * 
     * @param string $module Nombre del módulo.
     * @param string $param Parámetro con el valor de la información.
     * 
     * @return mixed Contenido del parámetro.
     */
    public function init($module, $param)
    {
        if ( ! file_exists(MOD_PATH . $module . DS . MOD_INC . 'module.php'))
        {
            return false;
        }
        
        require MOD_PATH . $module . DS . MOD_INC . 'module.php';
        
        $moduleClass = 'Module_' . $module;
        
        if (array_key_exists($param, get_class_vars($moduleClass)))
        {        
            $module = new $moduleClass;
            return $module->{$param};
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Guardar en caché los módulos instalados.
     * 
     * @return void
     */
    private function _cacheModules()
    {
        $cache = Core::getLib('cache');
        $cacheId = $cache->set('modules');
        
        if ( ! ($this->_modules = $cache->get($cacheId)))
        {
            $rows = Core::getLib('database')->select('m.module_id')
                ->from('module', 'm')
                ->join('product', 'p', 'm.product_id = p.product_id AND p.is_active = 1')
                ->where('m.is_active = 1')
                ->order('m.module_id')
                ->exec('rows');
                
            foreach ($rows as $row)
            {
                $this->_modules[$row['module_id']] = $row['module_id'];
            }
            
            $cache->save($cacheId, $this->_modules);
        }
    }
}