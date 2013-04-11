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
 * Cache
 * 
 * Clase para el manejo del caché mediante archivos.
 * 
 * @package     Framework\Core\Cache
 * @since       1.0.0
 * @todo        Agregar soporte para Memcache.
 */
class Core_Cache {

    /**
     * Lista de todos los archivos de caché que se han guardado.
     * 
     * @var array
     */
    private $_files = array();
    
    /**
     * Nombre del archivo de caché actual que estamos guardando.
     * 
     * @var string
     */
    private $_name = '';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('cache.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Genera un ID para el archivo en caché.
     * 
     * @param string $name Nombre del archivo en caché.
     * 
     * @return string ID del archivo.
     */
    public function set($name)
    {
        if (is_array($name))
        {
            $newDir = CACHE_PATH . $name[0];
            if ( ! is_dir($newDir))
            {
                Core::getLib('file')->mkdir($newDir, true, 0777);
            }
            $name = rtrim($name[0], '/') . DS . $name[1];
        }
        
        $id = $name;
        
        $this->_files[$id] = $name;
        $this->_name = $name;
        
        return $id;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener el contenido del archivo en caché.
     * 
     * @param string $id ID de archivo en caché.
     * @param int $time Tiempo que permanecerá activo en caché.
     * 
     * @return array|bool Nos devuelve el arreglo con el contenido o FALSE si no existe el archivo en caché.
     */
    public function get($id, $time = 0)
    {
        if ( ! $this->isCached($id, $time))
        {
            return false;
        }
        
        require $this->_getName($this->_files[$id]);
        
        if ( ! isset($content))
        {
            return false;
        }
        
        if ( ! is_array($content) && empty($content))
        {
            return true;
        }
        
        if ( is_array($content) && ! count($content))
        {
            return true;
        }
        
        return $content;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Guardar datos en caché
     * 
     * @param string $id ID de archivo en caché.
     * @param array $data Contenido que será guardado en caché.
     * 
     * @return void
     */
    public function save($id, $data)
    {
        $data = '<?php $content = ' . var_export($data, true) . ';';
        
        if ($open = @fopen($this->_getName($this->_files[$id]), 'w+'))
        {
            fwrite($open, $data);
            fclose($open);
        }
        else
        {
            Core_Error::trigger('Por favor revise los permisos del directorio /tmp/cache/');
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Eliminar datos de caché.
     * 
     * @param string $name Nombre del archivo que será eliminado.
     * 
     * @return void
     */
    public function remove($name)
    {
        if ( is_array($name))
        {
            $name = rtrim($name[0], '/') . DS . $name[1]; 
        }
        
        $file = $this->_getName($name);
        
        if (file_exists($file))
        {
            @unlink($file);
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Comprueba si un archivo está almacenado en caché o no.
     * 
     * @param string $id ID de archivo en caché.
     * @param int $time Tiempo que permanece en caché.
     * 
     * @return bool
     */
    public function isCached($id, $time = 0)
    {
        if (isset($this->_files[$id]) && file_exists($this->_getName($this->_files[$id])))
        {
            if ($time && (CORE_TIME - $time * 60) > (filemtime($this->_getName($this->_files[$id]))))
            {
                $this->remove($this->_files[$id]);
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Devuelve la ruta completa al archivo de caché.
     * 
     * @param string $file Nombre del archivo.
     * 
     * @return string
     */
    private function _getName($file)
    {
        return CACHE_PATH . $file . '.php';
    }
}