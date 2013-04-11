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
 * Template Plugin
 * 
 * Se encarga de la gestión de los plugins de las plantillas.
 * 
 * @package     Framework\Core\Template
 * @since       1.0.0
 */
class Core_Template_Plugin {
    
    /**
     * Guarda un arreglo de las rutas de los plugins
     */
    private static $_filePaths = array();
    
    /**
     * Obtener la ruta de un plugin.
     * 
     * @param string $type Tipo de plugin.
     * @param string $name Nombre del plugin.
     * 
     * @return string Ruta completa del plugin.
     */
    public function getPluginFilepath($type, $name)
    {
        $pluginFileName = $type . '.' . $name . '.php';
        
        if (isset(self::$_filePaths[$pluginFileName]))
        {
            return self::$_filePaths[$pluginFileName];
        }
        $return = false;
        foreach (array(TPL_PLUGIN, PLUGIN_PATH) as $pluginDir)
        {
            $pluginFilePath = $pluginDir . $pluginFileName;
            
            if (file_exists($pluginFilePath))
            {
                $return = $pluginFilePath;
                break;
            }
        }
        
        self::$_filePaths[$pluginFileName] = $return;
        return $return;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar plugins en la plantilla.
     * 
     * @param array $plugins Arreglo de plugins solicitados.
     * @param Core_Template $tpl Instancia de la clase Core_Template.
     * 
     * @return void
     */
    public function load($plugins, &$tpl)
    {
        foreach ($plugins as $plugin)
        {
            list($type, $name) = $plugin;
            
            if (isset($tpl->plugins[$type][$name]))
            {
                if ( ! function_exists($tpl->plugins[$type][$name]))
                {
                    Core_Error::trigger("[plugin] $type '{$name}' no está implementado.");
                }
                continue;
            }
            
            $pluginFile = $this->getPluginFilepath($type, $name);
            
            if ( ! $found = ($pluginFile != false))
            {
                $message = 'no se pudo cargar archivo plugin: ' . $type . '.' . $name . '.php';
            }
            
            if ($found)
            {
                include_once $pluginFile;
                
                $pluginFunc = 'tpl_' . $type . '_' . $name;
                
                if ( ! function_exists($pluginFunc))
                {
                    Core_Error::trigger('[plugin] la funcion ' . $pluginFunc . ' no se encuentra en ' . str_replace(SYS_PATH, '', $pluginFile));
                    continue;
                }
            }
            
            if ($found)
            {
                $tpl->plugins[$type][$name] = $pluginFunc;
            }
            else
            {
                Core_Error::trigger($message);
            }
        }
    }
}