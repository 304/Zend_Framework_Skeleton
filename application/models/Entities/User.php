<?php

namespace Entities;

/**
 * @Entity(repositoryClass="Repositories\User")
 * @Table(name="users")
 */
class User
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", length=50,  name="username")
     * @var string
     */
    private $username;

    /**
     * @Column(type="string", length=255,  name="password")
     * @var string
     */
    private $password;

    /**
     * @Column(type="string", length=255,  name="email")
     * @var string
     */
    private $email;

    /**
     * @Column(type="string", length=255,  name="salt")
     * @var string
     */
    private $salt;

    /**
     * construct
     */
    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        // empty password form protection
        if ($password == '') {
            return null;
        }

        // create salt if not exists
        if ( ! $this->getSalt() ) {
            $this->setSalt($this->_generateSalt());
        }

        $this->password = $this->_encodePassword($password, $this->getSalt());
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Generate "salt" password
     *
     * @param string $password
     * @param string $salt
     * @return string
     */
    protected function _encodePassword($password, $salt)
    {
        return md5($salt . $password);
    }

    /**
     * Check if password is correct
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        $encodedPassword = $this->_encodePassword($password, $this->getSalt());

        return ( $encodedPassword == $this->getPassword() );
    }

    /**
     * Set salt
     * (protected - to prevent changing salt without changing password)
     *
     * @param string $salt
     */
    protected function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string $salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Generate new salt
     *
     * @return string
     */
    protected function _generateSalt()
    {
        return md5(time() . 'salt' . rand(0, 10000) );
    }
    
    public function isGuest()
    {
        return ( ! $this->getId() );
    }
}