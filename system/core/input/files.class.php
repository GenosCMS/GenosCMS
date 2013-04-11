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
 * Input Files
 * 
 * Gestiona la entrada de datos mediante $_FILES
 * 
 * @package     Framework\Core\Input
 * @since       1.0.0
 */
class Core_Input_Files extends Core_Input {
        
    /**
     * Constructor
     * 
     * Reemplaza al de su clase padre.
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('input.files.init') : null;
        
        // $_FILES
        $this->data =& $_FILES;
        
        // Filtro
        $this->filter = Core::getLib('filter.input');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener un archivo.
     * 
     * Obtiene y genera un arreglo con los datos del archivo.
     * 
     * @param string $name Nombre del campo.
     * @param mixed $default Si no existe un valor retornamos este.
     * @param string $filter Filtro que aplicarémos al valor.
     * 
     * @return array Arreglo con los datos del archivo.
     */
	public function get($name, $default = null, $filter = 'cmd')
	{
		if (isset($this->data[$name]))
		{
			$results = $this->decodeData(
				array(
					$this->data[$name]['name'],
					$this->data[$name]['type'],
					$this->data[$name]['tmp_name'],
					$this->data[$name]['error'],
					$this->data[$name]['size']
				)
			);
			return $results;
		}

		return $default;

	}
    
    // --------------------------------------------------------------------
    
    /**
     * Método para decodificar los datos.
     * 
     * @param array $data Datos del archivo.
     */
    protected function decodeData(array $data)
	{
		$result = array();

		if (is_array($data[0]))
		{
			foreach ($data[0] as $k => $v)
			{
				$result[$k] = $this->decodeData(array($data[0][$k], $data[1][$k], $data[2][$k], $data[3][$k], $data[4][$k]));
			}
			return $result;
		}

		return array('name' => $data[0], 'type' => $data[1], 'tmp_name' => $data[2], 'error' => $data[3], 'size' => $data[4]);
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Establecer un valor
     * 
     * ::No aplica para este input::
     * 
     * @param string $name Nombre del valor.
     * @param mixed $value Valor.
     * 
     * @return void
     */
    public function set($name, $value)
    {

    }
}