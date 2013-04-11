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
 * Language
 * 
 * Esta clase nos permite el uso de idiomas dentro del sistema.
 * 
 * @package     Framework\Core\Language
 * @since       1.0.0
 */
class Core_Language {
    
    /**
     * Idioma cargado por default
     * 
     * @var string
     */
    private $_deftLang = 'es_MX';
    
    /**
     * Listado de variables de idioma.
     * 
     * @var array
     */
    private $_vars = array();
    
    /**
     * Listado de archivos de idioma cargados.
     * 
     * @var array
     */
    private $_langs = array();
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('language.init') : null;
        
        // Definimos el lenguage por defecto
        $deft_lang = Core::getParam('core.language');
        $idiom = empty($deft_lang) ? 'es_MX' : $deft_lang;
        
        $this->_deftLang = $idiom;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener frase del archivo de idioma de un módulo.
     * 
     * @param string $index Formato de la frase que será cargada.
     * @param array $params Parámetros que serán reemplazados en la frase.
     * @param string $idiom Lenguage del cual cargarémos la frase.
     * 
     * @return string Frase traducida y con los parámetros reemplazados.
     */
    public function phrase($index = '', $params = array(), $idiom = '')
    {
        // Debemos enviar siempre al menos 2 parámetros => module.line
        // y podemos enviar hasta 3 parámetros => module.file.line
        list($module, $line) = array_pad(explode('.', $index), 2, null);
        
        // Tenemos al menos 2 parámetros
        if ($module != null && $line != null)
        {   
            $idiom = ($idiom == '') ? $this->_deftLang : $idiom;
            
            if ( ! isset($this->_vars[$idiom][$module]))
            {
                $this->loadPhrases($module, $idiom);
            }
            
            if ( ! isset($this->_vars[$idiom][$module][$line]))
            {
                return $line;
            }
            
            $phrase = $this->_vars[$idiom][$module][$line];
            
            if (count($params) > 0)
            {
                $find = array();
                $replace = array();
                foreach ($params as $key => $value)
                {
                    $find[] = '{' . $key . '}';
                    $replace[] = $value;
                }
                
                $phrase = str_replace($find, $replace, $phrase);
            }
            
            return $phrase;
        }
        
        return $index;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Cargar archivo de idioma de un módulo.
     * 
     * @param array $module Módulo del cual cargarémos las frases.
     * @param string $idiom Idioma.
     * 
     * @return void
     */
    public function loadPhrases($module, $idiom = '')
    {
        // Vemos si ya hemos cargado el idioma...
        if (in_array($idiom.$module, $this->_langs))
        {
            return;
        }
        
        // Obtener de caché el archivo de idioma.
        $cache = Core::getLib('cache');
        $id = $cache->set(array(
                'language',
                $idiom . '_' . $module,
            )
        );
        
        // Buscamos..
        if ( ! ($lang = $cache->get($id)))
        {
            $rows = Core::getLib('database')->select('p.var_name, p.text_actual')
                ->from('language_phrase', 'p')
                ->join('module', 'm', 'p.module_id = m.module_id AND m.is_active = 1')
                ->where('p.language_id = ' . Core::getLib('database')->escape($idiom) . ' AND p.module_id = ' . Core::getLib('database')->escape($module))
                ->exec('rows');

            foreach ($rows as $row)
            {
                $lang[$row['var_name']] = $row['text_actual'];
            }
            
            $cache->save($id, $lang);
        }
        
        // Asignamos
        $this->_langs[] = $idiom.$module;
        $this->_vars[$idiom][$module] = $lang;
        unset($lang);
    }
}