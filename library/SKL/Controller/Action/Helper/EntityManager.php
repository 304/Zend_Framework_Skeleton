<?php

class SKL_Controller_Action_Helper_EntityManager extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
        return $this->getActionController()
                    ->getInvokeArg('bootstrap')
                    ->getResource('doctrine');
    }
}
