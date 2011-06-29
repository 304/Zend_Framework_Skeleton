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

    /**
     * @return Doctrine\ORM\EntityManager
     */
    protected function _initDoctrine()
    {
        $this->bootstrap('doctrineAutoloader');

        $config = new \Doctrine\ORM\Configuration();

        // Set correct enviroment for cli and web
        $isWebServerRequest = isset($_SERVER['SERVER_NAME']);
        $enviroment = $isWebServerRequest ? APPLICATION_ENV : 'development';

        // Cache config
        if ($enviroment == "development") {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }

        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // Proxy config
        if ($enviroment == "development") {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            // use "php doctrine.php orm:generate-proxies" for manual creation
            $config->setAutoGenerateProxyClasses(false);
        }

        // Get application.ini config params
        $doctrineConfig = $this->getOption('doctrine');


        $config->setProxyDir($doctrineConfig['path']['proxies']);
        $config->setProxyNamespace('App\Proxies');

        // Logger ON!
        //$logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
        //$config->setSQLLogger($logger);

        // Set metadata driver
        $driverImpl = $config->newDefaultAnnotationDriver($doctrineConfig['path']['entities']);
        $config->setMetadataDriverImpl($driverImpl);


        // Database connection config
        $connectionOptions = array(
            'host' => $doctrineConfig['conn']['host'],
            'driver' => $doctrineConfig['conn']['driver'],
            'user' => $doctrineConfig['conn']['user'],
            'password' => $doctrineConfig['conn']['pass'],
            'dbname' => $doctrineConfig['conn']['dbname'],
        );

        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

        return $em;
    }
}

