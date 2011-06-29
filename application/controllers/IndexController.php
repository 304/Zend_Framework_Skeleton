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
    }
    
    public function indexAction()
    {
    }

}

