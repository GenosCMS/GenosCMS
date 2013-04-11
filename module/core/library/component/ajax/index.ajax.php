<?php


class Core_Component_Ajax_Index extends Core_Ajax {
    
    public function main()
    {   
        $this->call("alert('Hola Ajax');");
    }
}