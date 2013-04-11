<?php
/**
* Polaris Framework
*
* HMVC Framework
*
* @package     Polaris
* @author      Ivan Molina Pavana <montemolina@live.com>
* @copyright   Copyright (c) 2013
* @version     1.0
*/

// ------------------------------------------------------------------------

/**
 * Installer
 * 
 * Se encarga de la instalación del framework
 * 
 * @package     Polaris
 * @subpackage  Core
 * @category    Library
 * @author      Ivan Molina Pavana <montemolina@live.com>
 */
class Core_Loader {
    
    /**
     * Objeto Template
     * 
     * @var object
     */
    private $_tpl = null;
    
    /**
     * Objecto Request
     * 
     * @var object
     */
    private $_req = null;
    
    /**
     * Objeto URL
     * 
     * @var object
     */
    private $_url = null;
    
    /**
     * Tipo de instalación: Upgrade/Install
     * 
     * @var string
     */
    private $_type = '';
    
    /**
     * Pasos de instalación
     * 
     * @var array
     */
    private $_steps = array(
        'review',
        'module',
        'configuration',
        'phrase',
        'menu',
        'rewrite'
    );
    
    /**
     * Paso actual
     * 
     * @var string
     */
    private $_step = '';
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->_tpl = Core::getLib('template');
        $this->_req = Core::getLib('request');
        $this->_url = Core::getLib('url');
        
        $this->_type = ($this->_req->get('type') == 'upgrade' ? 'upgrade' : 'loader');
        
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Iniciar proceso de instalación
     */
    public function run()
    {
        $step = ($this->_req->get('action') ? strtolower($this->_req->get('action')) : 'review');
        
        // Validar paso actual
        if ( ! in_array($step, $this->_steps))
        {
            exit('Paso inv&aacute;lido');
        }
        
        $method = '_' . $step;
        
/**
 *         $stepKey = 0;
 *         foreach ($this->_steps as $key => $myStep)
 *         {
 *             if ($myStep === $step)
 *             {
 *                 $stepKey = ($key - 1);
 *                 break;
 *             }
 *         }
 *         
 *         if (isset($this->_steps[$stepKey]) && !$this->_isPassed($this->_steps[$stepKey]))
 *         {
 *             $this->_url->forward($this->_step($this->_steps[$stepKey]));
 *         }
 */
        
        $this->_step = $step;
        
        // Mandamos a llamar al paso seleccionado
        if (method_exists($this, $method))
        {
            call_user_func(array(&$this, $method));
        }
        else
        {
            $step = 'review';
        }
        
        if ( ! file_exists($this->_tpl->getLayoutFile($step)))
        {
            $step = 'default';
        }
        
        $this->_tpl->css(array(
                    'bootstrap.css' => 'static_css',
                    'bootstrap-responsive.css' => 'static_css',
                    'template.css' => 'style_css',
                    'chosen.css' => 'static_css'
                )
            )
            ->js(array(
                'jquery.min.js' => 'static_script',
                'bootstrap.min.js' => 'static_script',
                'jquery.chosen.min.js' => 'static_script',
                'loader.js' => 'style_script'
            ))
            ->assign(array(
                'template' => $step,
                'type' => $this->_type,
                'next' => 'next',
                'steps' => $this->_getSteps(),    
            )
        );
        
        $this->_tpl->getLayout('template');
    }
    
    // --------------------------------------------------------------------
    // Pasos para la instalación
    // --------------------------------------------------------------------
    
    /**
     * Validar llave de registro
     * 
     * @access private
     * @return void
     */
    private function _review(){}
    
    // --------------------------------------------------------------------
    
