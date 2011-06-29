<?php
/**
 * Dependencies injection
 * @todo: think again :)
 */
class SKL_Controller_Action_Helper_Dependencies extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Pre dispatch
     */
    public function preDispatch()
    {
        $controller = $this->getActionController();
        $bootstrap  = $this->getBootstrap();
        
        
        $autoInitResources = $bootstrap->getResource('config')
                                       ->getConfig('application')
                                       ->autoinit
                                       ->resource;
        
        foreach($autoInitResources as $resourceName => $varName) {
            if ( $this->_hasVariable($varName) ) {
                if ( $bootstrap->hasResource($resourceName) ) {
                    $resource = $bootstrap->getResource($resourceName);
                    $this->_setValueInVariable($varName, $resource);
                }
            }
        }
    }

    /**
     * Set param in controller
     * 
     * @param string $varName
     * @param string $resource 
     */
    protected function _setValueInVariable($varName, $resource)
    {
        $controller = $this->getActionController();
        
        // Init variable
        $obj = new ReflectionObject($controller);
        $prop = $obj->getProperty($varName);
        $prop->setAccessible(true);

        // change value
        $prop->setValue($controller, $resource);
        
    }

    /**
     * Check if controller has a variable
     *
     * @param string $variable
     * @return bool
     */
    protected function _hasVariable($variable)
    {
        $controller = $this->getActionController();
        
        $reflection = new ReflectionObject($controller);
        return $reflection->hasProperty($variable);
    }

    /**
     * Get bootstrap
     * 
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    public function getBootstrap()
    {
        return $this->getFrontController()->getParam('bootstrap');
    }
}