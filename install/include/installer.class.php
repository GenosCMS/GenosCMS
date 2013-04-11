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
class Core_Installer {
    
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
        'license',
        'requirement',
        'database',
        'configuration',
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
        
        $this->_type = ($this->_req->get('type') == 'upgrade' ? 'upgrade' : 'install');
        
        // Sesión
        if ( ! isset($_SESSION))
        {
            session_start();
        }
        
        // Verificamos que tipo de acción vamos a realizar
        if ($this->_type == 'install' && $this->_req->get('step') == '')
        {
            if (file_exists(SETTING_PATH . 'server.php'))
            {
                require SETTING_PATH . 'server.php';
                
                if (isset($_CONF['core.is_installed']) && $_CONF['core.is_installed'] == true)
                {
                    // Esto es un upgrade
                    $this->_url->forward('index.php?type=upgrade');
                }
            }
            
            // Inicializamos
            $_SESSION['steps'] = array();
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Iniciar proceso de instalación
     */
    public function run()
    {
        $step = ($this->_req->get('step') ? strtolower($this->_req->get('step')) : 'license');
        
        // Validar paso actual
        if ( ! in_array($step, $this->_steps))
        {
            exit('Paso inv&aacute;lido');
        }
        
        $method = '_' . $step;
        
        $stepKey = 0;
        foreach ($this->_steps as $key => $myStep)
        {
            if ($myStep === $step)
            {
                $stepKey = ($key - 1);
                break;
            }
        }
        
        if (isset($this->_steps[$stepKey]) && !$this->_isPassed($this->_steps[$stepKey]))
        {
            $this->_url->forward($this->_step($this->_steps[$stepKey]));
        }
        
        $this->_step = $step;
        
        // Mandamos a llamar al paso seleccionado
        if (method_exists($this, $method))
        {
            call_user_func(array(&$this, $method));
        }
        else
        {
            $step = 'key';
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
                'install.js' => 'style_script'
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
    private function _key(){}
    
    // --------------------------------------------------------------------
    
    /**
     * Validar acuerdo de licencia
     * 
     * @access private
     * @return void
     */
    private function _license()
    {
        if ($this->_req->get('agree'))
        {
            $this->_pass('requirement');
        }
        
        $this->_tpl->title('Acuerdo de licencia');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Comprobar requisitos
     * 
     * @access private
     * @return void
     */
    private function _requirement()
    {
        $isPassed = true;
        
        // Verificar funciones PHP
        $verify = array(
			'php_version' => (version_compare(phpversion(), '5', '<') !== true ? true : false),
			'php_xml_support' => (function_exists('xml_set_element_handler') ? true : false),
			'php_gd' => ((extension_loaded('gd') && function_exists('gd_info')) ? true : false)
        );
        
        foreach ($verify as $check => $passed)
        {
            if ($passed === false)
            {
                $isPassed = false;
                break;
            }
        }
        
        // Verificamos Base de datos
        $drivers = Core::getLib('database.support')->getSupported();
        $dbChecks = array();
        $dbCnt = 0;
        foreach ($drivers as $driver)
        {
            $dbChecks[$driver['label']] = $driver['available'];
            
            // Almenos debemos tener soporte para un motor.
            if ($driver['available'])
            {
                $dbCnt++;
            }
        }
        
        if (empty($dbCnt))
        {
            $isPassed = false;
        }
        
        // Verificar archivos y carpetas
        $fileChecks = array();
        $moduleList = Core::getLib('module')->getModuleFiles();
        $modules = array_merge($moduleList['core'], $moduleList['external']);
        
        $noLoadFail = false;
        $cached = array();
        foreach ($modules as $module)
        {
            $module = $module['name'];
            
            if ( ! file_exists(MOD_PATH . $module . DS . MOD_INC . 'module.php'))
            {
                continue;
            }
            
            $lines = file(MOD_PATH . $module . DS . MOD_INC . 'module.php');
            foreach ($lines as $line)
            {
                $line = trim($line);
                if (substr($line, 0, 5) == 'class')
                {
                    $line = str_replace('class Module_', '', $line);
                    
                    if (isset($cached[$line]))
                    {
                        Core_Error::set('Se encontraron módulos con clases repetidas:<br>' . MOD_PATH . $module . DS . MOD_INC . 'module.php' . '<br> coincide con: <br>' . $cached[$line]);
                        $isPassed = false;
                        $noLoadFail = true;
                        break;
                    }
                    
                    $cached[$line] = MOD_PATH . $module . DS . MOD_INC . 'module.php';
                }
            }
        }
        
        if ( ! $noLoadFail)
        {
            $files = '';
            foreach ($modules as $module)
            {
                if ( ($moduleFiles = Core::getLib('module')->init($module['name'], 'fileWritable')))
                {
                    $files .= implode(',', $moduleFiles) . ',';
                }
            }

            $files = rtrim($files, ',');
            $files = explode(',', $files);
            sort($files);
            
            foreach($files as $key => $file)
            {
                $file = str_replace('/', DS, $file);
                
                if ( file_exists(ROOT . DS . $file) && Core::getLib('file')->isWritable(ROOT . DS . $file))
                {
                    $fileChecks[$file] = true;
                    continue;
                }
                
                if ( ! file_exists(ROOT . DS . $file))
                {
                    $fileChecks[$file] = false;
                    $isPassed = false;
                    continue;
                }
                
                if ( ! Core::getLib('file')->isWritable(ROOT . DS . $file))
                {
                    $fileChecks[$file] = false;
                    $isPassed = false;
                }
            }
            
        }
        
        // Verificar si pasamos los requisitos
        if ($this->_req->get('passed') && $isPassed)
        {
            $this->_pass('database');
        }
        
        // Mostrar plantilla
        $checks = array(
            'php' => array(
                'title' => 'PHP & Funciones',
                'passed' => '<span class="label label-success">Correcto</span>',
                'failed' => '<span class="label label-important" data-toggle="tooltip" title="Su servidor no soporta esta función">Error</span>',
                'checks' => array(
					'PHP 5' => $verify['php_version'],
					'PHP XML Support' => $verify['php_xml_support'],
					'PHP GD Support' => $verify['php_gd']
                )
            ),
            'database' => array(
                'title' => 'Base de datos',
                'passed' => '<span class="label label-success">Disponible</span>',
                'failed' => '<span class="label label-important">No disponible</span>',
                'checks' => $dbChecks
            ),
            'file' => array(
                'title' => 'Archivos y directorios',
                'passed' => '<span class="label label-success">Correcto</span>',
                'failed' => '<span class="label label-important" data-toggle="tooltip" title="Verificar permisos de escritura CHMOD 0777">Corregir</span>',
                'rename' => '<span class="label label-important" data-toggle="tooltip" title="Renombrar \'include\setting\server.php.new\' a \'include\setting\server.php\'">Renombrar</span>',
                'checks' => $fileChecks
            )
        );
        
        $this->_tpl->title('Comprobar requisitos')
            ->assign(array(
                'checks' => $checks,
                'isPassed' => $isPassed
            )
        );
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Configurar base de datos
     * 
     * @access private
     * @return void
     */
    private function _database()
    {
        // Motores de DB
        $_drivers = Core::getLib('database.support')->getSupported();
        $drivers = array();
        foreach ($_drivers as $driver)
        {
            $drivers[$driver['module']] = $driver['label'];
        }
        
        // Prefijo aleatorio
        $chars = array_merge(range('a', 'z'), range(0, 9));
        $prefix = '';
        for ($i = 0; $i < 3; $i++)
        {
            $prefix .= $chars[mt_rand(0, count($chars) - 1)];
        }
        $prefix .= '_';
        
        
        // Mostramos
        $this->_tpl->title('Base de datos')
            ->assign(array(
                'drivers' => $drivers,
                'prefix' => $prefix,
            )
        );
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Verificar que ya pasámos por un paso
     * 
     * @access private
     * @param string $step
     * @return bool
     */
    private function _isPassed($step)
    {
        return (isset($_SESSION['steps'][$step]) ? true : false);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar paso
     * 
     * @access private
     * @param string $forward
     * @return void
     */
    private function _pass($forward = null)
    {
        $_SESSION['steps'][$this->_step] = true;
        
        if ($forward !== false)
        {
            $this->_url->forward($this->_step($forward));
        }
        
        return true;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Componer URL
     * 
     * @access private
     * @param string $step
     * @return string
     */
    private function _step($step)
    {
        return 'index.php?type=' . $this->_type . '&step=' . $step;
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
        $cnt = 0;
        
        foreach ($this->_steps as $step)
        {
            $stepName = $step;
            
            switch($step)
            {
                case 'key':
                    $stepName = 'Verificación';
                break;
                case 'license':
                    $stepName = 'Acuerdo de licencia';
                break;
                case 'requirement':
                    $stepName = 'Comprobar requisitos';
                break;
                case 'database':
                    $stepName = 'Base de datos';
                break;
                case 'configuration':
                    $stepName = 'Configuración';
                break;
                case 'process':
                    $stepName = 'Procesar';
                break;
            }
            
            $cnt++;
            $steps[] = array(
                'name' => $stepName,
                'is_active' => ($this->_step == $step ? true : false),
                'count' => $cnt,
            );
        }
        
        return $steps;
    }
}