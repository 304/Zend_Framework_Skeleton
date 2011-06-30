<?php
/**
 * Resource for register doctrine listeners
 */
class SKL_Application_Resource_Listeners
    extends Zend_Application_Resource_ResourceAbstract
{
    
    public function init()
    {
        $this->_registerListeners();
        return $this;
    }
    
    protected function _registerListeners()
    {
        $em = $this->getBootstrap()->bootstrap('doctrine')
                                   ->getResource('doctrine');

        $evm = $em->getEventManager();
        
        foreach($this->getOptions() as $className) {
            $evm->addEventSubscriber(new $className);
        }
    }
}