<?php

class SKL_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @return SKL_Controller_Action_Helper_Auth
     */
    public function direct()
    {
        return $this;
    }

    /**
     * Login by adapter
     * 
     * @param Zend_Auth_Adapter_Interface $adapter
     * @return bool
     */
    public function login(Zend_Auth_Adapter_Interface $adapter)
    {
        $auth = Zend_Auth::getInstance();
        
        $authResult = $auth->authenticate($adapter);
        
        if ($authResult->isValid()) {
            return true;
        }
        
        return false;
    }

    /**
     * Storage auth identity
     * 
     * @param $identity
     * @param $remember - remember me option
     * 
     * @return SKL_Controller_Action_Helper_Auth
     */
    public function setAuthIdentity($identity, $remember = false)
    {
        $auth = Zend_Auth::getInstance();
        $auth->getStorage()->write($identity);
        if ($remember) {
            Zend_Session::rememberMe();
        }
        return $this;
    }

    /**
     * Logout
     * 
     * @return SKL_Controller_Action_Helper_Auth
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::forgetMe();
        return $this;
    }
}