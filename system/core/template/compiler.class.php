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
 * Template Compiler
 * 
 * Es el compilador que transforma las plantillas con una sintaxis similar
 * a Smarty en PHP puro. Gracias a esto la creación de plantillas se facilita
 * sin sacrificar tanto rendimiento.
 * 
 * @package     Framework\Core\Template
 * @since       1.0.0
 * @todo        Mejorar el manejo de errores de sintaxis.
 */
class Core_Template_Compiler extends Core_Template {
    
    
	/**
	 * Nombre de variables reservadas.
	 *
	 * @var string
	 */
	protected $reservedVarname = 'genos';
    
	/**
	 * Delimitador izquierdo: {
	 *
	 * @var string
	 */
	protected $leftDelim = '{';
	
	/**
	 * Delimitador derecho: {
	 *
	 * @var string
	 */
	protected $rightDelim = '}';
        
	/**
	 * Foreach stack.
	 * 
	 * @var array
	 */
	private $_foreachElseStack = array();
    
	/**
	 * Literal blocks. {literal}{/literal}
	 * 
	 * @var array
	 */
	private $_literals = array();
    
	/**
	 * String regex.
	 * 
	 * @var string
	 */
	private $_sDbQstrRegexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
	
	/**
	 * String regex.
	 * 
	 * @var string
	 */	
	private $_sSiQstrRegexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
	
	/**
	 * Bracket regex.
	 * 
	 * @var string
	 */	
	private $_sVarBracketRegexp = '\[[\$|\#]?\w+\#?\]';
	
	/**
	 * Variable regex.
	 * 
	 * @var string
	 */	
	private $_sSvarRegexp = '\%\w+\.\w+\%';
	
	/**
	 * Function regex.
	 * 
	 * @var string
	 */	
	private $_sFuncRegexp = '[a-zA-Z_]+';
    
    /**
     * Archivo de plantilla actual
     * 
     * @var string
     */
    private $_currentFile = '';
    
    /**
     * Plugins que serán cargados.
     * 
     * @var array
     */
    private $_pluginsInfo = array();
    
