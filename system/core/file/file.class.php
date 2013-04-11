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
 * File
 * 
 * Nos permite el manejo de archivos y directorios dentro del sistema.
 * 
 * @package     Framework\Core\File
 * @since       1.0.0
 */
class Core_File {
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('file.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener los archivos/carpetas de un directorio
     * 
     * @param string $dir Directorio donde buscarémos.
     * 
     * @return array Lista de archivos encontrados en el directorio.
     */
    public function getFiles($dir)
    {
        $files = array();
        if ($op = @opendir($dir))
        {
            while(($file = readdir($op)) !== false)
            {
                if ($file == '.' || $file == '..' || $file == 'index.html')
                {
                    continue;
                }
                
                $files[] = $file;
            }
            
            closedir($op);
            return $files;
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Verifica si el archivo o directorio tiene permisos de escritura
     * 
     * @param string $filePath Archivo o directorio a comprobar.
     * 
     * @return bool
     */
    public function isWritable($filePath)
    {
        if ( ! is_writable($filePath))
        {
            if ( ! stristr(PHP_OS, 'win'))
            {
                return false;
            }
        }
        
        return true;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear un directorio.
     * 
     * Nos permite crear uno o más directorios recursivos basados en la ruta
     * recibida.
     * 
     * @param string $dir El directorio completo que vamos a crear
     * @param bool $recurse TRUE nos permite crear directorios recursivos.
     * @param mixed $chmod Opcional, se pueden establecer los permisos CHMOD que tendrá el directorio.
     * 
     * @return void
     */
    public function mkdir($dir, $recurse = false, $chmod = null)
    {
        if ($recurse !== false)
        {
            $parts = explode(DS, trim($dir, DS));
            $parentDir = ((PHP_OS == 'WINNT' || PHP_OS == 'WIN32' || PHP_OS == 'Windows') ? '' : DS);
            
            foreach ($parts as $dir)
            {
                if ( ! is_dir($parentDir . $dir))
                {
                    mkdir($parentDir . $dir);
                    if ($chmod !== null)
                    {
                        chmod($parentDir . $dir, $chmod);
                    }
                }
                
                $parentDir .= $dir . DS;
            }
        }
        else
        {
            mkdir($dir);
            if ($chmod !== null)
            {
                chmod($dir, $chmod);
            }
        }
    }
}