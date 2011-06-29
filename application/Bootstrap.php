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
}

