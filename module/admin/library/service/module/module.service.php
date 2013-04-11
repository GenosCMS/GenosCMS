<?php

/**
 * @author Neutron
 * @copyright 2013
 */

class Admin_Service_Module extends Core_Service {
    
    /**
     * Obtener menús de los módulos para la admin
     * 
     * @access public
     * @return array
     */
    public function getAdminMenu()
    {
        $cache = Core::getLib('cache');
        $cId = $cache->set('module_admin_menu');
        
        if ( ! ($data = $cache->get($cId)))
        {
            // Generar menus
            
            // Guardar en caché
            $cache->save($cId, array(1, 2, 3, 4));
        }
        
        //var_dump($data);
        
        //exit;
    }
}