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
 * Error
 * 
 * Auxiliar en el manejo de los errores generados en el sistema.
 * 
 * @package     Framework\Core\Error
 * @since       1.0.0
 * @todo        Agregar soporte para set_error_handler()
 */
class Core_Error {
    
    /**
     * Lista de errores establecidos.
     * 
     * @var array
     */
    private static $_errors = array();
    
    // --------------------------------------------------------------------
    
    /**
     * Generar un error.
     * 
     * @param string $message Mensaje del error.
     * @param int $level Nivel de error.
     * 
     * @return bool Termina la ejecución si es un error nivel: E_USER_ERROR retorna FALSE de lo contrario.
     */
    public static function trigger($message = '', $level = E_USER_ERROR)
    {
        $calle = @next(debug_backtrace());
        
        trigger_error(strip_tags(utf8_decode($message) . ' en ' . $calle['file'] . ' linea ' . $calle['line']), $level);
        
		if ($level == E_USER_ERROR)
		{
			exit;
		}
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar un error.
     * 
     * Esto nos ayuda a llevar un control de los errores generados durante 
     * la ejecución de instrucciones en nuestros componentes.
     * 
     * <code>
     * if($cond == false)
     * {
     *      return Core_Error::set(Core::getPhrase('core.error_message_cond'));
     * }
     * </code>
     * 
     * @param string $error Mensaje de error que deseamos agregar.
     * 
     * @return bool FALSE nos permite usarlo como una funcion de retorno.
     */
    public static function set($error)
    {
        self::$_errors[] = $error;
        
        return false;
    }
    
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener todos los errores generados hasta el momento.
     * 
     * @param bool $array TRUE Devuelve los errores como un arreglo, FALSE como texto.
     * 
     * @return array|string Mensajes de error.
     */
    public static function get($array = false)
    {
        return ($array) ? self::$_errors : implode('', self::$_errors);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Verifica la existencia de errores.
     * 
     * Se utiliza para saber si ha ocurrido un error hasta este punto. Se usa
     * dentro de una condicional para saber si seguir o no.
     * 
     * Ejemplo:
     * <code>
     * if(Core_Error::isPassed())
     * {
     *      // Seguir...
     * }
     * else
     * {
     *      // Existe un error.
     * }
     * </code>
     * 
     * @return bool TRUE Si no existen errores, FALSE si los hay.
     */
    public static function isPassed()
    {
        return ( ! count(self::$_errors)) ? true : false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Mostrar error 404
     * 
     * @return void
     */
    public static function show404()
    {
        return Core::getLib('module')->setController('error.404');
    }
}