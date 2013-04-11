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
 * Html Menu
 * 
 * Clase para generar los menús en nuestro sitio.
 * 
 * @package     Framework\Core\Html
 * @since       1.0.0
 */
class Core_Html_Menu {
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('html.menu.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener menú.
     * 
     * Obtiene todos los menús de los sitios personalizados y preestablecidos:
     * Main, Header, Sub y Footer.
     * 
     * @param string $section Sección de la cual cargarémos los menús.
     * 
     * @return array Arreglo con los menús de la sección.
     */
    public function getMenu($section)
    {   
        $menus = array();
        
        $section = strtolower(str_replace('/', '.', $section));
        
        $cache = Core::getLib('cache');
        $cacheId = $cache->set(array('theme', 'menu_' . str_replace(array('/', '\\'), '_', $section)));
        
        if ( ! ($menus = $cache->get($cacheId)))
        {
            // Obtenemos los menús padres
            $parts = explode('.', $section);
            $menus1 = $this->_getMenu($section);
            $cached = array();
            foreach ($menus1 as $menu1)
            {
                $cached[] = $menu1['menu_id'];
            }
            
            $menus2 = $this->_getMenu($parts[0]);
            foreach ($menus2 as $key => $menu2)
            {
                if (in_array($menu2['menu_id'], $cached))
                {
                    unset($menus2[$key]);
                }
            }
            
            $final = array_merge($menus1, $menus2);
            // Componemos los menús
            $menus = array();
            foreach ($final as $menu)
            {
                if ($menu['parent_id'] > 0)
                {
                    continue;
                }
                
                $menus[$menu['menu_id']] = $menu;
            }
            
            // Obtenemos los menús hijos
            $children = $this->_getChildren();
            
            if (count($children))
            {
                foreach ($children as $child)
                {
                    // NO existe su padre
                    if ( ! isset($menus[$child['parent_id']]))
                    {
                        continue;
                    }
                    
                    $menus[$child['parent_id']]['children'][] = $child;
                }
            }
            
            $cache->save($cacheId, $menus);
        }
        
        // Comprobar permisos, y otros parámetros antes de guardar
        
        return $menus;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener menús de la DB
     * 
     * @param string $section_id Sección del menú.
     * @param int $parent_id ID del menú padre.
     * 
     * @return array
     */
    private function _getMenu($section_id, $parent_id = 0)
    {
        return Core::getLib('database')->select('m.menu_id, m.parent_id, m.section_id, m.var_name, mo.module_id AS module, m.url_value AS url')
            ->from('menu', 'm')
            ->join('module', 'mo', 'm.module_id = mo.module_id AND mo.is_active = 1')
            ->join('product', 'p', 'mo.product_id = p.product_id AND p.is_active = 1')
            ->where('m.parent_id = ' . (int) $parent_id . ' AND m.section_id = ' . Core::getLib('database')->escape($section_id) . ' AND m.is_active = 1')
            ->order('m.ordering ASC')
            ->exec('rows');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener todos los menús hijos
     * 
     * @return array Arreglo con los menús que tienen un menú padre.
     */
    private function _getChildren()
    {
        return Core::getLib('database')->select('m.menu_id, m.parent_id, m.section_id, m.var_name, mo.module_id AS module, m.url_value AS url')
            ->from('menu', 'm')
            ->join('module', 'mo', 'm.module_id = mo.module_id AND mo.is_active = 1')
            ->join('product', 'p', 'mo.product_id = p.product_id AND p.is_active = 1')
            ->where('m.parent_id > 0 AND m.is_active = 1')
            ->order('m.ordering ASC')
            ->exec('rows');
    }
}