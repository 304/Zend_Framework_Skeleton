<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @return Doctrine\ORM\EntityManager
     */
    protected function _initDoctrine()
    {
        require_once('Doctrine/Common/ClassLoader.php');

        // Create the doctrine autoloader and remove it from the spl autoload stack (it adds itself)
        require_once 'Doctrine/Common/ClassLoader.php';
        $doctrineAutoloader = array(new \Doctrine\Common\ClassLoader(), 'loadClass');
        //$doctrineAutoloader->register();
        spl_autoload_unregister($doctrineAutoloader);

        $autoloader = Zend_Loader_Autoloader::getInstance();

        // Push the doctrine autoloader to load for the Doctrine\ namespace
        $autoloader->pushAutoloader($doctrineAutoloader, 'Doctrine');

        $classLoader = new \Doctrine\Common\ClassLoader('Entities', realpath(__DIR__ . '/models/'), 'loadClass');
        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Entities');

        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', realpath(__DIR__ . '/../library/Doctrine/'), 'loadClass');
        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Symfony');

        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL\Migrations', realpath(__DIR__ . '/../library/Doctrine/DBAL/Migrations'));
        $autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Doctrine\DBAL\Migrations');

        $doctrineConfig = $this->getOption('doctrine');
        $config = new \Doctrine\ORM\Configuration();

        $cache = new \Doctrine\Common\Cache\ArrayCache;
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $driverImpl = $config->newDefaultAnnotationDriver(APPLICATION_PATH . '/models/Entities');
        //$driverImpl = $config->newDefaultAnnotationDriver($doctrineConfig['path']['entities']);
        $config->setMetadataDriverImpl($driverImpl);

        $config->setProxyDir(APPLICATION_PATH . '/../proxies');
        $config->setProxyNamespace('App\Proxies');

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

