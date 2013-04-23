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
 * Core
 * 
 * Esta es la clase base de todo.
 * 
 * Se encarga de cargar las librerías así como interactuar con ellas para
 * ofrecer mayor comodidad al realizar algunas tareas.
 * 
 * @package     Framework\Core
 * @since       1.0.0
 * @final
 */
final class Core {
    
    /**
     * Versión del Framework
     */
    const VERSION = '1.0.0 [beta]';
    
    /**
     * Nombre código
     */
    const CODE_NAME = 'Hydro';
    
    /**
     * Lista de objetos cargados.
     * 
     * @var array
     */
    private static $_object = array();
    
    /**
     * Lista de librerías cargadas.
     * 
     * @var array
     */
    private static $_libs = array();
    
    /**
     * Cargar librería y crear objecto de la clase.
     * 
     * Esta función carga una librería del nucleo, crea un objeto y lo retorna.
     * 
     * Ejemplo:
     * <code>
     * Core::getLib('url')->makeUrl('test');
     * </code>
     * 
     * En el ejemplo anterior se cargó la librería URL ubicada en /system/core/url/url.class.php
     * se creó una instancia de la clase y así se púdo llamar al método "makeUrl" directamente.
     * 
     * @param string $class Nombre de la librería
     * @param array $params Arreglo con los parámetros con que será inicializada la clase.
     * 
     * @return object Un objeto de la clase será retornado.
     */
    public static function &getLib($class, $params = array())
    {
        if (substr($class, 0, 5) != 'core.')
        {
            $class = 'core.' . $class;
        }
        
        $hash = md5($class . serialize($params));
        
        if (isset(self::$_object[$hash]))
        {
            return self::$_object[$hash];
        }
        
        Core::getLibClass($class);
        
        self::$_object[$hash] = Core::getObject($class, $params);
        
        return self::$_object[$hash];
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar archivo de librería.
     * 
     * Esta función se asegura de que exista el archivo de la librería y lo
     * carga para ser usado.
     * 
     * @param string $class Nombre de la clase.
     * 
     * @return bool Regresa TRUE si el archivo fue cargado, FALSE en caso contrario.
     */
    public static function getLibClass($class)
    {
        if (isset(self::$_libs[$class]))
        {
            return true;
        }
        
        self::$_libs[$class] = md5($class);
        
        $class = str_replace('.', DS, $class);
        
        $file = SYS_PATH . $class . '.class.php';
        
        if (file_exists($file))
        {
            require $file;
            return true;
        }
        
        $parts = explode(DS, $class);
        if(isset($parts[0]))
        {
            $subClassFile = SYS_PATH . $class . DS . $parts[1] . '.class.php';
            
            if (file_exists($subClassFile))
            {
                require $subClassFile;
                return true;
            }
        }
        
        Core_Error::trigger('No se puede cargar la librería: ' . $parts[0] . '/' . $parts[1] . '.class.php');
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear objeto de una clase.
     * 
     * @param string $class Nombre de la clase.
     * @param array $params Parámetros que pueden ser enviados al constructor de la clase.
     * 
     * @return object El objeto creado.
     */
    public static function &getObject($class, $params = array())
    {
        $hash = md5($class . serialize($params));
        
        if (isset(self::$_object[$hash]))
        {
            return self::$_object[$hash];
        }
        
        $class = str_replace(array('.', '-'), '_', $class);
        
        if ( ! class_exists($class))
        {
            Core_Error::trigger('No se puede crear un objeto de la clase: ' . $class);
        }
        
        if (count($params) > 0)
        {
            self::$_object[$hash] = new $class($params);
        }
        else
        {
            self::$_object[$hash] = new $class();
        }
        
        if (method_exists(self::$_object[$hash], 'getInstance'))
        {
            return self::$_object[$hash]->getInstance();
        }
        
        return self::$_object[$hash];
    }

    // --------------------------------------------------------------------

    /**
     * Comprobar si una clase ha sido cargada.
     *
     * @param string $class Nombre de la clase.
     * @param array $params Parámetros de la clase.
     *
     * @return bool
     */
    public static function isLoaded($class, $params = array())
    {
        if (substr($class, 0, 5) != 'core.')
        {
            $class = 'core.' . $class;
        }

        $hash = md5($class . serialize($params));

        return (isset(self::$_object[$hash]) ? true : false);
    }

    // --------------------------------------------------------------------

    /**
     * Agrega el prefijo a la tabla.
     * 
     * @param string $table Nombre de la tabla.
     * 
     * @return string Tabla con el prefijo definido en la configuración.
     */
    public static function getT($table)
    {
        return Core::getParam(array('db', 'prefix')) . $table;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar un parámetro de configuración
     * 
     * @see Core_Setting::get();
     * 
     * @param string $name Nombre del parámetro.
     * 
     * @return mixed Contenido del parámetro.
     */
    public static function getParam($name)
    {
        return Core::getLib('setting')->get($name);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Añadir un parámetro de configuración.
     * 
     * @see Core_Setting::set()
     * 
     * @param array $name Nombre del parámetro.
     * @param mixed $value Valor del parámetro.
     * 
     * @return void
     */
    public static function setParam($name, $value = '')
    {
        Core::getLib('setting')->set($name, $value);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar un bloque.
     * 
     * @see Core_Module::getComponent()
     * 
     * @param string $class Nombre de la clase.
     * @param array $params Parámetros para el constructor del componente.
     * @param bool $templateParams TRUE asigna los parámetros ($params) a la plantilla.
     * 
     * @return void
     */
    public static function getBlock($class, $params = array(), $templateParams = false)
    {
        Core::getLib('module')->getComponent($class, $params, 'block', $templateParams);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar un servicio
     * 
     * @see Core_Module::getService()
     * 
     * @param string $class Nombre de la clase.
     * @param array $params Parámetros para el constructor del servicio.
     * 
     * @return object Objeto del servicio cargado.
     */
    public static function getService($class, $params = array())
    {
        return Core::getLib('module')->getService($class, $params);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener una frase de un lenguaje
     * 
     * @see Core_Lang::phrase()
     * 
     * @param string $index Formato de la frase que será cargada.
     * @param array $params Parámetros que serán reemplazados en la frase.
     * @param string $idiom Lenguage del cual cargarémos la frase.
     * 
     * @return string Frase traducida y con los parámetros reemplazados.
     */
    public static function getPhrase($index, $params = array(), $idiom = '')
    {
        return Core::getLib('language')->phrase($index, $params, $idiom);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Checar si un módulo es válido.
     * 
     * @see Core_Module::isModule()
     * 
     * @param string $module Nombre del módulo.
     * 
     * @return bool TRUE si el módulo existe y está activo.
     */
    public static function isModule($module)
    {
        return Core::getLib('module')->isModule($module);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Marcar punto.
     * 
     * @see Core_Debug::mark()
     * 
     * @param string $label Una etiqueta para la marca del tiempo.
     * 
     * @return void
     */
    public static function mark($label)
    {
        Core::getLib('debug')->mark($label);
    }

    // --------------------------------------------------------------------

    /**
     * Saber si se enviaron datos mediante $_POST
     *
     * @return bool
     */
    public static function isPost()
    {
        return (count($_POST) > 0) ? true : false;
    }
}