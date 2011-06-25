<?php

/**
 * Resource for getting modules config
 */
class SKL_Application_Resource_Config
    extends Zend_Application_Resource_ResourceAbstract
{
    
    /**
     * Personal config suffix
     *
     * @var string
     */
    const PERSONAL_CONFIG_SUFFIX = 'personal';

    /**
     * Config directory in module
     *
     * @var string
     */
    protected $_directoryName = '';

    /**
     * Config path
     *
     * @var array
     */
    protected $_configPath = array();

    /**
     * Loaded configs
     *
     * @var array
     */
    protected $_loadedConfigs = array();

    /**
     * Init resource
     *
     * @return SKL_Application_Resource_Config
     */
    public function init()
    {
        return $this;
    }

    /**
     * @param  $directoryName
     * @return SKL_Application_Resource_Config
     */
    public function setDirectoryName($directoryName)
    {
        $this->_directoryName = $directoryName;

        return $this;
    }

    /**
     * Set cache options
     *
     * @param array $cacheOptions
     */
    public function setCacheOptions($cacheOptions)
    {
        $this->_cacheOptions = $cacheOptions;
    }

    /**
     * @param  $configName
     * @return array
     */
    public function getConfig($configName)
    {
        if (!isset($this->_loadedConfigs[$configName])) {
            $this->_loadedConfigs[$configName] = $this->_loadConfig($configName);
        }

        return $this->_loadedConfigs[$configName];
    }

    /**
     * @throws Zend_Application_Resource_Exception
     * @param  $configName
     * @return Zend_Config
     */
    protected function _loadConfig($configName)
    {
        $config = new Zend_Config(array(), true);

        foreach ($this->_getConfigPath($configName) as $path) {
            switch (pathinfo($path, PATHINFO_EXTENSION)) {
                case 'ini':
                    $configClass = 'Zend_Config_Ini';
                    break;

                case 'xml':
                    $configClass = 'Zend_Config_Xml';
                    break;

                case 'json':
                    $configClass = 'Zend_Config_Json';
                    break;

                default:
                    $configClass = null;
            }
            if (null === $configClass) {
                throw new Zend_Application_Resource_Exception('Incorrect file extension');
            }
            $config->merge(new $configClass($path));
        }

        $config = $config->get(APPLICATION_ENV, $config);

        return $config;
    }

    /**
     * Get config path
     *
     * @throws Zend_Application_Resource_Exception
     * @param  $configName
     * @return array string[]
     */
    protected function _getConfigPath($configName)
    {
        if (!isset($this->_configPath[$configName])) {

            /** @var $front Zend_Controller_Front */
            $front = $this->_bootstrap->bootstrap('Frontcontroller')
                    ->getResource('Frontcontroller');

            $controllerDirectories = $front->getControllerDirectory();

            // move default directory to the end
            $defaultDirectory = $controllerDirectories['default'];
            unset($controllerDirectories['default']);
            $controllerDirectories['default'] = $defaultDirectory;

            $path = array();

            $configExtensions = array('ini', 'xml', 'json');

            foreach ($controllerDirectories as $dir) {
                $directory = dirname($dir) . DIRECTORY_SEPARATOR
                             . $this->_directoryName . DIRECTORY_SEPARATOR;
                $tempPath = $directory . $configName;

                // load base config
                foreach ($configExtensions as $extension) {
                    $_path = $tempPath . '.' . $extension;
                    if (Zend_Loader::isReadable($_path)) {
                        $path[] = $_path;
                    }
                }

                // load personal config
                foreach ($configExtensions as $extension) {
                    $_path = $tempPath . '.' . self::PERSONAL_CONFIG_SUFFIX
                           . '.' . $extension;
                    if (Zend_Loader::isReadable($_path)) {
                        $path[] = $_path;
                    }
                }
            }

            if (0 == count($path)) {
                throw new Zend_Application_Resource_Exception(
                    sprintf(
                        'Config %s (with extensions %s) was not found',
                        $configName,
                        implode(', ', $configExtensions)
                    )
                );
            }

            $this->_configPath[$configName] = $path;
        }

        return $this->_configPath[$configName];
    }
}
