<?php
/**
 * Controlador por defecto
 */
class Admin_Component_Controller_Index extends Core_Component {
    
    /**
     * Nombre del módulo a cargar
     * 
     * @var string
     */
    private $_module = '';
    
    /**
     * Nombre del controlador a cargar
     */
    private $_controller = 'index';

    /**
     * Este es el método principal
     */
    public function main()
    {
        // Verificar si accedió desde el path configurado. 
        if (Core::getParam('admin.admin_folder') != $this->url->segment(1))
        {
            return Core_Error::show404();
        }
        
        // TODO: Validar si tiene permisos para acceder
        
        // Asignar el módulo a cargar (Verificar posible error al cambiar admin.admin_folder)
        $this->_module = (($module = $this->url->segment(2)) ? strtolower($module) : Core::getParam('admin.admin_folder'));
        if ($this->_module == 'logout')
        {
            $this->_controller = $this->_module;
            $this->_module = 'admin';
        }
        else
        {
            $this->_controller = (($controller = $this->url->segment(3)) ? $controller : $this->_controller);
        }
        
        // Cargamos posibles sub controladores
        $subController1 = $this->url->segment(4);
        $subController2 = $this->url->segment(5);
        
        // Validar controlador solicitado
        $pass = false;
        if (file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . 'admin' . DS . $this->_controller . '.controller.php'))
        {
            $this->_controller = 'admin.' . $this->_controller;
            $pass = true;
        }
        // Hasta 3 subcontroladores
        else if ( ! $pass && $subController2 && file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . 'admin' . DS . $this->_controller . DS . $subController1 . DS . $subController2 . '.controller.php'))
        {
            $this->_controller = 'admin.' . $this->_controller . '.' . $subController1 . '.' . $subController2;
        }
        // Hasta 2 subcontroladores
        else if ( ! $pass && $subController1 && file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . 'admin' . DS . $this->_controller . DS . $subController1 . '.controller.php'))
        {
            $this->_controller = 'admin.' . $this->_controller . '.' . $subController1;
        }
        // Subcontrolador con mismo nombre que el controlador
        else if ( ! $pass && file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . 'admin' . DS . $this->_controller . DS . $this->_controller . '.controller.php'))
        {
            $this->_controller = 'admin.' . $this->_controller . '.' . $this->_controller;
        }
        // Sub controlador es una carpeta y existe un index
        else if ( ! $pass && $subController1 && file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . 'admin' . DS . $this->_controller . DS . $subController1 . DS . 'index.controller.php'))
        {
            $this->_controller = 'admin.' . $this->_controller . '.' . $subController1 . '.index';
        }
        // Controlador con index?
        else if ( ! $pass && file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . 'admin' . DS . $this->_controller . DS . 'index.controller.php'))
        {
            $this->_controller = 'admin.' . $this->_controller . '.index';
        }
        // Controlador del módulo admin
        else if ( ! $pass && file_exists(MOD_PATH . 'admin' . DS . MOD_COMPONENT . DS . 'controller' . DS . $this->_module . DS . $this->_controller . '.controller.php'))
        {
            $this->_controller = $this->_module . '.' . $this->_controller;
            $this->_module = 'admin';
        }
        else if ( ! $pass && $subController1 && file_exists(MOD_PATH . 'admin' . DS . MOD_COMPONENT . DS . 'controller' . DS . $this->_module . DS . $this->_controller . DS . $subController1 . '.controller.php'))
        {
            $this->_controller = $this->_module . '.' . $this->_controller . '.' . $subController1;
            $this->_module = 'admin';
        }
        // Por si modifican la carpeta del módulo admin
        else if ( ! $pass && Core::getParam('admin.admin_folder') != 'admin' && file_exists(MOD_PATH . $this->_module . DS . MOD_COMPONENT . DS . 'controller' . DS . $this->_controller . '.controller.php'))
        {
            $pass = true;
        }
        
        // Obtener los menús para el panel de administración
        $modules = Core::getService('admin.module')->getAdminMenu();
        
        // Encontramos el controlador
        if ($pass)
        {
            Core::getLib('module')->setController($this->_module . '.' . $this->_controller);
        }
        else
        {
            if ($this->_module != Core::getParam('admin.admin_folder'))
            {
                return Core_Error::show404();
            }
            else
            {
                // Dashboard
            }
        }
    }   
}