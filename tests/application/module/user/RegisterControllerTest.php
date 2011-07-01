<?php

class User_RegisterControllerTest extends ControllerTestCase
{
    public function testRouteIndex()
    {
        $this->dispatch('/register');
        $this->assertModule('user');
        $this->assertAction("index");
        $this->assertController("register");
        $this->assertRoute('user-register');
    }
    
    public function testRegisterFormExist()
    {
        $this->dispatch('/register');
        $this->assertQueryCount('form#register', 1);
    }
    
    public function testRegisterProcess()
    {
        $userParams = array(
            'username' => 'register',
            'password' => 'register',
            'email'    => 'register@register.ru',
        );
        
         $this->getRequest()->setMethod('POST')
             ->setPost($userParams);       
        
        $this->dispatch('/register');
        $this->assertRedirectTo('/');
        
        $this->checkUserCreatedRight($userParams);
        $this->deleteUser($userParams);
    }

    
    protected function checkUserCreatedRight($userParams)
    {
        $user = $this->em->getRepository('\Entities\User')->findOneBy(array(
            'username' => $userParams['username']
        ));
        
        $this->assertEquals($user->getUsername(), $userParams['username']);
        $this->assertEquals($user->getEmail(), $userParams['email']);
    }

    protected function deleteUser($userParams)
    {
        $user = $this->em->getRepository('\Entities\User')->findOneBy(array(
            'username' => $userParams['username']
        ));

        $this->em->remove($user);
        $this->em->flush();
        
        $user = $this->em->getRepository('\Entities\User')->findOneBy(array(
            'username' => $userParams['username']
        ));
        
        $this->assertNull($user);
    }
}
