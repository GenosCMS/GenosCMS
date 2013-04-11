<?php
/**
 * Controlador por defecto
 */
class Error_Component_Controller_404 extends Core_Component {

    /**
     * Este es el método principal
     */
    public function main()
    {
        header("HTTP/1.0 404 Not Found");
        
        // TODO: Mejorar
        $this->template->title('Page not found');
    }   
}