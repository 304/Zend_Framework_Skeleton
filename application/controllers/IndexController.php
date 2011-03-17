<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_em = null;

    public function init()
    {
        $this->_em = $this->getInvokeArg('bootstrap')->getResource('doctrine');
    }

    public function indexAction()
    {
        
    }

    public function allAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $results = $this->_em->createQuery('select u from Application_Model_Entities_User u')->execute();

        foreach($results as $user) {
            echo $user->getName() . ' - '. $user->getBirthday()->format('Y-m-d H:i:s') . '<br>';
        }

        echo '<a href="'. $this->_helper->url('/').'">Back</a>';
    }

    public function addAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $user = new Application_Model_Entities_User();
        $user->setName('Noname');
        $user->setBirthday('2011-02-02');

        $this->_em->persist($user);
        $this->_em->flush();

        echo 'Ok!';

        echo '<a href="'. $this->_helper->url('/').'">Back</a>';
    }


}

