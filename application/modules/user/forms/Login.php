<?php

class User_Form_Login extends Zend_Form
{
    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');

        $this->addElement('text', 'username', array(
            'filters'     => array('StringTrim'),
            'validators'  => array(),
            'required'    => true,
            'label'       => 'Имя:',
        ));

        $this->addElement('password', 'password', array(
            'filters'     => array(),
            'validators'  => array(),
            'required'    => true,
            'label'       => 'Пароль:',
        ));

        $this->addElement('checkbox', 'remember', array(
            'filters'     => array(),
            'validators'  => array(),
            'required'    => false,
            'label'       => 'Запомнить',
        ));

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Войти',
        ));
    }
}