	/**
	 * Constructor, crea las expresiones regulares que se usarán en esta clase.
	 */
	public function __construct()
	{
        DEBUG_MODE ? Core::mark('template.compiler.init') : null;
       
		$this->_sQstrRegexp = '(?:' . $this->_sDbQstrRegexp . '|' . $this->_sSiQstrRegexp . ')';

		$this->_sDvarRegexp = '\$[a-zA-Z0-9_]{1,}(?:' . $this->_sVarBracketRegexp . ')*(?:\.\$?\w+(?:' . $this->_sVarBracketRegexp . ')*)*';

		$this->_sCvarRegexp = '\#[a-zA-Z0-9_]{1,}(?:' . $this->_sVarBracketRegexp . ')*(?:' . $this->_sVarBracketRegexp . ')*\#';

		$this->_sVarRegexp = '(?:(?:' . $this->_sDvarRegexp . '|' . $this->_sCvarRegexp . ')|' . $this->_sQstrRegexp . ')';

		$this->_sModRegexp = '(?:\|@?[0-9a-zA-Z_]+(?::(?>-?\w+|' . $this->_sDvarRegexp . '|' . $this->_sQstrRegexp .'))*)';		
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar plantilla.
     * 
     * @param string $name Nombre de la plantilla en caché.
     * @param string $data Contenido de la plantilla.
     * @param string $tplName Nombre de la plantilla.
     * 
     * @return void
     */
    public function compile($name, $data = null, $tplName = '')
    {
        // Para llevar los errores
        $this->_currentFile = str_replace(ROOT, '', $tplName);
        
        // Compilamos
        $data = $this->_compile($data);
        
		$content = '';
		$lines = explode("\n", $data);

		foreach ($lines as $line)
		{
			if (preg_match("/<\?php(.*?)\?>/i", $line))
			{
				if (substr(trim($line), 0, 5) == '<?php')
				{
					$content .= trim($line) . "\n";
				}
				else
				{
					$content .= $line . "\n";
				}
			}
			else
			{
				$content .= $line . "\n";
			}
		}

		if ($file = @fopen($name, 'w+'))
		{
			fwrite($file, $content);
			fclose($file);
		}
		else
		{
			Core_Error::trigger('No se puede cachear la plantilla, verifique que el directorio temporal y sus subcarpetas tengan permisos CHMOD 777');
		}
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar plantilla y convertir a PHP.
     * 
     * @param string $data Contenido de la plantilla.
     * 
     * @return string Contenido parseado.
     */
    private function _compile($data)
    {
		$ldq = preg_quote($this->leftDelim);
		$rdq = preg_quote($this->rightDelim);
		$text = array();
		$compiledText = '';
        
        // eliminar comentarios
        $data = preg_replace("/{$ldq}\*(.*?)\*{$rdq}/se", "", $data);
        
		// remove literal blocks
		preg_match_all("!{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}!s", $data, $matches);
		$this->_literals = $matches[1];
		$data = preg_replace("!{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}!s", stripslashes($ldq . "literal" . $rdq), $data);
        
        $text = preg_split("!{$ldq}.*?{$rdq}!s", $data);
        
		preg_match_all("!{$ldq}\s*(.*?)\s*{$rdq}!s", $data, $matches);
		$tags = $matches[1];
        
		$compiledTags = array();
		$totalCompiledTags = count($tags);
		for ($i = 0, $forMax = $totalCompiledTags; $i < $forMax; $i++)
		{
			$compiledTags[] = $this->_compileTag($tags[$i]);
		}
        
        $countCompiledTags = count($compiledTags);
		for ($i = 0, $forMax = $countCompiledTags; $i < $forMax; $i++)
		{   
			if ($compiledTags[$i] == '')
			{
				$text[$i+1] = preg_replace('~^(\r\n|\r|\n)~', '', $text[$i+1]);
			}
			$compiledText .= $text[$i].$compiledTags[$i];
		}
		$compiledText .= $text[$i];
        
		$compiledText = preg_replace('!\?>\n?<\?php!', '', $compiledText);

		$compiledHeader = '<?php /* Cached: ' . date("F j, Y, g:i a", time()) . ' */ ?>' . "\n";
        
        // Añadir los plugins solicitados
        if (count($this->_pluginsInfo) > 0)
        {
            $pluginsParams = '';
            foreach ($this->_pluginsInfo as $type => $plugins)
            {
                foreach ($plugins as $func => $key)
                {
                    $pluginsParams .= "array('{$type}', '{$func}'),";
                }
            }
            $pluginsCode = '<?php Core::getLib(\'template.plugin\')->load(array(' . rtrim($pluginsParams, ',') . '), $this); ?>' . "\n";
            $compiledHeader .= $pluginsCode;
            $this->_pluginsInfo = array();
        }
        
        $compiledText = $compiledHeader . $compiledText;

		return $compiledText;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar etiquetas personalizadas. (ej: {literal})
     * 
     * @param string $tag Nombre de la etiqueta.
     * 
     * @return string Código basado en la etiqueta.
     */
    private function _compileTag($tag)
    {
		preg_match_all('/(?:(' . $this->_sVarRegexp . '|' . $this->_sSvarRegexp . '|\/?' . $this->_sFuncRegexp . ')(' . $this->_sModRegexp . '*)(?:\s*[,\.]\s*)?)(?:\s+(.*))?/xs', $tag, $matches);

		if ($matches[1][0]{0} == '$' || $matches[1][0]{0} == "'" || $matches[1][0]{0} == '"')
		{
			return "<?php echo " . $this->_parseVariables($matches[1], $matches[2]) . "; ?>";
		}

		$tagCommand = $matches[1][0];
		$tagModifiers = !empty($matches[2][0]) ? $matches[2][0] : null;
		$tagArguments = !empty($matches[3][0]) ? $matches[3][0] : null;

        switch($tagCommand)
        {
            /**
             * Estructuras de control básicas
             */
			case 'for':
				$tagArguments = preg_replace("/\\$([A-Za-z0-9]+)/ise", "'' . \$this->_parseVariable('\$$1') . ''", $tagArguments);
				return '<?php for (' . $tagArguments . '): ?>';
				break;
			case '/for':
				return "<?php endfor; ?>";
			case 'if':
				return $this->_compileIf($tagArguments);
				break;
			case 'else':
				return "<?php else: ?>";
				break;
			case 'elseif':
				return $this->_compileIf($tagArguments, true);
				break;
			case '/if':
				return "<?php endif; ?>";
				break;
			case 'foreach':
				array_push($this->_foreachElseStack, false);
				$args = $this->_parseArgs($tagArguments);
				if (!isset($args['from']))
				{
					return '';
				}
				if (!isset($args['value']) && !isset($args['item']))
				{
					return '';
				}
				if (isset($args['value']))
				{
					$args['value'] = $this->_removeQuote($args['value']);
				}
				elseif (isset($args['item']))
				{
					$args['value'] = $this->_removeQuote($args['item']);
				}

				(isset($args['key']) ? $args['key'] = "\$this->_vars['".$this->_removeQuote($args['key'])."'] => " : $args['key'] = '');
                
                $iteration = (isset($args['name']) ? true : false);

				$result = '<?php if (count((array)' . $args['from'] . ')): ?>' . "\n";
                if ($iteration)
                {
                    $result .= '<?php $this->_tplVars[\'iteration\'][\'' . $args['name'] . '\'] = 0; ?>' . "\n";
                }
				$result .= '<?php foreach ((array) ' . $args['from'] . ' as ' . $args['key'] . '$this->_vars[\'' . $args['value'] . '\']): ?>';
                if ($iteration)
                {
                    $result .= '<?php $this->_tplVars[\'iteration\'][\'' . $args['name'] . '\']++; ?>' . "\n";
                }
				return $result;
				break;
			case 'foreachelse':
				$this->_foreachElseStack[count($this->_foreachElseStack)-1] = true;
				return "<?php endforeach; else: ?>";
				break;
			case '/foreach':
				if (array_pop($this->_foreachElseStack))
				{
					return "<?php endif; ?>";
				}
				else
				{
					return "<?php endforeach; endif; ?>";
				}
				break;
			case 'literal':
				list (,$literal) = each($this->_literals);
				return "<?php echo '" . str_replace("'", "\'", $literal) . "'; ?>\n";
				break;
            /** Cargamos plugins si existen */
            default:
                if ($this->_compileCompilerTag($tagCommand, $tagArguments, $output))
                {
                    return $output;
                }
                else if ($this->_compileCustomTag($tagCommand, $tagArguments, $tagModifiers, $output))
                {
                    return $output;
                }
                else
                {
                    $this->_syntaxError('Etiqueta no reconocida: {' . $tagCommand . '}');
                }
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Compiler Tag.
     * 
     * Manda a llamar a un plugin del tipo: compiler.name.php
     * 
     * @param string $tagCommand {etiqueta}
     * @param string $tagArgs Argumentos recibidos.
     * @param string $output Variable de salida donde guardarémos el resutado.
     * 
     * @return bool TRUE si existe el plugin, FALSE en caso contrario.
     */
    private function _compileCompilerTag($tagCommand, $tagArgs, &$output)
    {
        $found = false;
        $haveFunction = true;
        // Verificar si el plugin ya fue cargado
        if (isset($this->plugins['compiler'][$tagCommand]))
        {
            $found = true;
            $pluginFunc = $this->plugins['compiler'][$tagCommand];
            if ( ! function_exists($pluginFunc))
            {
                $message = "compiler function '$tag_command' is not implemented";
                $haveFunction = false;
            }
        }
        else if ($pluginFile = Core::getLib('template.plugin')->getPluginFilepath('compiler', $tagCommand))
        {
            $found = true;
            
            include_once $pluginFile;
            
            $pluginFunc = 'tpl_compiler_' . $tagCommand;
            
            if ( ! function_exists($pluginFunc))
            {
                $message = 'La funcion ' . $pluginFunc . ' no se encuentra en ' . str_replace(SYS_PATH, '', $pluginFile);
                $haveFunction = false;
            }
            else
            {
                $this->plugins['compiler'][$tagCommand] = $pluginFunc;
            }
        }
        
        if ($found)
        {
            if ($haveFunction)
            {
                $output = call_user_func_array($pluginFunc, array($tagArgs, &$this));
                if ($output != '')
                {
                    $output = '<?php ' . $output . ' ?>';
                }
                return true;
            }
            else
            {
                $this->_syntaxError($message, E_USER_WARNING);
            }
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar función.
     * 
     * Manda a llamar a un plugin del tipo: function.name.php
     * 
     * @param string $tagCommand {etiqueta}
     * @param string $tagArgs Argumentos recibidos.
     * @param string $tagModifier Modificador que requerimos.
     * @param string $output Variable de salida donde guardarémos el resutado.
     * 
     * @return bool TRUE si existe el plugin, FALSE en caso contrario.
     */
    public function _compileCustomTag($tagCommand, $tagArgs, $tagModifier, &$output)
    {
        $found = false;
        $haveFunction = true;
        // Verificar si el plugin ya fue cargado
        if (isset($this->plugins['function'][$tagCommand]))
        {
            $found = true;
            $pluginFunc = $this->plugins['function'][$tagCommand];
            if ( ! function_exists($pluginFunc))
            {
                $message = "custom function '$tag_command' is not implemented";
                $haveFunction = false;
            }
        }
        else if ($pluginFile = Core::getLib('template.plugin')->getPluginFilepath('function', $tagCommand))
        {
            $found = true;
            
            include_once $pluginFile;
            
            $pluginFunc = 'tpl_function_' . $tagCommand;
            
            if ( ! function_exists($pluginFunc))
            {
                $message = 'La funcion ' . $pluginFunc . ' no se encuentra en ' . str_replace(SYS_PATH, '', $pluginFile);
                $haveFunction = false;
            }
            else
            {
                $this->plugins['function'][$tagCommand] = $pluginFunc;
            }
        }
        
        if ( ! $found)
        {
            return false;
        }
        else if ( ! $haveFunction)
        {
            $this->_syntaxError($message, E_USER_WARNING);
            return true;
        }
        
        // Añadir el plugin para ser cargado en esta plantilla
        $this->_addPlugin('function', $tagCommand);
        
        $args = $this->_parseArgs($tagArgs);
        $args = $this->_compileArgList($args);
        
        $output = $this->_compilePluginCall('function', $tagCommand) . '(array(' . implode(',', $args) . '), $this)';
        
        // Existe un modificador en la función?
        if ($tagModifier != '')
        {
            $this->_parseModifier($output, $tagModifier);
        }
        
        if ($output != '')
        {
            $output = '<?php echo ' . $output . '; ?>';
        }
        
        return true;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar modificador.
     * 
     * Manda a llamar a un plugin del tipo: modifier.name.php
     * 
     * @param string $modifier Nombre del modificador.
     * @param string $args Argumentos para el modificador.
     * 
     * @return bool TRUE si existe el plugin, FALSE en caso contrario.
     */
    private function _compileModifier($modifier, $args)
    {
        $found = false;
        $haveFunction = true;
        // Verificar si el plugin ya fue cargado
        if (isset($this->plugins['modifier'][$modifier]))
        {
            $found = true;
            $pluginFunc = $this->plugins['modifier'][$modifier];
            if ( ! function_exists($pluginFunc))
            {
                $message = "modifier function '$tag_command' is not implemented";
                $haveFunction = false;
            }
        }
        else if ($pluginFile = Core::getLib('template.plugin')->getPluginFilepath('modifier', $modifier))
        {
            $found = true;
            
            include_once $pluginFile;
            
            $pluginFunc = 'tpl_modifier_' . $modifier;
            
            if ( ! function_exists($pluginFunc))
            {
                $message = 'La funcion ' . $pluginFunc . ' no se encuentra en ' . str_replace(SYS_PATH, '', $pluginFile);
                $haveFunction = false;
            }
            else
            {
                $this->plugins['modifier'][$modifier] = $pluginFunc;
            }
        }
        
        if ($found)
        {
            if ($haveFunction)
            {
                $this->_addPlugin('modifier', $modifier);
                
                return '' . $pluginFunc . '(' . $args . ')';
            }
            else
            {
                $this->_syntaxError($message);
            }
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
    
   	/**
	 * Parsear argumentos.
     * 
     * (ej. {tag bar1=sample1 bar2=sample2}
	 *
	 * @param string $arguments Argumentos obtenidos.
     * 
	 * @return array Arreglo con todos los argumentos.
	 */
	public function _parseArgs($arguments)
	{
		$result	= array();
		preg_match_all('/(?:' . $this->_sQstrRegexp . ' | (?>[^"\'=\s]+))+|[=]/x', $arguments, $matches);

		$state= 0;
		foreach($matches[0] as $value)
		{
			switch($state)
			{
				case 0:
					if (is_string($value))
					{
						$name = $value;
						$state= 1;
					}
					else
					{
						$this->_syntaxError("Nombre de atributo no válido");
					}
					break;
				case 1:
					if ($value == '=')
					{
						$state= 2;
					}
					else
					{
						 $this->_syntaxError("Esperando '=' después de '{$lastValue}'");
					}
					break;
				case 2:
					if ($value != '=')
					{
						if(!preg_match_all('/(?:(' . $this->_sVarRegexp . '|' . $this->_sSvarRegexp . ')(' . $this->_sModRegexp . '*))(?:\s+(.*))?/xs', $value, $variables))
						{
							$result[$name] = $value;
						}
						else
						{
							$result[$name] = $this->_parseVariables($variables[1], $variables[2]);
						}
						$state= 0;
					}
					else
					{
						$this->_syntaxError("'=' no puede ser un valor de atributo");
					}
					break;
			}
			$lastValue = $value;
		}

		if($state!= 0)
		{
			if($state== 1)
			{
				$this->_syntaxError("Esperando '=' después del nombre de atributo '{$lastValue}'");
			}
			else
			{
				$this->_syntaxError("Falta valor del atributo");
			}
		}

		return $result;
	}
    
    // --------------------------------------------------------------------
    
	/**
	 * Compilar declaraciones IF
	 *
	 * @param string $arguments Argumentos del IF.
	 * @param bool $elseIf TRUE si se trata de un ELSEIF.
	 * @param bool $while TRUE si se trata de un ciclo WHILE.
     * 
	 * @return string Devuelve el código PHP del IF.
	 */
	private function _compileIf($arguments, $elseIf = false, $while = false)
	{
		$result = "";
		$args = array();
		$argStack	= array();

		preg_match_all('/(?>(' . $this->_sVarRegexp . '|\/?' . $this->_sSvarRegexp . '|\/?' . $this->_sFuncRegexp . ')(?:' . $this->_sModRegexp . '*)?|\-?0[xX][0-9a-fA-F]+|\-?\d+(?:\.\d+)?|\.\d+|!==|===|==|!=|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\%|\+|\-|\/|\*|\@|\b\w+\b|\S+)/x', $arguments, $matches);
		$args = $matches[0];

		$countArgs = count($args);
		for ($i = 0, $forMax = $countArgs; $i < $forMax; $i++)
		{
			$arg = &$args[$i];
			switch (strtolower($arg))
			{
				case '!':
				case '%':
				case '!==':
				case '==':
				case '===':
				case '>':
				case '<':
				case '!=':
				case '<>':
				case '<<':
				case '>>':
				case '<=':
				case '>=':
				case '&&':
				case '||':
				case '^':
				case '&':
				case '~':
				case ')':
				case ',':
				case '+':
				case '-':
				case '*':
				case '/':
				case '@':
					break;
				case 'eq':
					$arg = '==';
					break;
				case 'ne':
				case 'neq':
					$arg = '!=';
					break;
				case 'lt':
					$arg = '<';
					break;
				case 'le':
				case 'lte':
					$arg = '<=';
					break;
				case 'gt':
					$arg = '>';
					break;
				case 'ge':
				case 'gte':
					$arg = '>=';
					break;
				case 'and':
					$arg = '&&';
					break;
				case 'or':
					$arg = '||';
					break;
				case 'not':
					$arg = '!';
					break;
				case 'mod':
					$arg = '%';
					break;
				case '(':
					array_push($argStack, $i);
					break;
				case 'is':
					$isArgCount = count($args);
					$isArg = implode(' ', array_slice($args, 0, $i - 0));
					$argTokens = $this->_compileParseIsExpr($isArg, array_slice($args, $i+1));
					array_splice($args, 0, count($args), $argTokens);
					$i = $isArgCount - count($args);
					break;
				default:
					preg_match('/(?:(' . $this->_sVarRegexp . '|' . $this->_sSvarRegexp . '|' . $this->_sFuncRegexp . ')(' . $this->_sModRegexp . '*)(?:\s*[,\.]\s*)?)(?:\s+(.*))?/xs', $arg, $matches);

					if (isset($matches[0]{0}) && ($matches[0]{0} == '$' || $matches[0]{0} == "'" || $matches[0]{0} == '"'))
					{
						$arg = $this->_parseVariables(array($matches[1]), array($matches[2]));
					}

					break;
			}
		}

		if($while)
		{
			return implode(' ', $args);
		}
		else
		{
			if ($elseIf)
			{
				return '<?php elseif ('.implode(' ', $args).'): ?>';
			}
			else
			{
				return '<?php if ('.implode(' ', $args).'): ?>';
			}
		}

		return $result;
	}
    
    // --------------------------------------------------------------------
    
	/**
	 * Parsear variables.
	 *
	 * @param array $variables Arreglo de variables.
	 * @param array $modifiers Arreglo de modificadires.
     * 
	 * @return string Variable modificada.
	 */
	private function _parseVariables($variables, $modifiers)
	{
		$result = "";
		foreach($variables as $key => $value)
		{
			if (empty($modifiers[$key]))
			{
				$result .= $this->_parseVariable(trim($variables[$key])).'.';
			}
			else
			{
				$result .= $this->_parseModifier($this->_parseVariable(trim($variables[$key])), $modifiers[$key]).'.';
			}
		}
		return substr($result, 0, -1);
	}
    
    // --------------------------------------------------------------------
    
	/**
	 * Parsear una variable específica
	 *
	 * @param string $variable Nombre de la variable que se está analizando.
     * 
	 * @return string Variable modificada.
	 */
	private function _parseVariable($variable)
	{
		if ($variable{0} == "\$")
		{
			return $this->_compileVariable($variable);
		}
		else
		{
			return $variable;
		}
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar todas las variables
     * 
     * @param string $variable Nombre de variable.
     * 
     * @return string Variable modificada.
     */
    private function _compileVariable($variable)
    {
		$result = '';
		$variable = substr($variable, 1);

		preg_match_all('!(?:^\w+)|(?:' . $this->_sVarBracketRegexp . ')|\.\$?\w+|\S+!', $variable, $matches);
		$variables = $matches[0];
		$varName = array_shift($variables);
        
        if ($varName == $this->reservedVarname)
        {
			if ($variables[0]{0} == '[' || $variables[0]{0} == '.')
			{
				$find = array("[", "]", ".");
				switch(strtoupper(str_replace($find, "", $variables[0])))
				{
					case 'GET':
						$result = "\$_GET";
						break;
					case 'POST':
						$result = "\$_POST";
						break;
					case 'COOKIE':
						$result = "\$_COOKIE";
						break;
					case 'ENV':
						$result = "\$_ENV";
						break;
					case 'SERVER':
						$result = "\$_SERVER";
						break;
					case 'SESSION':
						$result = "\$_SESSION";
						break;
					default:
						$var = str_replace($find, "", $variables[0]);
						$result = "\$this->_tplVars['$var']";
						break;
				}
				array_shift($variables);
			}
			else
			{
				$this->_syntaxError('$' . $varName.implode('', $variables) . ' es una referencia $tpl no válida', E_USER_ERROR);
			}
        }
        else 
        {
            $result = "\$this->_vars['$varName']";
        }
        
		foreach ($variables as $var)
		{
			if ($var{0} == '[')
			{
				$var = substr($var, 1, -1);
				if (is_numeric($var))
				{
					$result .= "[$var]";
				}
				elseif ($var{0} == '$')
				{
					$result .= "[" . $this->_compileVariable($var) . "]";
				}
			}
			elseif ($var{0} == '.')
			{
   				$result .= "['" . substr($var, 1) . "']";
			}
			elseif (substr($var,0,2) == '->')
			{
				$this->_syntaxError('Llamar a miembros de objetos no está permitido');
			}
			else
			{
				$this->_syntaxError('$' . $varName.implode('', $variables) . ' es una referencia no válida');
			}
		}
        
		return $result;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Parsear modificadores.
     * 
     * @param string $variable Variable.
     * @param string $modifiers Modificador.
     * 
     * @return string Variable modificada.
     */
    private function _parseModifier($variable, $modifiers)
    {
		$mods = array();
		$args = array();

		$mods = explode('|', $modifiers);
		unset($mods[0]);
		foreach ($mods as $mod)
		{
			$args = array();
			if (strpos($mod, ':'))
			{
				$parts = explode(':', $mod);
				$cnt = 0;

				foreach ($parts as $key => $part)
				{
					if ($key == 0)
					{
						continue;
					}

					if ($key > 1)
					{
						$cnt++;
					}

					$args[$cnt] = $this->_parseVariable($part);
				}

				$mod = $parts[0];
			}

			if ($mod{0} == '@')
			{
				$mod = substr($mod, 1);
				$mapArray = false;
			}
			else
			{
				$mapArray = true;
			}

			$arg = ((count($args) > 0) ? ', '.implode(', ', $args) : '');
            
            if (function_exists($mod))
            {
                $variable = '' . $mod . '(' . $variable . $arg . ')';
            }
            else
            {
                $tmp = $this->_compileModifier($mod, $variable . $arg);
            
                if ( ! $tmp)
                {
                    $this->_syntaxError('No se encontró el modificador: ' . $mod);
                }
                else
                {
                    $variable = $tmp;
                }
            }
        }
        
        return $variable;
    }
    
    // --------------------------------------------------------------------
    
	/**
	 * Remover quotes de las variables PHP.
	 *
	 * @param string $string Variable PHP para trabajar.
     * 
	 * @return string Variable PHP modificada.
	 */
	public function _removeQuote($string)
	{
		if (($string{0} == "'" || $string{0} == '"') && $string{strlen($string)-1} == $string{0})
		{
			return substr($string, 1, -1);
		}
		else
		{
			return $string;
		}
	}
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar lista de atributos de una etiqueta
     * 
     * @param array $args Arreglo de argumentos.
     * 
     * @return array Lista de argumentos.
     */
    private function _compileArgList($args)
    {
        $argList = array();
        foreach ($args as $name => $value)
        {
            $argList[] = "'$name' => $value";
        }
        
        return $argList;
    }
    
    
    // --------------------------------------------------------------------
    
    /**
     * Compilar nombre de la función del plugin.
     * 
     * @param string $type Tipo de función.
     * @param string $name Nombre de la función.
     * 
     * @return string Nuevo nombre de la función.
     */
    private function _compilePluginCall($type, $name)
    {
        if ( isset($this->plugins[$type][$name]))
        {
            return $this->plugins[$type][$name];
        }
        else
        {
            return 'tpl_' . $type . '_' . $name;
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Añadir un plugin para ser agregado
     * 
     * @param string $type Tipo de plugin. (function, modifier, compiler)
     * @param string $name Nombre del plugin.
     * @return void
     */
    private function _addPlugin($type, $name)
    {
        if ( ! isset($this->_pluginsInfo[$type]))
        {
            $this->_pluginsInfo[$type] = array();
        }
        
        if ( ! isset($this->_pluginsInfo[$type][$name]))
        {
            $this->_pluginsInfo[$type][$name] = true;
        }
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Manejar los errores de sintaxis de la plantilla
     * 
     * @param string $errorMsg Mensaje de error.
     * @param int $errorType Tipo de error.
     * 
     * @return bool Si no es un error de tipo E_USER_ERROR retorna FALSE.
     */
    private function _syntaxError($errorMsg, $errorType = E_USER_ERROR)
    {
        $errorMsg = '[' . $this->_currentFile . ']: ' . $errorMsg;
        
        trigger_error($errorMsg, $errorType);
        
		if ($errorType == E_USER_ERROR)
		{
			exit;
		}
        
        return false;
    }
}