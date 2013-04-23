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
                    'label' => 'core.username',
                    'rules' => 'required|is_valid[user_name]|callback_checkUser',
                    'error' => array(
                        'is_valid[user_name]' => 'El nombre de usuario no es valido...',
                    )
                ),
                array(
                    'field' => 'name',
                    'label' => 'core.form_name',
                    'rules' => 'required|is_valid[name]'
                ),
                array(
                    'field' => 'pwd',
                    'label' => 'core.form_pawd',
                    'filter'=> 'raw',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'pwd2',
                    'label' => 'core.form_pawd',
                    'filter'=> 'raw',
                    'rules' => 'required|matches[pwd]',
                    'error' => array(
                        'matches' => 'Debe ingresar la misma contraseña.',
                    )
                ),
                array(
                    'field' => 'year',
                    'label' => 'core.form_year',
                    'filter'=> 'int',
                    'rules' => 'required|is_valid[numeric]'
                ),
                array(
                    'field' => 'url[]',
                    'filter'=> 'int',
                    'label' => 'core.url',
                    'rules' => 'is_valid[url]'
                ),
                array(
                    'field' => 'enabled',
                    'filter'=> 'int',
                    'rules' => 'required',
                )
            );

            $form = Core::getLib('form')->set($fields);
            // Validar...
            if ($form->validate($this) === true)
            {
                echo 'RESULT: '; print_r($form->fields());
            }
            else
            {
                echo '<pre>ERROR:'; print_r(Core::getLib('form')->error()); echo '</pre>';
            }
        }
        
        //
        $this->template->title('Index')
        ->assign(array(
                'range' => range(1980, 2020),
            ));
    }
    
    public function checkUser($str)
    {
        return 'Checado';
    }   
}