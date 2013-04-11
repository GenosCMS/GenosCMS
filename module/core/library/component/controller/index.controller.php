<?php
/**
 * Controlador por defecto
 */
class Core_Component_Controller_Index extends Core_Component {

    /**
     * Este es el método principal
     */
    public function main()
    {
        $variable = '';
        
        if ($this->input->count() && $this->input->getMethod() == 'POST')
        {
            $variable = $this->input->cookie->get('session');
        }
        
        $this->template
            ->assign('variable', $variable)
            ->js('core/ajax.js', 'static_script')
            ->title('Index');
    }   
}