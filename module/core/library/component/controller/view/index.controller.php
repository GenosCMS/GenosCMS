<?php
/**
 * Controlador por defecto
 */
class Core_Component_Controller_View_Index extends Core_Component {

    /**
     * Este es el método principal
     */
    public function main($var = '', $foo = '')
    {
        exit('Exito:' . $var . $foo);
    }   
}