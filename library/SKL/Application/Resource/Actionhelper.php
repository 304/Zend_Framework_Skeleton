<?php

/**
 * Resource for loading action helpers
 */
class SKL_Application_Resource_ActionHelper
    extends Zend_Application_Resource_ResourceAbstract
{
    
    public function init()
    {
        $this->getBootstrap()->bootstrap('frontcontroller');
        foreach($this->getOptions() as $helperName) {
            Zend_Controller_Action_HelperBroker::getStaticHelper($helperName);
        }
    }
}