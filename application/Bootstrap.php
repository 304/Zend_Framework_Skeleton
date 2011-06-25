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
    
    protected function _initDoctrineAutoloader()
    {
        // Autoloader config
        require_once 'Doctrine/Common/ClassLoader.php';

        $doctrineAutoloader = array(new \Doctrine\Common\ClassLoader(), 'loadClass');

        $autoloader = Zend_Loader_Autoloader::getInstance();

        // Push the doctrine autoloader to load for the Doctrine\ namespace
        $autoloader->pushAutoloader($doctrineAutoloader, 'Doctrine');

        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', realpath(APPLICATION_PATH . '/../library/Doctrine/'), 'loadClass');
        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Symfony');

        // Entities loader
        $modelLoader = new \Doctrine\Common\ClassLoader('Entities', realpath(APPLICATION_PATH . '/models/'));
        $autoloader->pushAutoloader(array($modelLoader, 'loadClass'), 'Entities');

        // Repository loader
        $modelLoader = new \Doctrine\Common\ClassLoader('Repositories', realpath(APPLICATION_PATH . '/models/'));
        $autoloader->pushAutoloader(array($modelLoader, 'loadClass'), 'Repositories');

        // Migrations loader
        $modelLoader = new \Doctrine\Common\ClassLoader('Tools', realpath(APPLICATION_PATH . '/../library/SKL/Doctrine/'));
        $autoloader->pushAutoloader(array($modelLoader, 'loadClass'), 'Tools');
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

