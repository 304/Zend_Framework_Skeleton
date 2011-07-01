<?php

class User_IndexController extends Zend_Controller_Action
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_em = null;

    public function indexAction()
    {
        $form = new User_Form_Login();
        
        $request = $this->getRequest();
        
        if ( $request->isPost() ) {
            if ( $form->isValid($request->getPost()) ) {
                
                $username = $form->getValue('username');
                $password = $form->getValue('password');
                $remember = $form->getValue('remember');
                
                if ( $this->_authenticate($username, $password, $remember) ) {
                    $this->_helper->redirector->gotoRoute(array(), 'main-page');
                } else {
                    $message = 'Not valid';
                }
            }
        }
        
        $this->view->assign(array(
            'form'    => $form,
            'message' => isset($message) ? $message : null
        ));
    }
    
    /**
     * Logout action
     */
    public function logoutAction()
    {
        $this->_helper->auth->logout();
        $this->_helper->redirector->gotoRoute(array(), 'main-page');
    }    
    
    /**
     * Authenticate user
     * 
     * @param string $username
     * @param string $password
     * @param string $remember
     * @return bool
     */
    protected function _authenticate($username, $password, $remember)
    {
        $adapter = $this->_getAuthAdapter($username, $password);

        if ($this->_helper->auth->login($adapter)) {

            $user = $this->_em
                    ->getRepository('\Entities\User')
                    ->findOneBy(array('username' => $username));

            $this->_helper->auth->setAuthIdentity($user, $remember);
            return true;        
        }
        
        return false;
    }
    
    /**
     * Get auth adapter
     * 
     * @param string $username
     * @param string $password
     * @return SKL_Auth_Adapter_Doctrine2 
     */
    protected function _getAuthAdapter($username, $password)
    {
        $authAdapter = new SKL_Auth_Adapter_Doctrine2();

        $authAdapter->setEm($this->_em)
                ->setEntityName('\Entities\User')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password')
                ->setIdentity($username)
                ->setCredential($password);

        return $authAdapter;
    }    
}