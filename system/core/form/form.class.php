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
     * @return \Core_Form
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
     * @param string $label Frase del nombre del campo.
     * @param string $rules Reglas que serán aplicadas al campo.
     * @param null $default Valor por defecto que puede tener el campo.
     * @param string $filter Filtro que será aplicado al campo.
     * @param array $error Contiene los mensajes de error para las reglas aplicadas al campo.
     *
     * @internal param mixed $value Este será el valor del campo, por lo general obtenido por Core_Input
     * @return \Core_Form
     */
    public function set($field, $label = '', $rules = '', $default = null, $filter = '', $error = array())
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
                $row['label']   = ( isset($row['label']) ? $row['label'] : $row['field']);
                $row['rules']   = ( isset($row['rules']) ? $row['rules'] : '');
                $row['default'] = (isset($row['default']) ? $row['default'] : null);
                $row['filter']  = (isset($row['filter']) ? $row['filter'] : '');
                $row['error']   = ( isset($row['error']) ? $row['error'] : array());
                
                // Asignamos
                $this->set($row['field'], $row['label'], $row['rules'], $row['default'], $row['filter'], $row['error']);
            }
            
            return $this;
        }
        
        // Comprobar campos
        if ( ! is_string($field) || ! is_string($rules) || $field == '')
        {
            return $this;
        }

        // Campo con valores de tipo array, por el momento sólo soporta un nivel.
        if (strpos($field, '[') !== false && preg_match_all('/\[(.*?)\]/', $field, $matches))
        {
            $field = current(explode('[', $field));
            $filter = 'array';
        }
        
        // Agregamos los datos
        $this->_data[$field] = array(
            'field'     => $field,
            'label'     => $label,
            'rules'     => $rules,
            'default'   => $default,
            'filter'    => $filter,
            'error'     => $error,
            'value'     => null,
        );
        
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Obtener información de un campo.
     *
     * @param string $field Nombre del campo.
     * @param string $var Variable a retornar.
     *
     * @return string
     */
    public function get($field, $var = 'value')
    {
        return (isset($this->_data[$field][$var])) ? $this->_data[$field][$var] : null;
    }

    // --------------------------------------------------------------------

    /**
     * Validar campos.
     * 
     * Realiza el proceso de validación para cada campo asignado por self::set().
     * Si haremos validaciones personalizadas necesitamos la instancia del
     * controlador como parámetro.
     * 
     * @param object $object Controlador frontal.
     * @param string $method Método por el cual recogemos los datos del formulario, (post, get, etc...).
     * 
     * @return bool TRUE si los campos pararon la validación, FALSE en caso contrario.
     */
    public function validate($object = null, $method = 'post')
    {
        // Controlador frontal.
        if ( ! is_null($object))
        {
            $this->_object = &$object;
        }
        
        // Sin campos asignados
        if (count($this->_data) == 0)
        {
            return false;
        }
        
        foreach ($this->_data as $field => $row)
        {
            // Obtenemos el valor.
            $this->_data[$field]['value'] = Core::getLib('input')->{$method}->get($field, $row['default'], $row['filter']);

            // Validar campo...
            $this->_exec($row, explode('|', $row['rules']), $this->_data[$field]['value']);
        }

        // Si no hay errores el formulario es válido.
        if (count($this->_error) == 0)
        {
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Obtener error.
     *
     * Obtiene el error generado en un campo o un arreglo con todos los errores.
     *
     * @param string $field NULL devuelve todos los errores
     *
     * @return string Mensaje de error
     */
    public function error($field = null)
    {
        if (is_null($field))
        {
            return $this->_error;
        }

        return (isset($this->_error[$field]) ? $this->_error[$field] : '');
    }

    // --------------------------------------------------------------------

    /**
     * Obtener campos.
     *
     * Genera una lista de los campos validados con su respectivo valor.
     *
     * @return array Lista de campos.
     */
    public function fields()
    {
        $fields = array();

        foreach ($this->_data as $field => $row)
        {
            $fields[$field] = $row['value'];
        }

        return $fields;
    }

    // --------------------------------------------------------------------

    /**
     * Valor por defecto para el campo.
     *
     * Esta función es usada por Core_Form_Helper, esta nos permite mostrar
     * los valores por defecto de los campos dentro de la plantilla.
     *
     * @param string $field Nombre del campo.
     * @param string $default Valor por defecto establecido en la plantilla.
     *
     * @return string Valor del campo.
     */
    public function setValue($field, $default)
    {
        // Campo con valores de tipo array, por el momento sólo soporta un nivel.
        if (strpos($field, '[') !== false && preg_match_all('/\[(.*?)\]/', $field, $matches))
        {
            $field = current(explode('[', $field));
        }

        if ( ! isset($this->_data[$field]))
        {
            return $default;
        }

        if (is_array($this->_data[$field]['value']))
        {
            return array_shift($this->_data[$field]['value']);
        }
        
        return $this->_data[$field]['value'];
    }

    // --------------------------------------------------------------------

    /**
     * Valor por defecto para campos tipo (radio|checkbox).
     *
     * Esta función es usada por Core_Form_Helper, esta nos permite mostrar
     * los campos seleccionados por defecto dentro de la plantilla.
     *
     * @param string $field Nombre del campo.
     * @param string $value Valor del campo.
     * @param bool $default TRUE campo seleccionado, FALSE campo no seleccionado.
     *
     * @return bool
     */
    public function setOption($field, $value, $default = false)
    {
        if ( ! isset($this->_data[$field]))
        {
            return $default;
        }

        $field = $this->_data[$field]['value'];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return false;
            }
        }
        else
        {
            if (($field === '' || $value === '') || ($field != $value))
            {
                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Valor por defecto para campos tipo select.
     *
     * Esta función es usada por Core_Form_Helper, esta nos permite mostrar
     * las opciones seleccionadas por defecto dentro de la plantilla.
     *
     * @param string $field Nombre del campo.
     * @param array $default TRUE campo seleccionado, FALSE campo no seleccionado.
     *
     * @return array
     */
    public function setSelect($field, $default = array())
    {
        // Campo con valores de tipo array, por el momento sólo soporta un nivel.
        if (strpos($field, '[') !== false && preg_match_all('/\[(.*?)\]/', $field, $matches))
        {
            $field = current(explode('[', $field));
        }

        if ( ! isset($this->_data[$field]))
        {
            return $default;
        }

        $field = $this->_data[$field]['value'];

        if (is_array($field))
        {
            return $field;
        }
        else
        {
            return array($field);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Contar campos que fueron declarados.
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
     * @param array $rules Lista de reglas.
     * @param string $value Valor del campo.
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
        
        // Validar cada regla
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
                
                // Validar plugin
                $result = $plugin($value, $param, $this);
                
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
            
            // Error
            if ($result === false)
            {
                // Buscamos un mensaje de error personalizado.
                $rulePhrase = ($rule == 'is_valid') ? 'is_valid[' . $param . ']' : $rule;
                if ( isset($this->_data[$row['field']]['error'][$rulePhrase]))
                {

                    $this->_error[$row['field']] = $this->_data[$row['field']]['error'][$rulePhrase];
                }
                // Tal vez es un mensaje de error por defecto.
                else
                {
                    $rulePhrase = 'core.form_rule_' . (($rule == 'is_valid') ? 'is_valid_' . $param : $rule);
                    $this->_error[$row['field']] = Core::getPhrase($rulePhrase, array(
                            'field' => $row['label'],
                            'param' => $param,
                        )
                    );
                }
                // Si se generó un error, no tiene caso continuar validando las demás reglas.
                break;
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