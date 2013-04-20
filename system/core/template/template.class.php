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
 * Template
 * 
 * Esta clase se encarga de la gestión de plantillas.
 * 
 * También nos permite agregar información y archivos a la plantilla principal,
 * desde un componente.
 * 
 * @package     Framework\Core\Template
 * @since       1.0.0
 */
class Core_Template {
    
    /**
     * Nombre de la plantilla por default.
     * 
     * @var string
     */
    public $displayLayout = 'template';
    
    /**
     * Plugins cargados
     * 
     * @var array
     */
    public $plugins = array(
        'compiler' => array(),
        'function' => array(),
        'modifier' => array(),
    );
    
    /**
     * Es una plantilla del panel de administración?
     * 
     * @var bool
     */
    private $_isAdmin = false;
    
    /**
     * Carpeta del theme del layout usado.
     * 
     * @var string
     */
    private $_themeLayout = '';
    
    /**
     * Carpeta del tema que es usado.
     * 
     * @var string
     */
    private $_themeFolder = '';
    
    /**
     * Lista de variables reservadas
     * 
     * @var array
     */
    private $_tplVars = array();
    
    /**
     * Lista de variables asignadas a las plantillas.
     * 
     * @var array
     */
    private $_vars = array();
    
    /**
     * Código JavaScript
     * 
     * @var array
     */
    private $_script = array(
        'ready' => '',
        'code' => ''
    );
    
    /**
     * Título de la página.
     * 
     * @var string
     */
    private $_title = array();
    
    /**
     * Meta tags
     * 
     * @var array
     */
    private $_meta = array();
    
    /**
     * CSS
     * 
     * @var array
     */
    private $_css = array();
    
    /**
     * JS Files
     * 
     * @var array
     */
    private $_js = array();
    
