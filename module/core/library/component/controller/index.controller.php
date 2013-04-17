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
        if (count($_POST) > 0)
        {
            // Reglas
            $fields= array(
                array(
                    'field' => 'username',
                    'value' => $this->input->post->getString('username'),
                    'label' => 'core.username',
                    'rules' => 'required|is_valid[user_name]|callback_checkUser'
                ),
                array(
                    'field' => 'name',
                    'value' => $this->input->post->getString('name'),
                    'label' => 'core.form_name',
                    'rules' => 'required|is_valid[name]'
                ),
                array(
                    'field' => 'pwd',
                    'value' => $this->input->post->getRaw('pwd'),
                    'label' => 'core.form_pawd',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'pwd2',
                    'value' => $this->input->post->getRaw('pwd2'),
                    'label' => 'core.form_pawd',
                    'rules' => 'required|matches[pwd]'
                ),
                array(
                    'field' => 'year',
                    'value' => $this->input->post->getInt('name'),
                    'label' => 'core.form_year',
                    'rules' => 'required|exact_length[4]'
                ),
                array(
                    'field' => 'url',
                    'value' => $this->input->post->get('url', null, 'array'),
                    'label' => 'core.url',
                    'rules' => 'is_valid[url]'
                )
            );
            
            $isPassed = Core::getLib('form')->set($fields)->validate($this);
            if ($isPassed == true)
            {
                echo 'Exito';exit;
            }
            else
            {
                //$this->template->assign('formErrors', Core::getLib('form')->error());
            }
        }
        
        //
        $this->template->title('Index');
    }
    
    public function checkUser($str)
    {
        return 'Checado';
    }   
}