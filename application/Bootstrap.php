<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @return void
     */
    protected function _initRoutes()
    {
        $configResource = $this->bootstrap('config')->getResource('config');
        $routesConfig = $configResource->getConfig('routes');

        $router = $this->bootstrap('Router')->getResource('Router');
        $router->addConfig($routesConfig);
    }    
    
    /**
     * Init action helpers
     * @return void
     */
    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new SKL_Controller_Action_Helper_Dependencies()
        );

    }
}

