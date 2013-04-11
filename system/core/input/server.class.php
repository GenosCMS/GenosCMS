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
 * Gestiona la entrada de datos mediante $_SERVER
 * 
 * @package     Framework\Core\Input
 * @since       1.0.0
 */
class Core_Input_Server extends Core_Input {
    
    /**
     * Dirección IP
     * 
     * @var string
     */
    protected $ipAddress = null;
    
    /**
     * Es un dispositivo movil?
     * 
     * @var bool
     */
    protected $isMobile = false;
        
    /**
     * Constructor
     * 
     * Reemplaza al de su clase padre.
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('input.server.init') : null;
        
        // $_FILES
        $this->data =& $_SERVER;
        
        // Filtro
        $this->filter = Core::getLib('filter.input');
    }
    
    // -------------------------------------------------------------
    
    /**
     * Obtener dirección IP
     * 
     * @return string
     */
    public function getIp()
    {
        static $ip;
        
        if ($ip)
        {
            return $ip;
        }
 		
 		$ip = $_SERVER['REMOTE_ADDR'];
 
 		if (isset($_SERVER['HTTP_CLIENT_IP']))
 		{
 			$ip = $_SERVER['HTTP_CLIENT_IP'];
 		}
 		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
 		{
 			foreach ($matches[0] as $_IP)
 			{
 				if (!preg_match("#^(10|172\.16|192\.168)\.#", $_IP))
 				{
 					$ip = $_IP;
 					break;
 				}
 			}
 		}
 		elseif (isset($_SERVER['HTTP_FROM']))
 		{
 			$ip = $_SERVER['HTTP_FROM'];
 		}
        
        return $ip;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Obtener el nombre del navegador.
     * 
     * @return string
     */
    public function getBrowser()
    {
        static $agent;
        
        if ($agent)
        {
            return $agent;
        }
        
        $agent = $this->getString('HTTP_USER_AGENT');
        
    	if (preg_match("/Firefox\/(.*)/i", $agent, $matches) && isset($matches[1]))
    	{
    		$agent = 'Firefox ' . $matches[1];
    	}
    	elseif (preg_match("/MSIE (.*);/i", $agent, $matches))
    	{
    		$parts = explode(';', $matches[1]);
    		$agent = 'IE ' . $parts[0];    		
    	}
    	elseif (preg_match("/Opera\/(.*)/i", $agent, $matches))
    	{
    		$parts = explode(' ', trim($matches[1]));
    		$agent = 'Opera ' . $parts[0];
    	}
    	elseif (preg_match('/\s+?chrome\/([0-9.]{1,10})/i', $agent, $matches))
    	{
    		$parts = explode(' ', trim($matches[1]));
    		$agent = 'Chrome ' . $parts[0];
    	}
    	elseif (preg_match('/android/i', $agent))
    	{
			$this->isMobile = true;
			$agent = 'Android';			
    	}    
    	elseif (preg_match('/opera mini/i', $agent))
    	{
			$this->isMobile = true;
			$agent = 'Opera Mini';			
    	}   
    	elseif (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|fennec|plucker|xiino|blazer|elaine)/i', $agent))
    	{
			$this->isMobile = true;
    		$agent = 'Palm';			
    	}      	
    	elseif (preg_match('/blackberry/i', $agent))
    	{
			$this->isMobile = true;
			$agent = 'Blackberry';
    	}     	
    	elseif (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile|windows phone)/i', $agent))
    	{
			$this->isMobile = true;
			$agent = 'Windows Smartphone';
    	}    	
		elseif (preg_match("/Version\/(.*) Safari\/(.*)/i", $agent, $matches) && isset($matches[1]))
    	{
    		if (preg_match("/iPhone/i", $agent) || preg_match("/ipod/i", $agent))
    		{
    			$parts = explode(' ', trim($matches[1]));
    			$agent = 'Safari iPhone ' . $parts[0];	
    			$this->isMobile = true;
    		}
    		else 
    		{
    			$agent = 'Safari ' . $matches[1];
    		}
    	}
    	elseif (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $agent))
    	{
    		$this->isMobile = true;
    	}
    	
    	return $agent;
    }
}