    /**
     * Validar acuerdo de licencia
     * 
     * @access private
     * @return void
     */
    private function _module()
    {   
        $isAdded = false;
        if (($module = $this->_req->get('module')))
        {
            $exists = Core::getLib('database')->select('COUNT(*)')->from('module')->where('module_id = ' . Core::getLib('database')->escape($module['module_id']))->exec('field');
            
            if ( ! $exists)
            {
                Core::getLib('database')->insert('module', $module);
                
                Core::getLib('cache')->remove('modules');
                
                $isAdded = true;
            } 
        }
        
        $modules = Core::getLib('database')->select('*')->from('module')->exec('rows');
        
        $this->_tpl->title('Módulos')
            ->assign(array(
            'modules' => $modules,
            'isAdded' => $isAdded
        ));
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Configuración
     * 
     * @access private
     * @return void
     */
    private function _configuration()
    {
        $isAdded = false;
        $db = Core::getLib('database');
        
        if ( ($setting = $this->_req->get('setting')))
        {
            $exists = $db->select('COUNT(*)')->from('setting')->where('module_id = ' . $db->escape($setting['module_id']) . ' AND var_name = ' . $db->escape($setting['var_name']))->exec('field');
            
            if ( ! $exists)
            {
                $setting['value_default'] = $setting['value_actual'];
                
                $db->insert('setting', $setting);
                
                Core::getLib('cache')->remove('setting');
                
                $isAdded = true;
            }
        }
        
        if ( ($phrase = $this->_req->get('phrase')))
        {
            $exists = $db->select('COUNT(*)')->from('language_phrase')->where('module_id = ' . $db->escape($phrase['module_id']) . ' AND var_name = ' . $db->escape($phrase['var_name']))->exec('field');
            
            if ( ! $exists)
            {
                $phrase['text_default'] = $phrase['text_actual'];
                
                $db->insert('language_phrase', $phrase);
                
                Core::getLib('cache')->remove(array('language', $phrase['language_id'] . '_' . $phrase['module_id']));
                
                $isAdded = true;
            }
        }
        
        if ( ($group = $this->_req->get('group')))
        {
            $exists = $db->select('COUNT(*)')->from('setting_group')->where('module_id = ' . $db->escape($group['module_id']) . ' AND group_id = ' . $db->escape($group['group_id']))->exec('field');
            
            if ( ! $exists)
            {
                $db->insert('setting_group', $group);
                
                $isAdded = true;
            }
        }
        
        // Módulos
        $rows = $db->select('module_id')->from('module')->exec('rows');
        $modules = array();
        foreach ($rows as $row)
        {
            $modules[$row['module_id']] = $row['module_id']; 
        }
        
        // Grupos
        $rows = $db->select('group_id')->from('setting_group')->exec('rows');
        $groups = array();
        foreach ($rows as $row)
        {
            $groups[$row['group_id']] = $row['group_id'];
        }
        
        $this->_tpl->title('Configuración')
            ->assign(array(
                'modules' => $modules,
                'groups' => $groups,
                'isAdded' => $isAdded
            )
        );
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Phrases
     * 
     * @access private
     * @return void
     */
    private function _phrase()
    {
        $isAdded = false;
        $db = Core::getLib('database');
        
        if ( ($phrase = $this->_req->get('phrase')))
        {
            $exists = $db->select('COUNT(*)')->from('language_phrase')->where('module_id = ' . $db->escape($phrase['module_id']) . ' AND var_name = ' . $db->escape($phrase['var_name']))->exec('field');
            
            if ( ! $exists)
            {
                $phrase['text_default'] = $phrase['text_actual'];
                
                $db->insert('language_phrase', $phrase);
                
                Core::getLib('cache')->remove(array('language', $phrase['language_id'] . '_' . $phrase['module_id']));
                
                $isAdded = true;
            }
        }
        
        // Módulos
        $rows = $db->select('module_id')->from('module')->exec('rows');
        $modules = array();
        foreach ($rows as $row)
        {
            $modules[$row['module_id']] = $row['module_id']; 
        }
        
        $this->_tpl->title('Frases')
            ->assign(array(
                'modules' => $modules,
                'isAdded' => $isAdded
            )
        );
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar menús
     * 
     * @access private
     * @return void
     */
    private function _menu()
    {
        $isAdded = false;
        $db = Core::getLib('database');
        
        // Menú
        if ( ($menu = $this->_req->get('menu')))
        {
            $exists = $db->select('COUNT(*)')->from('menu')->where('module_id = ' . $db->escape($menu['module_id']) . ' AND var_name = ' . $db->escape($menu['var_name']))->exec('field');
            
            if ( ! $exists)
            {
                $isSection = (int) $menu['section_id'];
                $isSection = ($isSection > 0 ? true : false);
                
                $menu['parent_id'] = ($isSection ? $menu['section_id'] : 0);
                $menu['section_id'] = ($isSection ? null : $menu['section_id']);
                
                $db->insert('menu', $menu);
                
                Core::getLib('cache')->remove(array('theme', 'menu_' . ($menu['section_id'] == null ? 'main' : $menu['section_id'])));
                
                $isAdded = true;
            }
        }
        
        // Frase
        if ( ($phrase = $this->_req->get('phrase')))
        {
            $exists = $db->select('COUNT(*)')->from('language_phrase')->where('module_id = ' . $db->escape($phrase['module_id']) . ' AND var_name = ' . $db->escape($phrase['var_name']))->exec('field');
            
            if ( ! $exists)
            {
                $phrase['text_default'] = $phrase['text_actual'];
                
                $db->insert('language_phrase', $phrase);
                
                Core::getLib('cache')->remove(array('language', $phrase['language_id'] . '_' . $phrase['module_id']));
                
                $isAdded = true;
            }
        }
        
        // Módulos
        $rows = $db->select('module_id')->from('module')->exec('rows');
        $modules = array();
        foreach ($rows as $row)
        {
            $modules[$row['module_id']] = $row['module_id']; 
        }
        
        // Menús
        $menus = array();
        // Bloques
        $menus['block'] = array('main', 'main_right', 'footer');
        // Parents
        $rows = $db->select('menu_id, var_name')->from('menu')->where('parent_id = 0')->exec('rows');        
        foreach ($rows as $row)
        {
            $menus['parent'][] = $row;
        }
        
        $this->_tpl->title('Menús')
            ->assign(array(
                'modules' => $modules,
                'menus' => $menus,
                'isAdded' => $isAdded
            )
        );
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Rutas
     * 
     * @access private
     * @return void
     */
    private function _rewrite()
    {
        $isAdded = false;
        $db = Core::getLib('database');
        
        if ( ($route = $this->_req->get('route')))
        {
            // Validamos
            $route['url'] = ($route['url'] != '') ? ($route['module_id'] . '/' . trim($route['url'], '/')) : $route['module_id'];
            $route['replacement'] = ($route['replacement'] != '') ? ($route['module_id'] . '/' . trim($route['replacement'], '/')) : 'index';
            
            $exists = $db->select('COUNT(*)')->from('rewrite')->where('module_id = ' . $db->escape($route['module_id']) . ' AND url = ' . $db->escape($route['url']) . ' AND replacement = ' . $db->escape($route['replacement']))->exec('field');
            
            if ( ! $exists)
            {
                $db->insert('rewrite', $route);
                
                Core::getLib('cache')->remove('rewrite');
                
                $isAdded = true;
            }
        }
        
        // Módulos
        $rows = $db->select('module_id')->from('module')->exec('rows');
        $modules = array();
        foreach ($rows as $row)
        {
            $modules[$row['module_id']] = $row['module_id']; 
        }
        
        $this->_tpl->title('Rutas')
            ->assign(array(
                'modules' => $modules,
                'isAdded' => $isAdded
            )
        );
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener los pasos
     * 
     * @access private
     * @return array
     */
    private function _getSteps()
    {
        $steps = array();
        
        foreach ($this->_steps as $step)
        {
            $stepName = $step;
            $icon = '';
            switch($step)
            {
                case 'review':
                    $stepName = 'Resumen';
                    $icon = 'home';
                break;
                case 'module':
                    $stepName = 'Módulos';
                    $icon = 'inbox';
                break;
                case 'configuration':
                    $stepName = 'Configuración';
                    $icon = 'cog';
                break;
                case 'phrase':
                    $stepName = 'Frases';
                    $icon = 'globe';
                break;
                case 'menu':
                    $stepName = 'Menús';
                    $icon = 'list';
                break;
                case 'rewrite':
                    $stepName = 'Rutas';
                    $icon = 'random';
                break;
            }
            
            $steps[] = array(
                'step' => $step,
                'name' => $stepName,
                'is_active' => ($this->_step == $step ? true : false),
                'icon' => $icon,
            );
        }
        
        return $steps;
    }
}