<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @return Doctrine\ORM\EntityManager
     */
    protected function _initDoctrine()
    {
        // Create the doctrine autoloader and remove it from the spl autoload stack (it adds itself)
        require_once 'Doctrine/Common/ClassLoader.php';

        $doctrineAutoloader = array(new \Doctrine\Common\ClassLoader(), 'loadClass');
        //$doctrineAutoloader->register();
// ???
//        spl_autoload_unregister($doctrineAutoloader);

        $autoloader = Zend_Loader_Autoloader::getInstance();

        // Push the doctrine autoloader to load for the Doctrine\ namespace
        $autoloader->pushAutoloader($doctrineAutoloader, 'Doctrine');

// ???
//        $classLoader = new \Doctrine\Common\ClassLoader('Entities', realpath(__DIR__ . '/models/'), 'loadClass');
//        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Entities');

        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', realpath(__DIR__ . '/../library/Doctrine/'), 'loadClass');
        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Symfony');

// ???
//        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL\Migrations', realpath(__DIR__ . '/../library/Doctrine/DBAL/Migrations'));
//        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Doctrine\DBAL\Migrations');

        

        
        $config = new \Doctrine\ORM\Configuration();

        // Cache config
        if ( APPLICATION_ENV == "development") {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }

        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);


        // Proxy config
        if ( APPLICATION_ENV == "development") {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            // use "php doctrine.php orm:generate-proxies" for manual creation
            $config->setAutoGenerateProxyClasses(false);
        }

        $config->setProxyDir(APPLICATION_PATH . '/models/Proxies');
        $config->setProxyNamespace('App\Proxies');


        // Logger ON!
        $logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
        $config->setSQLLogger($logger);

        // Get application.ini config params
        $doctrineConfig = $this->getOption('doctrine');

        // Set metadata driver
        $driverImpl = $config->newDefaultAnnotationDriver($doctrineConfig['path']['entities']);
        $config->setMetadataDriverImpl($driverImpl);

        
        // Database connection config
        $connectionOptions = array(
            'driver'    => $doctrineConfig['conn']['driver'],
            'user'      => $doctrineConfig['conn']['user'],
            'password'  => $doctrineConfig['conn']['pass'],
            'dbname'    => $doctrineConfig['conn']['dbname'],
            'host'      => $doctrineConfig['conn']['host']
        );

        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

        Zend_Registry::set('em', $em);

        return $em;
    }
}

