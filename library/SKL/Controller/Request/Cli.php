<?php

class SKL_Controller_Request_Cli extends Zend_Controller_Request_Abstract
{
    public function __construct(array $config)
    {
        if ( $config ) {
            $this->setOptions($config);
        } else {
            throw new Zend_Controller_Request_Exception('You must set config params for request');
        }
    }

    /**
     * Set options
     * @param array $config
     * @return Vcf_Controller_Request_Cli
     */
    public function setOptions(array $config)
    {
        foreach($config as $key => $value) {

            switch ($key) {

                case $this->getModuleKey():
                    $this->setModuleName($value);
                    break;

                case $this->getControllerKey():
                    $this->setControllerName($value);
                    break;

                case $this->getActionKey():
                    $this->setActionName($value);
                    break;

                default:
                    break;
            }

            if ( $key >= 0 ) {
                $this->setParam($key, $value);
            }
        }

        return $this;
    }
}
