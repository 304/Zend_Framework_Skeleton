<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_em = null;

    public function init()
    {
        $this->_em = $this->_helper->EntityManager();
    }

    public function indexAction()
    {

    }
}

