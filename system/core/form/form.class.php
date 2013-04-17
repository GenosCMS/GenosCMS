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
 * Form
 * 
 * Esta clase nos permite validar formularios y además preparar los datos
 * para ingresarlos a la Base de Datos.
 * 
 * @todo        Agregar soporte para campos tipo array ej: <input type="text" name="url[]" />
 * 
 * @package     Framework\Core\Form
 * @since       1.0.0
 */
class Core_Form {
    
    /**
     * Controlador frontal, es el que usa el validador.
     * 
     * @var object
     */
    private $_object = null;
    
    /**
     * Datos del formulario
     * 
     * @var array
     */
    private $_data = array();
    
    /**
     * Lista de errores generados.
     * 
     * @var array
     */
    private $_error = array();
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('form.init') : null;
    }
    
    /**
     * Establecer reglas para uno o más campos.
     * 
     * Podemos enviar mediante un arreglo los datos de los campos que queremos validar.
     * 
     * @param array|string $field Nombre del campo o arreglo de reglas de varios campos.
     * @param mixed $value Este será el valor del campo, por lo general obtenido por Core_Input
     * @param string $label Frase del nombre del campo.
     * @param string $rules Reglas que serán aplicadas al campo.
     * 
     * @return Core_Form_Validator
     */
    public function set($field, $value = '', $label = '', $rules = '')
    {
        // Si se trata de un arreglo de reglas, las asignamos una por una.
        if (is_array($field))
        {
            foreach ($field as $row)
            {
                // El nombre del campo es requerido
                if ( ! isset($row['field']))
                {
                    continue;
                }
                
                // Realizamos un par de comprobaciones
                $row['value'] = ( isset($row['value']) ? $row['value'] : '');
                $row['label'] = ( isset($row['label']) ? $row['label'] : $row['field']);
                $row['rules'] = ( isset($row['rules']) ? $row['rules'] : '');
                
                // Asignamos
                $this->set($row['field'], $row['value'], $row['label'], $row['rules']);
            }
            
            return $this;
        }
        
        // Comprobar campos
        if ( ! is_string($field) || ! is_string($rules) || $field == '')
        {
            return $this;
        }
        
        // Agregamos los datos
        $this->_data[$field] = array(
            'field'     => $field,
            'value'     => $value,
            'label'     => $label,
            'rules'     => $rules,
            'error'     => '',
        );
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Validar campos.
     * 
     * Realiza el proceso de validación para cada campo asignado por self::set().
     * Si harémos validaciones personalizadas necesitamos la instancia del
     * controlador como parámetro.
     * 
     * @param object $object Controlador frontal.
     * 
     * @return bool TRUE si los campos pararon la validación, FALSE en caso contrario.
     */
    public function validate($object = null)
    {
        // Controlador frontal.
        if ( ! is_null($object))
        {
            $this->_object = &$object;
        }
        
        // Sin campos asignados
        if ( count($this->_data) == 0)
        {
            return false;
        }
        
        foreach ($this->_data as $field => $row)
        {
            $this->_exec($row, explode('|', $row['rules']), $row['value']);
        }
        var_dump($this);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * 
     */
    public function setValue($field, $default)
    {
        if ( ! isset($this->_data[$field]))
        {
            return $default;
        }
        
        return $this->_data[$field]['value'];
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Contar campos validados.
     * 
     * @return int Total de campos.
     */
    public function count()
    {
        return count($this->_data);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Ejecutar la validación de un campo.
     * 
     * @param array $row Datos del campo a validar.
     * @param int $cycle Auxiliar para campos de tipo array.
     * 
     * @return void    
     */
    private function _exec($row, $rules, $value, $cycle = 0)
    {
        // Aplicamos las mismas reglas a campos de tipo array
        if (is_array($value))
        {
            foreach ($value as $key => $val)
            {
                $this->_exec($row, $rules, $val, $cycle);
                $cycle++;
            }
            
            return;
        }
        
        // --------------------------------------------------------------------
        
        // Validamos cada regla
        foreach ($rules as $rule)
        {
            $is_array = false;
            if (is_array($this->_data[$row['field']]['value']))
            {
                $is_array = true;
            }
            
            // La regla usará una función del controlador?
			$callback = false;
			if (substr($rule, 0, 9) == 'callback_')
			{
				$rule = substr($rule, 9);
				$callback = true;
			}
            
            // Buscamos parámetros, las reglas pueden contener parámetros: min_length[3]
			$param = false;
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match))
			{
				$rule	= $match[1];
				$param	= $match[2];
			}
            
            if ($callback == true)
            {
                if ( ! method_exists($this->_object, $rule))
                {
                    continue;
                }
                
                $result = $this->_object->$rule($value, $param);
                
                if ($is_array == true)
                {
                    $this->_data[$row['field']]['value'][$cycle] = (is_bool($result) ? $value : $result);
                }
                else
                {
                    $this->_data[$row['field']]['value'] = (is_bool($result) ? $value : $result);
                }
                
                // No es un campo requerido
                if ( ! in_array('required', $rules, true) && $result !== false)
                {
                    continue;
                }
            }
            else
            {
                // Cargamos el plugin
                $plugin = 'form_rule_' . $rule;
                $this->_loadPlugin($rule);
                
                // Si no existe tal vez sea una función de PHP
                if ( ! function_exists($plugin))
                {
                    if ( function_exists($rule))
                    {
                        $result = $rule($value);
                        
                        if ($is_array == true)
                        {
                            $this->_data[$row['field']]['value'][$cycle] = (is_bool($result) ? $value : $result);
                        }
                        else
                        {
                            $this->_data[$row['field']]['value'] = (is_bool($result) ? $value : $result);
                        }
                    }
                    
                    continue;
                }
                
                // Validamos plugin
                $result = $plugin($value, $param);
                
                if ($is_array == true)
                {
                    $this->_data[$row['field']]['value'][$cycle] = (is_bool($result) ? $value : $result);
                }
                else
                {
                    $this->_data[$row['field']]['value'] = (is_bool($result) ? $value : $result);
                }
            }
            
            // --------------------------------------------------------------------
            
            // Mensaje de error
            if ($result === false)
            {
                // Regla
                $rule = ($rule == 'is_valid') ? 'is_valid_' . $param : $rule;
                // Generamos el mensaje
                $message = Core::getPhrase('core.form_rule_' . $rule, array(
                        'field' => $row['label'],
                        'param' => $param,                    
                    )
                );
                
                // Asignamos error, los errores se generarán en el orden de las reglas.
                if ($this->_data[$row['field']]['error'] != '')
                {
                    continue;
                }
                
                $this->_data[$row['field']]['error'] = $message;
                
                // Errores
                if ( ! isset($this->_error[$row['field']]))
                {
                    $this->_error[$row['field']] = $message;
                }
            }
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar plugin de validación.
     * 
     * @param string $name Nombre del plugin
     * 
     * @return void
     */
    private function _loadPlugin($name)
    {
        $filePath = CORE_PATH . 'form' . DS . 'plugin' . DS . 'rule.' . $name . '.php';
        
        if ( file_exists($filePath))
        {
            require_once $filePath;
        }
    }
}