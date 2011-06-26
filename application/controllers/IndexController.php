<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_em = null;

    /**
     * @var \Entities\User
     */
    private $_user = null;


    public function init()
    {
        $this->_em = $this->_helper->EntityManager();
        
        $this->_user = ( Zend_Auth::getInstance()->hasIdentity() )
                     ? Zend_Auth::getInstance()->getIdentity()
                     : new \Entities\User();
        
    }

    public function indexAction()
    {
    }
}

