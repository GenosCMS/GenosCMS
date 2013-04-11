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
 * XML Parser
 * 
 * Leer y convertir un archivo XML a un ARRAY
 * 
 * @package     Framework\Core\Xml
 * @since       1.0.0
 * @final
 */
class Core_Xml_Parser {
    
    /**
     * Objeto XML
     * 
     * @var object
     */
    private $_xml = null;
    
    /**
     * Código XML
     * 
     * @var string
     */
    private $_xmlCode = '';
    
    /**
     * Datos del archivo XML en un ARRAY
     * 
     * @var array
     */
    private $_data = array();
    
    /**
     * Pila XML
     * 
     * @var array
     */
    private $_stackXml = array();
    
    /**
     * CDATA
     * 
     * @var string
     */
    private $_cdata = '';
    
    /**
     * CDATA Find
     * 
     * @var array
     */
    private $_cdataFind = array('�![CDATA[', ']]�', "\r\n", "\n");
    
    /**
     * CDATA Replace
     * 
     * @var array
     */
    private $_cdataReplace = array('<![CDATA[', ']]>', "\n", "\r\n");
    
    /**
     * Total de etiquetas
     * 
     * @var int
     */
    private $_tagCnt = 0;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('xml.parser.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener el contenido de un archivo XML
     * 
     * @param string $file Ruta del archivo XML.
     * 
     * @return string Contenido del archivo XML.
     */
    public function getXml($file)
    {
        // Comprobamos que sea un archivo...
        if ( ! preg_match("/<(.*?)>/i", $file) && file_exists($file))
        {
            return file_get_contents($file);
        }
        
        return $file;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Analizar código XML y convertirlo a un ARRAY
     * 
     * @param string $file Archivo XML.
     * @param string $encoding Tipo de codificación.
     * 
     * @return array Contenido del archivo XML convertido en un ARRAY.
     */
    public function parse($file, $encoding = 'ISO-8859-1')
    {
        $this->_xmlCode = $this->getXml($file);
        
        if (empty($this->_xmlCode))
        {
            return false;
        }
        
        if ( ! ($this->_xml = xml_parser_create($encoding)))
        {
            return false;
        }
        
        xml_parser_set_option($this->_xml, XML_OPTION_SKIP_WHITE, 0);
        xml_parser_set_option($this->_xml, XML_OPTION_CASE_FOLDING, 0);
        xml_set_character_data_handler($this->_xml, array(&$this, '_characterData'));
        xml_set_element_handler($this->_xml, array(&$this, '_startElement'), array(&$this, '_stopElement'));
        
        xml_parse($this->_xml, $this->_xmlCode);
        
        xml_parser_free($this->_xml);
        
        return $this->_data;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Manejar el inicio de un elemento.
     * 
     * @param object $parser Parser.
     * @param string $name Nombre del elemento.
     * @param array $attrs Atributos.
     * 
     * @return void
     */
    private function _startElement(&$parser, $name, $attrs)
    {   
        $this->_cdata = '';
        
        array_unshift($this->_stackXml, array(
            'name' => $name,
            'attrs' => $attrs,
            'total' => ++$this->_tagCnt
        ));
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Manejar el final de un elemento.
     * 
     * @param object $parser Parser
     * @param string $name Nombre del elemento.
     * 
     * @return void
     */
    private function _stopElement(&$parser, $name)
    {
        $tag = array_shift($this->_stackXml);
        
        if ($tag['name'] != $name)
        {
            return;
        }
        
        $xmlData = $tag['attrs'];
        
        if (trim($this->_cdata) === '' || $tag['total'] == $this->_tagCnt)
        {
            if (count($xmlData) == 0)
            {
                $xmlData = $this->_unescapeCdata($this->_cdata);
            }
            else
            {
                $this->_addNode($xmlData, 'value', $this->_unescapeCdata($this->_cdata));
            }
        }
        
        if (isset($this->_stackXml[0]))
        {
            $this->_addNode($this->_stackXml[0]['attrs'], $name, $xmlData);
        }
        else
        {
            $this->_data = $xmlData;
        }
        
        $this->_cdata = '';
    }
    
    
    // --------------------------------------------------------------------
    
    /**
     * Manejar CDATA almacenándolo en una variable.
     * 
     * @param object $parser Parser.
     * @param string $cdata Contenido.
     * 
     * @return void
     */
    private function _characterData(&$parser, $cdata)
    {
        $this->_cdata .= $cdata;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Agregar nodos hijo
     * 
     * @param array $node Nodo.
     * @param string $name Nombre del nuevo nodo.
     * @param string $value Valor del nuevo nodo.
     * 
     * @return void
     */
    private function _addNode(&$node, $name, $value)
    {
        if ( ! is_array($node) || ! in_array($name, array_keys($node)))
        {
            $node[$name] = $value;
        }
        else if (is_array($node[$name]) && isset($node[$name][0]))
        {
            $node[$name][] = $value;
        }
        else
        {
            $node[$name] = array($node[$name]);
            $node[$name][] = $value; 
        }
    }
    
    /**
     * Escapar CDATA
     * 
     * @param string $cdata CDATA
     * 
     * @return string Texto escapado. 
     */
    private function _unescapeCdata($cdata)
    {
        return str_replace($this->_cdataFind, $this->_cdataReplace, $cdata);
    }
}