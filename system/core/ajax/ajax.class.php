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
 * Ajax
 * 
 * Esta clase resuelve y ejecuta solicitudes a componentes de tipo AJAX.
 * 
 * @package     Framework\Core\Ajax
 * @since       1.0.0
 */
class Core_Ajax {
    
    /**
     * Llamadas JavaScript creadas por el componente.
     * 
     * @var array
     */
    private static $_calls = array();
    
    /**
     * Componente solicitado.
     * 
     * @var string
     */
    private $_component = '';
    
    /**
     * Estas son las funciones jQuery que soporta esta clase.
     * 
     * @var array
     */
	private $_jquery = array(
		'addClass',
		'removeClass',
		'val',
		'focus',
		'show',
		'remove',
		'hide',
		'slideDown',
		'slideUp',
		'submit',
		'attr',
		'height',
		'width',
		'after',
		'before',
		'fadeOut'
	);
    
    /**
     * Alias de objectos.
     * 
     * Esto nos permite acceder a las librerías más comunes de una manera más cómoda.
     * 
     * @var array
     */
    private $_objects = array(
        'input' => 'input',
        'url' => 'url'
    );
    
    // --------------------------------------------------------------------
    
    /**
     * Método mágico.
     * 
     * @param string $name Nombre de la variable.
     * 
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->_objects[$name]))
        {
            return Core::getLib($this->_objects[$name]);
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Determinar componente solicitado.
     * 
     * @return void
     */
    public function setController()
    {
        $url = Core::getLib('url');
                        
        $url->setAjaxRouting();
        
        $segments = Core::getLib('url')->getSegments();
        
        // Se requieren al menos 2 segmentos /module/method/
        if (count($segments) < 2)
        {
            exit('[error] La solicitud no es v&aacute;lida.');
        }
        
        // Asignación del módulo
        $this->_component = $segments[1];
        
        // Asignación del método/controlador
        for ($i = 2; $i <= count($segments); $i++)
        {
            $this->_component .= '/' . $segments[$i];
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Genera el componente y procesa la solicitud.
     * 
     * @return void
     */
    public function getController()
    {
        // Creamos el nombre del componente.
        $component = $this->_component;
        $component = str_replace(Core::getParam('core.url_ajax_suffix'), '', $component);
        $component = str_replace('/', '.', $component);
        
        
        // Mandamos a llamar al componente.
        Core::getLib('module')->getComponent($component, array('noTemplate' => true), 'ajax');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Crear una llamada a código JavaScript.
     * 
     * Se utiliza para generar cualquier código JavaScript cuando regresemos
     * al navegador una vez que la rutina de AJAX se ha completado.
     * 
     * <code>
     * $this->call("document.getElementById('test').style.display = 'none';");
     * $this->call('$("#test").hide();');
     * </code>
     * 
     * @param string $call Código JavaScript que se va a ejecutar.
     * 
     * @return object
     */
    public function call($call)
    {
        self::$_calls[] = $call;
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    // --------------------------------------------------------------------
    //                         Integración con jQuery
    // --------------------------------------------------------------------
    
    // --------------------------------------------------------------------
    
    /**
     * jQuery html()
     * 
     * @param string $id ID del elemento en el DOM dónde agregarémos el contenido.
     * @param string $html Contenido HTML/texto que vamos a agregar.
     * @param string $extra Funciones jQuery extra que aplicarémos al elemento.
     * 
     * @return Core_Ajax
     */
    public function html($id, $html, $extra = '')
    {
		$html = str_replace('\\', '\\\\', $html);
		$html = str_replace('"', '\"', $html);
        
        $this->call("$('" . $id . "').html(\"" . $html . "\")" . $extra . ";");
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * jQuery prepend()
     * 
     * @param string $id ID del elemento en el DOM dónde agregarémos el contenido.
     * @param string $html Contenido HTML/texto que vamos a agregar.
     * @param string $extra Funciones jQuery extra que aplicarémos al elemento.
     * 
     * @return Core_Ajax
     */
    public function prepend($id, $html, $extra = '')
    {
        $html = str_replace(array("\n", "\t"), '', $html);
        $html = str_replace('\\', '\\\\', $html);
        $html = str_replace('"', '\"', $html);
        
        $this->call("$('" . $id . "').prepend(\"" . $html . "\")" . $extra . ";");
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * jQuery append()
     * 
     * @param string $id ID del elemento en el DOM dónde agregarémos el contenido.
     * @param string $html Contenido HTML/texto que vamos a agregar.
     * @param string $extra Funciones jQuery extra que aplicarémos al elemento.
     * 
     * @return Core_Ajax
     */
    public function append($id, $html, $extra = '')
    {
        $html = str_replace(array("\n", "\t"), '', $html);
        $html = str_replace('\\', '\\\\', $html);
        $html = str_replace('"', '\"', $html);
        
        $this->call("$('" . $id . "').append(\"" . $html . "\")" . $extra . ";");
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Método mágico para emular las funciones jQuery
     * 
     * @param string $method Nombre del método jQuery.
     * @param array $arguments Argumentos que se enviarán al método jQuery.
     * 
     * @return Core_Ajax
     */
    public function __call($method, $arguments)
    {
        if ( ! in_array($method, $this->_jquery))
        {
            exit('[error] '. $method . ' No es una funci&oacute;n jQuery v&aacute;lida.');
        }
        
        $args = '';
        foreach ($arguments as $key => $arg)
        {
            // El primer parámetro es el nombre del elemento en el DOM
            if($key == 0)
            {
                continue;
            }
            
            $value = '\'' . str_replace("'", "\'", $arg) . '\'';
			if (is_bool($arg))
			{
				$value = ($arg === true ? 'true' : 'false');
			}
            
            $args .= $value . ',';
        }
        
        $args = rtrim($args, ',');
        
        $this->call('$(\'' . $arguments[0] . '\').' . $method . '(' . $args . ');');
        
        return $this;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar contenido generado por las plantillas de un bloque.
     * 
     * El sistema está diseñado para mostrar automáticamente los datos de 
     * los bloques y dentro de una llamada AJAX con ayuda de esta función
     * podemos obtener desde el bufer el contenido generado por la plantilla
     * de un bloque y asignarlo a un elemento HTML.
     * 
     * <code>
     * Core::getBlock('core.user-info');
     * $this->html('#result', $this->getContent(), '.show()');
     * </code>
     * 
     * @param $clean Se establece en TRUE si se debe tratar de limpiar el contenido en función de cómo se va a devolver.
     * 
     * @return string Devuelve la salida, lo que le permite utilizarlo en cualquier forma que desee.  
     */
	public function getContent($clean = false)
	{
		$content = ob_get_contents();
	
		ob_clean();		
	
		if ($clean)
		{
			$content = str_replace(array("\n", "\t"), '', $content);					
			$content = str_replace('\\', '\\\\', $content);
			$content = str_replace("'", "\'", $content);
			$content = str_replace('"', '\"', $content);
		}
        
		return $content;
	}
    
    
    // --------------------------------------------------------------------
    
    /**
     * Devuelve el resultado generado por el componente.
     * 
     * Todas las llamadas JavaScript creadas por el componente son retornadas
     * al navegador y son interpretadas como código JavaScript.
     * 
     * @return string Código JavaScript.
     */
    public function result()
    {
        $xml = '';
        foreach (self::$_calls as $call)
        {
            $xml .= $this->_ajaxSafe($call);
        }
        
        return $xml;   
    }
    
    // --------------------------------------------------------------------
    
	/**
	 * Safe AJAX Code.
     * 
     * Elimina los caracteres de salto de linea.
	 * 
	 * @param string $str Cadena a limpiar.
     * 
	 * @return string Cadena limpia.
	 */
	private function _ajaxSafe($str)
	{
		$str = str_replace(array("\n", "\r"), '\\n', $str);

		return $str;
	}
}