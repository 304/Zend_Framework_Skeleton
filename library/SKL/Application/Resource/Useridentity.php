<?php

/**
 * Resource user identity
 */
class SKL_Application_Resource_UserIdentity
    extends Zend_Application_Resource_ResourceAbstract
{
    
    /**
     * Default identity
     *
     * @var string
     */
    protected $_defaultIdentity;
    
    /**
     * Set default identity
     * 
     * @param string $identityClass 
     */
    public function setDefaultIdentity($identityClass)
    {
        $this->getBootstrap()->bootstrap('doctrine')->getResource('doctrine');
        
        $this->_defaultIdentity = new $identityClass;
    }
    
    /**
     * Get default identity
     * 
     * @return class|null
     */
    public function getDefaultIdentity()
    {
        return $this->_defaultIdentity;
    }

    /**
     * Init resource
     *
     * @return SKL_Application_Resource_UserIdentity
     */
    public function init()
    {
        return ( Zend_Auth::getInstance()->hasIdentity() )
               ? Zend_Auth::getInstance()->getIdentity()
               : $this->getDefaultIdentity();
    }
}
