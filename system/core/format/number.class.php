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
 * Format Numbre
 * 
 * Clase para dar formato a números.
 * 
 * @package     Framework\Core\Format
 * @since       1.0.0
 */
class Core_Format_Number {
    
    /**
     * Constructor
     */
    public function __construct()
    {
        DEBUG_MODE ? Core::mark('format.number.init') : null;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Convierte bytes en formatos más distinguibles, tales como:
     * kilobytes, megabytes, etc.
     * 
     * Por defecto, el formato apropiado automáticamente será elegido.
     * Sin embargo, puede elegir el tipo que desa obtener.
     * 
     * @param int $bytes El número de bytes
     * @param string $unit Tipo de unidad a convertir.
     * @param int $precision Número de decimales.
     * 
     * @return string El número de bytes en las unidades adecuadas.
     */
    public function bytes($bytes, $unit = 'auto', $precision = 2)
    {
        if (empty($bytes))
        {
            return 0;
        }
        
        $unitTypes = array('b', 'kb', 'MB', 'GB', 'TB', 'PB');
        
        // Unidad automática
        $i = floor(log($bytes, 1024));
        
        // Unidad definida
		if ($unit !== 'auto' && in_array($unit, $unitTypes))
		{
			$i = array_search($unit, $unitTypes, true);
		}
        
        return round($bytes / pow(1024, $i), $precision) . ' ' . $unitTypes[$i];
    }
}