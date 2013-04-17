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
 * Form Helper
 * 
 * Nos ayuda a generar facilmente campos de formulario. 
 * 
 * Trabaja en conjunto con los plugin de plantilla:
 * 
 * {form_checkbox}
 * {form_input}
 * {form_radio}
 * {form_select}
 * 
 * @package     Framework\Core\Form
 * @since       1.0.0
 * @final
 */
class Core_Form_Helper {
 
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('form.helper.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar un campo de texto estándar.
     * 
     * @param string $name Nombre del campo
     * @param string $value Valor que tendrá el campo.
     * @param string $type Tipo de campo
     * @param string $extra Atributos extra del campo.
     * 
     * @return string Código HTML del campo.
     */
    public function formInput($name, $value = '', $type = '', $extra = '')
    {
        // Campos de texto normales
        if ($type == 'textarea')
        {
            $defaults = array('name' => $name);
            
            return '<textarea ' . $this->_parseFormAttributes($name, $defaults) . $extra . '>' . $this->_setValue($name, $value) . '</textarea>';
        }
        else
        {
            $defaults = array('type' => $type, 'name' => $name, 'value' => $this->_setValue($name, $value));
            
            return '<input ' . $this->_parseFormAttributes($name, $defaults) . $extra . ' />';
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar campo tipo select
     * 
     * @param string $name Nombre del campo select.
     * @param array $options Opciones del select.
     * @param array $selected Valores seleccionados por defecto.
     * @param string $extra Atributos extra del campo.
     * 
     * @return string Código HTML del campo.
     */
    public function formSelect($name, $options = array(), $selected = array(), $extra = '')
    {
        if ( ! is_array($selected))
        {
            $selected = array($selected);
        }
        
        // Si no hay un estado seleccionado vamos a tratar de establecerlo automáticamente
        if (count($selected) === 0)
        {
            if (isset($_POST[$name]))
            {
                $selected = array($_POST[$name]);
            }
        }
        
        if ($extra != '') $extra = ' '.$extra;
        
        $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === false) ? ' multiple="multiple"' : '';
        
        $form = '<select name="'.$name.'"'.$extra.$multiple.">\n";
        
        foreach ($options as $key => $val)
        {
			$key = (string) $key;

			if (is_array($val) && ! empty($val))
			{
				$form .= '<optgroup label="'.$key.'">'."\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';

					$form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
				}

				$form .= '</optgroup>'."\n";
			}
			else
			{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

				$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
        }
        
        $form .= '</select>';
        
        return $form;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Generar un campo tipo 'checkbox' ó 'radio'
     * 
     * @param string $name Nombre del campo.
     * @param string $value Valor del campo.
     * @param bool $checked TRUE genera un campo selecionado por defecto.
     * @param string $extra Atributos extra del campo.
     * 
     * @return string Código HTML del campo.
     */
    public function formOption($name, $value = '', $checked = false, $type = '', $extra = '')
    {
        $defaults = array('type' => $type, 'name' => $name, 'value' => $value);
        
        $checked = $this->_setOption($name, $value, $checked);
        
        if ($checked == true)
        {
            $defaults['checked'] = 'checked';
        }
        
        return '<input ' . $this->_parseFormAttributes($name, $defaults) . $extra . ' />';
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Establecer valor para un campo de texto o un area de texto.
     * 
     * Buscamos el valor dentro del Request
     * 
     * @param string $field Nombre del campo.
     * @param string $default Valor por defecto.
     * 
     * @return string Valor del campo.
     */
	private function _setValue($field = '', $default = '')
	{
        if (Core::getLib('form')->count() == 0)
        {
            if ( ! isset($_POST[$field]))
            {
                return $default;
            }
            
            return $this->_formPrep($_POST[$field]);
        }
        
        return $this->_formPrep(Core::getLib('form')->setValue($field, $default), $field);
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Establecer si el campo estará selecionado.
     * 
     * @param string $field Nombre del campo.
     * @param string $value Valor del campo.
     * @param bool $default TRUE genera un campo selecionado por defecto.
     * 
     * @return bool El campo irá seleccionado o no. (TRUE/FALSE)
     */
    private function _setOption($field, $value = '', $default = false)
    {
        if (count($_POST) == 0)
        {
            return $default;
        }
        
        if (Core::getLib('form')->count() == 0)
        {
            // No se envió el parámetro
            if ( ! isset($_POST[$field]))
            {
                if (count($_POST) === 0 && $default == true)
                {
                    return true;
                }
                
                return false;
            }
            
            $field = $_POST[$field];
            
            if ( ($field == '' || $value == '') || ($field != $value))
            {
                return false;
            }
            
            return true;
        }
        
        return Core::getLib('form')->setOption($field, $value, $default);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Analizar atributos de un campo.
     * 
     * @param array $attrs Lista de atributos.
     * @param array $default Atributos que será agregados por defecto.
     * 
     * @return string Atributos en formato: var="value"
     */
    private function _parseFormAttributes($attributes, $default)
    {
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key == 'value')
			{
				$val = $this->_formPrep($val, $default['name']);
			}

			$att .= $key . '="' . $val . '" ';
		}

		return $att;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Preparar texto.
     * 
     * Esto para ser mostrado con seguridad en el sitio, por si tiene etiquetas HTML.
     * 
     * @param string $str Texto a limpiar.
     * @param string $fieldName Nombre del campo.
     * 
     * @return string Texto limpio.
     */
	private function _formPrep($str = '', $fieldName = '')
	{
		static $preppedFields = array();

		// if the field name is an array we do this recursively
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->_formPrep($val);
			}

			return $str;
		}

		if ($str === '')
		{
			return '';
		}

		// we've already prepped a field with this name
		// @todo need to figure out a way to namespace this so
		// that we know the *exact* field and not just one with
		// the same name
		if (isset($preppedFields[$fieldName]))
		{
			return $str;
		}

		$str = htmlspecialchars($str);

		// In case htmlspecialchars misses these.
		$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);

		if ($fieldName != '')
		{
			$preppedFields[$fieldName] = $fieldName;
		}

		return $str;
	}
}