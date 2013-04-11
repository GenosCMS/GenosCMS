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
 * URL
 * 
 * Permite un manejo eficiente de las URL internas/externas al sistema.
 * 
 * @package     Framework\Core\Url
 * @since       1.0.0
 */
class Core_Url {
    
    /**
     * Listado de Rutas definidas por el usuario.
     * 
     * @var array
     */
    private $_routes = array();
    
    /**
     * Segmentos encontrados en la URI
     * 
     * @var array
     */
    private $_segments = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('url.init') : null;
        
        // Cachear rutas definidas por el usuario.
        $cache = Core::getLib('cache');
        $cacheId = $cache->set('rewrite');
        
        if ( ! ($routes = $cache->get($cacheId)))
        {
            $rows = Core::getLib('database')->select('r.url, r.replacement, m.module_id')
                ->from('rewrite', 'r')
                ->join('module', 'm', 'r.module_id = m.module_id AND m.is_active = 1')
                ->order('r.ordering ASC')
                ->exec('rows');
                
            foreach ($rows as $row)
            {
                $routes[$row['module_id']][$row['url']] = $row['replacement'];
            }
            
            $cache->save($cacheId, $routes);
        }
        
        $this->_routes = $routes;
        unset($routes);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Genera las rutas de acuerdo al URI recibido.
     * 
     * @return void
     */
    public function setRouting()
    {
        // Generar los segmentos de la URI
        $this->_setSegments();
        
        // Parsear las rutas
        $this->_parseRoutes();
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar las rutas de acuerdo al URI recibido. Para peticiones AJAX
     * 
     * @return void
     */
    public function setAjaxRouting()
    {
        $this->_setSegments(true);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener los segmentos
     * 
     * @return array Arreglo con los segmentos.
     */
    public function getSegments()
    {
        return $this->_segments;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener un segmento
     * 
     * @param int $index Posición del segmento
     * 
     * @return mixed Valor del segmento.
     */
    public function segment($index)
    {
        return (isset($this->_segments[$index]) ? $this->_segments[$index] : '');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener los parámetros recibidos en el URI
     * 
     * Nos permite enviar como parámetros al método main() de un controlador
     * todos los segmentos posteriores al módulo/controlador.
     * 
     * Ejemplo:
     * 
     * Segmentos recibidos: post/view/1234/title-post
     * Clase solicitada: post.view
     * Parámetros que serán enviados: main(1234, title-post);
     * 
     * @param string $class Es el controlador que fue solicitado.
     * 
     * @return array Segmentos que serán pasados como variables.
     */
    public function params($class)
    {
        return array_slice($this->_segments, count(explode('.', $class)));
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Enviar al usuario a una nueva URL directa.
     * 
     * @param string $url URL de destino
     * @param string $message Opcional, se puede agregar un mensaje FLASH.
     * 
     * @return void
     */
    public function forward($url, $message = null)
    {
        if ($message !== null)
        {
            Core::addMessage($message);
        }
        
        $this->_send($url);
        exit;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Enviar al usuario a una nueva URL
     * 
     * @see self::makeUrl()
     * 
     * @param string $url URL de destino. El mismo formato que makeUrl();
     * @param array $params Parámetros adicionales que se agregarán a la URL.
     * @param string $message Opcional, se puede agregar un mensaje FLASH.
     * 
     * @return void
     */
    public function send($url, $params = array(), $message = null)
    {
        if ($message !== null)
        {
            Core::addMessage($message);
        }
        
        $this->_send((preg_match("/(http|https):\/\//i", $url) ? $url : $this->makeUrl($url, $params)));
        exit;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear un enlace interno.
     * 
     * Ejemplo:
     * 
     * <code>
     * Core::getLib('url')->makeUrl('user.profile.' . $userID); // Resultado: /user/profile/1234
     * Core::getLib('url')->makeUrl('user.edit', array('id' => 1234, 'u' => 'Nombre')); // Resultado: /user/edit?id=1234&u=Nombre
     * Core::getLib('url')->makeUrl('http://external.com'); // Resultado: http://external.com
     * </code>
     * 
     * @param string $url Dirección del enlace.
     * @param array $params Son parámetros que se agregarán a la URL.
     * 
     * @return string
     */
    public function makeUrl($url, $params = array())
    {
        // Es una url externa?
        if (preg_match('/http:\/\//i', $url))
        {
            return $url;
        }
        
        // Dirección actual
        if ($url == 'current')
        {
            $url = '';
            foreach ($this->_segments as $segment)
            {
                $url .= $segment . '.';
            }
        }
        
        // URL ajax
        $isAjax = false;
        if (substr($url, 0, 5) == 'ajax.')
        {
            $url = (Core::getParam('core.url_rewrite_mod')) ? $url : substr($url, 5);
            $isAjax = true;
        }
        
        // Contiene parámetros extra?
        if ( ! is_array($params))
        {
            $params = array();
        }
        
        $url = trim($url, '.');
        $urls = '';
        
        $parts = explode('.', $url);
        
        $urls .= (Core::getParam('core.url_full_path')) ? Core::getParam('core.path') : Core::getParam('core.folder');
        $urls .= (Core::getParam('core.url_rewrite_mod')) ? '' : ($isAjax ? 'ajax.php/' : 'index.php/');
        $urls .= $this->_makeUrl($parts, $params);
        
        return $urls;
    }
    
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener los segmentos de la URI
     * 
     * @return void
     */
    private function _setSegments($isAjax = false)
    {
        // Obtenemos el URI
        $parts = preg_split('#\?#i', $_SERVER['REQUEST_URI'], 2);
        $uriString = (empty($parts[0]) ? '/' : str_replace(array('//', '../'), '/', trim($parts[0], '/')));
        
        // Generamos los segmentos
        foreach ( explode('/', preg_replace('|/*(.+?)/*$|', '\\1', $uriString)) as $val)
        {
            $this->_segments[] = strtolower(trim($val));
        }
        
        // ReIndexar segmentos.
        // Si está activo el módulo Rewrite eliminamos el primer segmento correspondiente a index.php/ajax.php
        if (Core::getParam('core.url_rewrite_mod') && !$isAjax)
        {
            array_unshift($this->_segments, NULL);
        }
        
        unset($this->_segments[0]);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Parsear la ruta
     * 
     * @return void
     */
    private function _parseRoutes()
    {
        $module = $this->segment(1);
        
        // NO hay rutas para este módulo
        if ( ! isset($this->_routes[$module]))
        {
            return;
        }
        
        //
        $uri = implode('/', $this->_segments);
        foreach ($this->_routes[$module] as $key => $val)
        {
            $key = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $key);
            
            if ( preg_match('#^'.$key.'$#', $uri))
            {
                // Tenemos una variable de referencia?
                if (strpos($val, '$') !== false && strpos($key, '(') !== false)
                {
                    $val = preg_replace('#^'.$key.'$#', $val, $uri);
                }
                
                // Asignamos los nuevos segmentos y ReIndexamos
                $this->_segments = explode('/', $val);
                array_unshift($this->_segments, NULL);
                unset($this->_segments[0]);
                
                break;
            }
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Redireccionar.
     * 
     * @param string $url
     * 
     * @return void
     */
    private function _send($url)
    {
        // Liberámos buffer
        ob_clean();
        
        // Enviamos...
        header('Location: ' . $url);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Formar URL
     * 
     * @see self::makeUrl()
     * 
     * @param array $parts Segmentos de la URL.
     * @param array $params Parámetros que agregarémos a la URL.
     * 
     * @return string
     */
    private function _makeUrl(&$parts, &$params)
    {   
        // Primero las "subcarpetas"
        $urls = '';
        foreach ($parts as $part)
        {
            $urls .= $part . '/';
        }
        $urls = rtrim($urls, '/');
        
        // Sufijo
        $isAjax = (substr($urls, 0, 4) == 'ajax') ? true : false;
        $suffix = ($isAjax ? Core::getParam('core.url_ajax_suffix') : Core::getParam('core.url_suffix'));
        $urls .= $suffix;
        
        // Parámetros
        if (count($params))
        {
            $urls .= '?';
            foreach ($params as $key => $value)
            {
                $urls .= $key . '=' . $value . '&';
            }
            $urls = trim($urls, '&');
        }
        
        return ($urls == $suffix) ? '' : $urls;
    }
}