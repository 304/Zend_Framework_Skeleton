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

    /**
     * @todo: move to resource
     */
    protected function _initDoctrineAutoloader()
    {
        // Autoloader config
        require_once 'Doctrine/Common/ClassLoader.php';

        $autoloader = Zend_Loader_Autoloader::getInstance();

        $loadNamespaces = array(
            'Doctrine'     => APPLICATION_PATH . '/../library/',
            'Symfony'      => APPLICATION_PATH . '/../library/Doctrine/',
            'Entities'     => APPLICATION_PATH . '/models/',
            'Proxies'      => APPLICATION_PATH . '/models/',
            'Repositories' => APPLICATION_PATH . '/models/',
            'Tools'        => APPLICATION_PATH . '/../library/SKL/Doctrine',
        );


        foreach ($loadNamespaces as $namespace => $path) {
            $loader = new \Doctrine\Common\ClassLoader($namespace, realpath($path));
            $autoloader->pushAutoloader(array($loader, 'loadClass'), $namespace);
        }        
    }
}