    /**
     * Constructor, inicializa las variables
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('template.init') : null;
        
        if ( defined('CORE_INSTALLER'))
        {
            $this->_themeLayout = 'install';
            $this->_themeFolder = 'loader';
        }
        else
        {
            // Estámos en el panel de administración?
            $this->_isAdmin = (Core::getLib('url')->segment(1) == Core::getParam('admin.admin_folder'));
            
            if ($this->_isAdmin)
            {
                $this->_themeLayout = 'admin';
                $this->_themeFolder = Core::getParam('core.default_theme');
            }
            else
            {
                // TODO: Buscar el theme por defecto
                $this->_themeLayout = 'public';
                $this->_themeFolder = Core::getParam('core.default_theme');
            }
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Plantilla principal que será cargada.
     * 
     * @param string $layout
     * 
     * @return Core_Template
     */
    public function setTemplate($layout)
    {
        $this->displayLayout = $layout;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar una variable a la plantilla.
     * 
     * @param array|string $var Arreglo de variables que serán asignadas ó nombre de la variable. 
     * @param string $val Opcional, valor de la variable.
     * 
     * @return Core_Template
     */
    public function assign($var, $val = '')
    {
        if ( ! is_array($var))
        {
            $var = array($var => $val);
        }
        
        foreach ($var as $key => $val)
        {
            $this->_vars[$key] = $val;
        }
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Borrar de memoria todo o una variable específica.
     * 
     * @param array|string $name Nombre o arreglos de nombres de variables.
     * 
     * @return void
     */
    public function clean($name = '')
    {
        if ($name)
        {
            if ( ! is_array($name))
            {
                $name = array($name);
            }
            
            foreach ($name as $var)
            {
                unset($this->_vars[$var]);
            }
            
            return;
        }
        
        unset($this->_vars);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar título a la página.
     * 
     * @param string $title Título que será agregado.
     * 
     * @return Core_Template
     */
    public function title($title)
    {
        // Podemos usar frases.
        if (strpos($title, '.') !== false)
        {
            $title = Core::getPhrase($title);
        }
        
        $this->_title[] = $title;
        
        $this->meta('og:site_name', Core::getParam('core.site_title'));
        $this->meta('og:title', $title);
        
        return $this; 
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Metadatos.
     * 
     * @param array|string $meta Nombre del meta dato ó arreglo de metadatos.
     * @param string $value Valor del meta dato.
     * 
     * @return Core_Template
     */
    public function meta($meta, $value = null)
    {
        if ( ! is_array($meta))
        {
            $meta = array($meta => $value);
        }
        
        foreach ($meta as $key => $value)
        {
            if ($key == 'description')
            {
                $this->_meta['og:description'] = $value;
            }
            
            if ( isset($this->_meta[$key]))
            {
                $this->_meta[$key] .= ($key == 'keywords' ? ', ' : ' ') . $value;
            }
            else
            {
                $this->_meta[$key] = $value;
            }
        }
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar archivos CSS.
     * 
     * <code>
     * $this->template->css('jquery.ui.css', 'static_css');
     * $this->template->css(array('jquery.ui.css' => 'static_css'));
     * </code>
     * 
     * @param array|string $file Archivo CSS que será agregado.
     * @param string $type Tipo de archivo.
     * 
     * @return Core_Template
     */
    public function css($file, $type = '')
    {
        if ( ! is_array($file))
        {
            $file = array($file => $type);
        }
        
        $this->_css[] = $file;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar archivos JS.
     * 
     * <code>
     * $this->template->js('jquery.ui.min.js', 'static_script');
     * $this->template->js(array('jquery.ui.min.js' => 'static_script'));
     * </code>
     * 
     * @param array $file Archivo JS que será agregado.
     * @param string $type Tipo de archivo.
     * 
     * @return Core_Template
     */
    public function js($file = array(), $type = '')
    {
        if ( ! is_array($file))
        {
            $file = array($file => $type);
        }
        
        $this->_js[] = $file;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar etiqueta de imagen con URL del tema usado.
     * 
     * @param array $src Archivo de la imagen.
     * @param bool $static TRUE si es un archivo estático.
     * @param string $attrs Atributos de la imagen.
     * 
     * @return string
     */
    public function getImage($src, $static = false, $attrs = '')
    {
        $imagePath = (($static !== false) ? Core::getParam('core.url_static_img') . $src : $this->_getStyleUrl('img', $src));
        
        return '<img src="' . $imagePath . '" ' . $attrs . '/>';
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear título del sitio.
     * 
     * @return string
     */
    private function _getTitle()
    {
        $titles = '';        
        foreach ($this->_title as $title)
        {
            $titles .= $title . ' ' . Core::getParam('core.title_delim') . ' ';
        }
                
        $titles .= Core::getParam('core.site_title');
                
        return $titles;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar meta tags.
     * 
     * @todo Limpiar valores de las meta.
     * 
     * @return string
     */
    private function _getMeta()
    {
        $metas = '';
        foreach ($this->_meta as $name => $value)
        {
            $metas .= "\n\t" . '<meta property="' . $name . '" content="' . $value . '" />';
        }
        
        return $metas;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar el código de los archivos CSS.
     * 
     * @return string
     */
    private function _getStyles()
    {
        $styles = '';
        foreach ($this->_css as $css)
        {
            $styles.= $this->_getAssetUrl($css);
        }
        
        return $styles;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar el código de los archivos JS.
     * 
     * @return string
     */
    private function _getScripts()
    {
        // Archivos JS
        $scripts = '';
        foreach ($this->_js as $js)
        {
            $scripts.= $this->_getAssetUrl($js);
        }

        return $scripts;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar URL de un Asset.
     * 
     * @param array $asset Arreglo con los assets (CSS/JS)
     * 
     * @return string
     */
    private function _getAssetUrl($asset)
    {
        $return = '';
        foreach ($asset as $file => $type)
        {
            switch($type)
            {
                // Archivos JS del tema
                case 'style_script':
                    $return .= "\n\t" . '<script src="'. $this->_getStyleUrl('js', $file) .'" type="text/javascript"></script>';
                    break;
                // Archivos CSS del tema
                case 'style_css':
                    $return .= "\n\t" . '<link href="'. $this->_getStyleUrl('css', $file) . '" rel="stylesheet">';
                    break;
                // Archivos estáticos
                case 'static_script':
                    $return .= "\n\t" . '<script src="'. Core::getParam('core.url_static_script') . $file .'" type="text/javascript"></script>';
                    break;
                case 'static_css':
                    $return .= "\n\t" . '<link href="'. Core::getParam('core.url_static_style') . $file . '" rel="stylesheet">';
                    break;
                // Archivos JS & CSS de un módulo
                default:
                    if (preg_match('/module/i', $type))
                    {
                        $parts = explode('_', $type);
                        if (isset($parts[1]))
                        {
                            if (substr($file, -3) == '.js')
                            {
                                $return .= "\n\t" . '<script src="'. Core::getParam('core.path') . 'module/' . $parts[1] . '/static/js/' . $file .'" type="text/javascript"></script>';
                            }
                            else if(substr($file, -4) == '.css')
                            {
                                $return .= "\n\t" . '<link href="'. $this->_getStyleUrl('css', $file, $parts[1]) . '" rel="stylesheet">';
                            }
                        }
                    }
                    break;
            }
        }
        
        return $return;
    }
    
    // --------------------------------------------------------------------

    /**
     * Generar la URL de un archivo localizado en un tema.
     *
     * @param string $type Tipo de archivo.
     * @param string $file Nombre del archivo.
     * @param string $module Módulo de los archivos
     *
     * @return string
     */
    private function _getStyleUrl($type, $file = null, $module = null)
    {
        if ($module !== null)
        {
            $url = Core::getParam('core.path') . 'module/' . $module . '/static/' . $type . '/';
            $dir = MOD_PATH . $module . DS . 'static' . DS . $type . DS;
            
            if (file_exists($dir . $this->_themeFolder . DS . $file))
            {
                $url .= $this->_themeFolder . '/' . $file;
            }
            else
            {
                $url .= $file;
            }
            
            return $url;
        }
        
        $url = Core::getParam('core.path') . 'theme/' . ($this->_themeLayout ? $this->_themeLayout . '/' : '') . $this->_themeFolder . '/'  . $type . '/';
        
        if ($file !== null)
        {
            if ( file_exists(TPL_PATH . ($this->_themeLayout ? $this->_themeLayout . DS : '') . $this->_themeFolder . DS . $type . DS . $file))
            {
                $url .= $file;
            }
            else
            {
                $url = Core::getParam('core.path') . 'theme/' . ($this->_themeLayout ? $this->_themeLayout . '/' : '') . 'default/' . $type . '/' . $file; 
            }
        }
        
        return $url; 
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener la ruta del archivo header.php del tema si este existe.
     * 
     * @return string|bool Ruta del archivo header.php | FALSE si no se encuentra.
     */
    public function getHeaderFile()
    {
        $file = $this->_getStyleUrl('inc', 'header.php');
        $file = str_replace(Core::getParam('core.path'), ROOT . DS, $file);
        
        if (file_exists($file))
        {
            return $file;
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar layout.
     * 
     * @param string $name Nombre del layout. 
     * @param bool $return TRUE para devolver el contenido FALSE para mostrarlo.
     * 
     * @return void|string Muestra el contenido. | Retorna el contenido.
     */
    public function getLayout($name, $return = false)
    {
        $this->_getFromCache($this->getLayoutFile($name));

        if ($return)
        {
            return $this->_returnLayout();   
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener la ruta completa del layot.
     * 
     * @param string $name Nombre del layout.
     * 
     * @return string Ruta completa.
     */
    public function getLayoutFile($name)
    {
        $file = TPL_PATH . ($this->_themeLayout != '' ? $this->_themeLayout . DS : '') . $this->_themeFolder . DS . 'html' . DS . $name . TPL_SUFFIX;
        
        if ( ! file_exists($file))
        {
            $file = TPL_PATH . ($this->_themeLayout != '' ? $this->_themeLayout . DS : '') . 'default' . DS . 'html' . DS . $name . TPL_SUFFIX;
        }
        
        return $file;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Mostrar la plantilla de un componente.
     * 
     * @param string $template Nombre de la plantilla.
     * @param bool $return TRUE para devolver el contenido FALSE para mostrarlo.
     * 
     * @return void|bool Muestra el contenido. | Retorna el contenido.
     */
    public function getTemplate($template, $return = false)
    {
        $file = $this->getTemplateFile($template);
        
        $this->_getFromCache($file);
        
        if ($return)
        {
            return $this->_returnLayout();   
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener la ruta de un archivo de plantilla.
     * 
     * @param string $template Nombre de la plantilla.
     * 
     * @return string Ruta completa de la plantilla.
     */
    public function getTemplateFile($template)
    {
        $parts = explode('.', $template);
        $module = $parts[0];
        
        unset($parts[0]);
        
        $name = implode(DS, $parts);
        // Buscamos la plantilla.
        if (file_exists(MOD_PATH . $module . DS . MOD_TPL . $this->_themeFolder . DS . $name . TPL_SUFFIX))
        {
            $file = MOD_PATH . $module . DS . MOD_TPL . $this->_themeFolder . DS . $name . TPL_SUFFIX;
        }
        else if (isset($parts[2]) && file_exists(MOD_PATH . $module . DS . MOD_TPL . $this->_themeFolder . DS . $name . DS . $parts[2] . TPL_SUFFIX))
        {
            $file = MOD_PATH . $module . DS . MOD_TPL . $this->_themeFolder . DS . $name . DS . $parts[2] . TPL_SUFFIX;
        }
        else if (file_exists(MOD_PATH . $module . DS . MOD_TPL . 'default' . DS . $name . TPL_SUFFIX))
        {
            $file = MOD_PATH . $module . DS . MOD_TPL . 'default' . DS . $name . TPL_SUFFIX;
        }
        else
        {
            Core_Error::trigger('No se puede cargar la plantilla del módulo: ' . $module . '->' . $name);
        }
        
        return $file;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Retornar contenido de la plantilla.
     * 
     * @return string Obtiene el contenido del buffer y lo regresa.
     */
	private function _returnLayout()
	{
		$content = ob_get_contents();
		
		ob_clean();
		
		return $content;		
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Obtiene un archivo de plantilla del caché.
     * 
     * Si no existe entonces se ejecuta el parser para crear el archivo en caché.
     * 
     * @param string $file Archivo de la plantilla.
     * 
     * @return void
     */
    private function _getFromCache($file)
    {
        if ( ! $this->_isCached($file))
        {
            $content = (file_exists($file)) ? file_get_contents($file) : '';
            
            Core::getLib('template.compiler')->compile($this->_getCachedName($file), $content, $file);
        }
        
        require $this->_getCachedName($file);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Comprueba si una plantilla ya se ha almacenado en caché o no.
     * 
     * @param string $name Nombre de la plantilla.
     * 
     * @return bool TRUE se encuentra en caché, FALSE no se ha compilado la plantilla.
     */
    private function _isCached($name)
    {
        //return false;
        if ( ! file_exists($compileName = $this->_getCachedName($name)))
        {
            return false;
        }
        
        if (file_exists($name))
        {
            $time = filemtime($name);
            $ctime = filemtime($compileName);
            
            // Checamos si la plantilla fue modificada recientemente.
            if($ctime <= $time)
            {
                return false;
            }
        }
        
        return true;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtiene la ruta del archivo de plantilla en caché
     * 
     * @param string $name Nombre de la plantilla.
     * 
     * @return string Ruta completa al archivo en caché.
     */
    private function _getCachedName($name)
    {
        if ( ! is_dir(CACHE_PATH . 'template' . DS))
        {
            Core::getLib('file')->mkdir(CACHE_PATH . 'template' . DS, true, 0777);
        }
        
        return CACHE_PATH . 'template' . DS . str_replace(array(TPL_PATH, MOD_PATH, TPL_SUFFIX, DS), array('', '', '', '_'), $name) . ($this->_isAdmin ? '_admin' : '') . '.php';
    }
}