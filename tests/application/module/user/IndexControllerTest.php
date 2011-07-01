<?php

class User_IndexControllerTest extends ControllerTestCase
{
    protected $testUser = null;
    
    protected function _createTestUser()
    {
        $user = new \Entities\User();
        $user->setUsername('test');
        $user->setPassword('test');
        $user->setEmail('test@test.ru');
        
        $this->em->persist($user);
        $this->em->flush();
        
        return $user;
        
    }
    
    protected function _removeTestUser()
    {
        $this->em->remove($this->testUser);
        $this->em->flush();
    }
    
    public function tearDown()
    {
        $this->_removeTestUser();
    }


    public function setUp()
    {
        parent::setUp();
        
        $this->testUser = $this->_createTestUser();
    }

    public function testRouteIndex()
    {
        $this->dispatch('/login');
        $this->assertModule('user');
        $this->assertAction("index");
        $this->assertController("index");
        $this->assertRoute('user-login');
    }
    
    public function testLoginFormExist()
    {
        $this->dispatch('/login');
        $this->assertQueryCount('form#login', 1);
    }
    
    public function testLoginSuccess()
    {
        $this->getRequest()->setMethod('POST')
             ->setPost(array(
                 'username' => 'test',
                 'password' => 'test',
        ));
        
        $this->dispatch('/login');
        $this->assertRedirectTo('/');
    }

    public function testLoginUncorrectPassword()
    {
        $this->getRequest()->setMethod('POST')
             ->setPost(array(
                 'username' => 'test',
                 'password' => 'incorrectPassword',
        ));
        
        $this->dispatch('/login');
        $this->assertQueryContentContains('div.error', 'Not valid');
    }
    
    public function testLoginUsernameCaseSensitive()
    {
        $this->getRequest()->setMethod('POST')
             ->setPost(array(
                 'username' => 'TesT',
                 'password' => 'test',
        ));
        
        $this->dispatch('/login');
        $this->assertQueryContentContains('div.error', 'Not valid');
    }

    public function testLoginPasswordCaseSensitive()
    {
        $this->getRequest()->setMethod('POST')
             ->setPost(array(
                 'username' => 'test',
                 'password' => 'TeSt',
        ));
        
        $this->dispatch('/login');
        $this->assertQueryContentContains('div.error', 'Not valid');
    }
}
