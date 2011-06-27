<?php
/**
 * @todo: test it and improve implementation
 */
class SKL_Controller_Action_Helper_Dependencies extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {
        $controller = $this->getActionController();

        // Get all setter methods
        $initMethods = $this->_getInitMethods();

        foreach($initMethods as $varName => $methodName) {

            if ( $this->_hasVariable($controller, $varName) ) {

                // Init variable
                $obj = new ReflectionObject($controller);
                $prop = $obj->getProperty($varName);
                $prop->setAccessible(true);
                
                // change value
                $prop->setValue($controller, $this->$methodName());
            }
        }
    }

    /**
     * Get EntityManager
     * @return \Doctrine\Common\EventManager|null
     */
    protected function _emSetter()
    {
        $resourceName = 'em';
        $bootstrap = $this->getBootstrap();

        if ( $bootstrap->hasResource($resourceName) ) {
            return $bootstrap->getResource($resourceName);
        } else {
            return null;
        }
    }
    
    protected function _getInitMethods()
    {

        $reflectionClass = new ReflectionClass(__CLASS__);
        $methods = $reflectionClass->getMethods();

        return $this->_getSetters($methods);
    }

    protected function _getSetters($methods)
    {
        $initMethods = array();

        foreach($methods as $method) {
            if ( preg_match('|^(.+?)Setter|', $method->name, $match) ) {
                $initMethods[ $match[1] ] = $method->name;
            }
        }

        return $initMethods;
    }

    /**
     * Check if controller has a variable
     *
     * @param object $controller
     * @param string $variable
     * @return bool
     */
    protected function _hasVariable($controller, $variable)
    {
        $reflection = new ReflectionObject($controller);
        return $reflection->hasProperty($variable);
    }


    /**
     * Get bootstrap
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    public function getBootstrap()
    {
        return $this->getFrontController()->getParam('bootstrap');
    }
}