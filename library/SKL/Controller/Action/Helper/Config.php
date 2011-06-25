<?php

class SKL_Controller_Action_Helper_Config extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Get config
     * @param string $name
     * @param string $section
     * @return Zend_Config_Ini
     */
    public function direct($name)
    {
        $configResource = $this->getActionController()
                               ->getInvokeArg('bootstrap')
                               ->getResource('Config');
        $config = $configResource->getConfig($name);
        return $config;
    }
}
