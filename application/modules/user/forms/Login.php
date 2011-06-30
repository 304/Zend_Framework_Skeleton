<?php
/**
 * Login form
 */
class User_Form_Login extends Zend_Form
{
    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->_addElements();
    }
 
    /**
     * @return User_Form_Login
     */
    protected function _addElements()
    {
        $this->addElements(array(
            $this->_getFieldUsername(),
            $this->_getFieldPassword(),
            $this->_getFieldRemember(),
            $this->_getFieldSubmit(),
        ));
        return $this;
    }    
    
    /**
     * @return Zend_Form_Element_Text
     */        
    protected function _getFieldUsername()
    {
        $field = $this->createElement('text', 'username', array(
            'filters'     => array('StringTrim'),
            'validators'  => array(),
            'required'    => true,
            'label'       => 'Имя:',
        ));
        
        return $field;
    }    
    
    /**
     * @return Zend_Form_Element_Password
     */        
    protected function _getFieldPassword()
    {
        $field = $this->createElement('password', 'password', array(
            'filters'     => array(),
            'validators'  => array(),
            'required'    => true,
            'label'       => 'Пароль:',
        ));
        
        return $field;
    }    

    /**
     * @return Zend_Form_Element_Checkbox
     */        
    protected function _getFieldRemember()
    {
        $field = $this->createElement('checkbox', 'remember', array(
            'filters'     => array(),
            'validators'  => array(),
            'required'    => false,
            'label'       => 'Запомнить',
        ));
        
        return $field;
    }    

    /**
     * @return Zend_Form_Element_Submit
     */        
    protected function _getFieldSubmit()
    {
        $field = $this->createElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Войти',
        ));
        
        return $field;
    }    
}