<?php

class User_Form_Register extends Zend_Form
{
    public function init()
    {
        $this->setName("register");
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

        $this->addElement('text', 'email', array(
            'filters'     => array('StringTrim'),
            'validators'  => array('EmailAddress'),
            'required'    => true,
            'label'       => 'Email:',
        ));

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Добавить пользователя',
        ));
    }
}