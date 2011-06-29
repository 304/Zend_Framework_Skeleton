<?php
require_once 'Doctrine/Common/ClassLoader.php';

/**
 * Resource for autoloading namespaces
 */
class SKL_Application_Resource_Autoload
    extends Zend_Application_Resource_ResourceAbstract
{
    
    public function init()
    {
        $this->_loadNamespaces();
        return $this;
    }
    
    protected function _loadNamespaces()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        
        $options = $this->getOptions();

        foreach ($options as $namespace => $path) {
            $loader = new \Doctrine\Common\ClassLoader($namespace, realpath($path));
            $autoloader->pushAutoloader(array($loader, 'loadClass'), $namespace);
        }
    }
}