<?php

class User_RegisterController extends Zend_Controller_Action
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
        $form = new User_Form_Register();
        
        $request = $this->getRequest();
        
        if ( $request->isPost() ) {
            if ( $form->isValid($request->getPost()) ) {

                $username = $form->getValue('username');
                $password = $form->getValue('password');
                $email    = $form->getValue('email');
                
                $user = new \Entities\User();
                
                $user->setUsername($username);
                $user->setPassword($password);
                $user->setEmail($email);
                
                $this->_em->persist($user);
                $this->_em->flush();
                
                $this->_redirect($this->getHelper('url')->url(array(), 'main-page'));
            }
        }
        
        $this->view->assign(array(
            'form' => $form,
        ));
    }
}

?>
