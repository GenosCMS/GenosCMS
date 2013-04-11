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
 * Input Cookie
 * 
 * Gestiona la entrada de datos.
 * 
 * Estos son los métodos mágicos que podemos usar.
 * 
 * Todos siguen la siguiente estructura:
 * 
 * getFilter($name, $default = null); Dónde "Filter" es el filtro que usarémos.
 * 
 * @method integer getInt()   Detecta el primer valor, entero con signo.
 * @method integer getUint()  Detecta el primer valor entero sin signo.
 * @method float   getFloat() Detecta el primer número de punto flotante.
 * @method boolean getBool()  Convierte el valor a un tipo de datos booleano.
 * @method string  getWord()  Permite sólo mayúsculas, minúsculas (A-Z) y guiones bajos.
 * @method string  getAlnum() Permite sólo mayúsculas, minúsculas (A-Z) y dígitos.
 * @method string  getCmd()   Permite sólo mayúsculas, minúsculas (A-Z), guiones bajos, puntos y guiones.
 * @method string  getBase64()Permite sólo caracteres válidos en la codificación Base64
 * @method string  getString()Devuelve una cadena totalmente decodificada.
 * @method string  getHtml()  Devuelve una cadena con las entidades HTML y etiquetas intactas, sujeto al filtro XSS.
 * @method array   getArray() Devuelve el valor como una matriz sin filtrado adicional.
 * 
 * @package     Framework\Core\Input
 * @since       1.0.0
 */
class Core_Input {
    
    /**
     * Datos enviados por el usuario.
     * 
     * ($_GET, $_POST, $_FILES)
     * 
     * @var array
     */
    protected $data = array();
    
    /**
     * Objetos de entrada
     * 
     * @var array
     */
    protected $inputs = array();
    
    /**
     * Objetos de entrada auxiliares.
     * 
     * @var array
     */
    private $_inputAux = array(
        'files' => 'input.files',
        'cookie' => 'input.cookie',
        'server' => 'input.server'
    );
    
    /**
     * Constructor
     * 
     * @param array $source Origen de los datos, por defecto es $_REQUEST
     */
    public function __construct($source = null)
    {
        DEBUG_MODE ? Core::mark('input.init') : null;
        
        if ( is_null($source))
        {
            $this->data =& $_REQUEST;
        }
        else
        {
            $this->data = $source;
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Método mágico que nos ayuda a filtrar los datos
     * 
     * @param string $name Nombre de la función
     * @param array $arguments Argumentos ([0] => Nombre de la variable, [1] => Valor por defecto)
     * 
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get')
        {
            $filter = substr($name, 3);
            
            $default = null;
            if (isset($arguments[1]))
            {
                $default = $arguments[1];
            }
            
            return $this->get($arguments[0], $default, $filter);
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Método mágico para obtener el objeto de entrada.
     * 
     * @param $name Nombre del objecto
     * 
     * @return object
     */
    public function __get($name)
    {
        if (isset($this->inputs[$name]))
        {
            return $this->inputs[$name];
        }

        if (isset($this->_inputAux[$name]))
        {
            $this->inputs[$name] = Core::getLib($this->_inputAux[$name]);
            return $this->inputs[$name];
        }
        
        $superGlobal = '_' . strtoupper($name);
        if ( isset($GLOBALS[$superGlobal]))
        {
            $this->inputs[$name] = Core::getLib('input', array($GLOBALS[$superGlobal]));
            return $this->inputs[$name];
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener un dato.
     * 
     * @param string $name Nombre del campo.
     * @param mixed $default Si no existe un valor retornamos este.
     * @param string $filter Filtro que aplicarémos al valor.
     * 
     * @return mixed El valor 'filtrado'
     */
    public function get($name, $default = null, $filter = 'cmd')
    {
        if (isset($this->data[$name]))
        {
            return Core::getLib('filter.input')->clean($this->data[$name], $filter);
        }
        
        return $default;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtiene un arreglo con los valores de la solicitud.
     * 
     * @param array $vars Arreglo de valores que queremos recuperar.
     * @param mixed $datasource Arreglo desde el cual recuperarémos los datos.
     * 
     * @return array Valores filtrados.
     */
	public function getArray(array $vars, $datasource = null)
	{
		$results = array();

		foreach ($vars as $k => $v)
		{
			if (is_array($v))
			{
				if (is_null($datasource))
				{
					$results[$k] = $this->getArray($v, $this->get($k, null, 'array'));
				}
				else
				{
					$results[$k] = $this->getArray($v, $datasource[$k]);
				}
			}
			else
			{
				if (is_null($datasource))
				{
					$results[$k] = $this->get($k, null, $v);
				}
				elseif (isset($datasource[$k]))
				{
					$results[$k] = Core::getLib('filter.input')->clean($datasource[$k], $v);
				}
				else
				{
					$results[$k] = Core::getLib('filter.input')->clean(null, $v);
				}
			}
		}

		return $results;
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Establecer un dato
     * 
     * @param string $name Nombre del campo.
     * @param mixed $value Valor.
     * 
     * @return void
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Contar valores recibidos
     * 
     * @return int Total de datos recibidos.
     */
    public function count()
    {
        return count($this->data);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener el método de la entrada de datos.
     * 
     * @return string Método usado para recolectar los datos.
     */
	public function getMethod()
	{
		$method = strtoupper($_SERVER['REQUEST_METHOD']);
		return $method;
	}
}