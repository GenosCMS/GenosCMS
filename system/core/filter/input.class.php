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
 * Filter Input
 * 
 * Esta clase nos permite filtrar el contenido de entrada. Por lo general
 * se trata del contenido que será guardado en la base de datos.
 * 
 * @package     Framework\Core\Filter
 * @since       1.0.0
 */
class Core_Filter_Input {
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('filter.input.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Limpiar contenido malicioso o mal espesificado.
     * 
     * @param mixed $source Contenido que será filtrado.
     * @param string $type Tipo de variable que debería ser (INT, UINT, FLOAT, BOOLEAN, WORD, ALNUM, CMD, BASE64, STRING, ARRAY, PATH, NONE)
     * 
     * @return mixed Contenido 'limpio'
     */
    public function clean($source, $type = '')
    {
		// Tipo de restricción
		switch (strtoupper($type))
		{
			case 'INT':
			case 'INTEGER':
				// Usamos sólo el primer valor entero
				preg_match('/-?[0-9]+/', (string) $source, $matches);
				$result = @ (int) $matches[0];
				break;

			case 'UINT':
				// Usamos sólo el primer valor entero
				preg_match('/-?[0-9]+/', (string) $source, $matches);
				$result = @ abs((int) $matches[0]);
				break;

			case 'FLOAT':
			case 'DOUBLE':
				// Usamos sólo el primer valor de punto flotante
				preg_match('/-?[0-9]+(\.[0-9]+)?/', (string) $source, $matches);
				$result = @ (float) $matches[0];
				break;

			case 'BOOL':
			case 'BOOLEAN':
				$result = (bool) $source;
				break;

			case 'WORD':
				$result = (string) preg_replace('/[^A-Z_]/i', '', $source);
				break;

			case 'ALNUM':
				$result = (string) preg_replace('/[^A-Z0-9]/i', '', $source);
				break;

			case 'CMD':
				$result = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $source);
				$result = ltrim($result, '.');
				break;

			case 'BASE64':
				$result = (string) preg_replace('/[^A-Z0-9\/+=]/i', '', $source);
				break;

			case 'STRING':
				$result = (string) $this->_remove($this->_decode((string) $source));
				break;

			case 'HTML':
				$result = (string) $this->_remove((string) $source);
				break;

			case 'ARRAY':
				$result = (array) $source;
				break;

			default:
				// Are we dealing with an array?
				if (is_array($source))
				{
					foreach ($source as $key => $value)
					{
						// Filter element for XSS and other 'bad' code etc.
						if (is_string($value))
						{
							$source[$key] = $this->_remove($this->_decode($value));
						}
					}
					$result = $source;
				}
				else
				{
					// Or a string?
					if (is_string($source) && !empty($source))
					{
						// Filter source for XSS and other 'bad' code etc.
						$result = $this->_remove($this->_decode($source));
					}
					else
					{
						// Not an array or string.. return the passed parameter
						$result = $source;
					}
				}
				break;
		}

		return $result;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Tratar de convertir a texto plano.
     * 
     * @param string $source
     * 
     * @return string Texto plano.
     */
	private function _decode($source)
	{
		static $ttr;

		if (!is_array($ttr))
		{
			// Entity decode
			$trans_tbl = get_html_translation_table(HTML_ENTITIES);
			foreach ($trans_tbl as $k => $v)
			{
				$ttr[utf8_encode($k)] = $v;
                //$ttr[$v] = utf8_encode($k);
			}
		}
		$source = strtr($source, $ttr);

		// Convert decimal
		$source = preg_replace('/&#(\d+);/me', "utf8_encode(chr(\\1))", $source); // decimal notation

		// Convert hex
		$source = preg_replace('/&#x([a-f0-9]+);/mei', "utf8_encode(chr(0x\\1))", $source); // hex notation
		return $source;
	}
        
    // --------------------------------------------------------------------
    
    /**
     * Simple XSS clean
     * 
     * Método interno para eliminar iterativamente todas las etiquetas
     * y atributos no deseados.
     * 
     * @todo Se puede mejorar pero por ahora funciona bien.
     * 
     * @param string $source Contenido que vamos a limpiar.
     * 
     * @return string Contenido filtrado para XSS.
     */
	private function _remove($source)
	{
        // &entity\n;
        $source = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $source);
        $source = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $source);
 
        // Eliminar cualquier atributo que empiece con "on" o xmlns
        $source = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $source);
 
        // Eliminar protocolos javascript: y vbscript:
        $source = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $source);
        $source = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $source);
        $source = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $source);
 
        // Sólo funciona en IE: <span style="width: expression(alert('Ping!'));"></span>
        $source = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $source);
        $source = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $source);
        $source = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $source);
        
        // Eliminar: Namespace Elements
        $source = preg_replace('#</*\w+:\w[^>]*+>#i', '', $source);

        // Eliminar las etiquetas realmente no deseadas
        do
        {
            $old_data = $source;
            $source = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $source);
        }
        while ($old_data !== $source);
 
        // hemos terminado...
        return $source;
	}
}