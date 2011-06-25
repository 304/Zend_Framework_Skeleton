<?php

// Init application
require_once __DIR__ . '/../init.php';

// Parse CLI params
try {
    $getopt = new Zend_Console_Getopt(array(
        'module|m=s'     => 'module name',
        'controller|c=s' => 'controller name',
        'action|a=s'     => 'action name',
    ));
    foreach (array('module', 'controller', 'action') as $option) {
        if (!isset($getopt->$option)) {
            throw new Zend_Console_Getopt_Exception(sprintf('"%s" parameter is not set', $option));
        }
    }
} catch (Zend_Console_Getopt_Exception $e) {
    echo "\r\n" . $e->getMessage() . "\r\n\r\n";
    echo $getopt->getUsageMessage();
    exit;
}

// Set CLI params
$args = $getopt->getRemainingArgs();
$path = array(
    'module'     => $getopt->module,
    'controller' => $getopt->controller,
    'action'     => $getopt->action,
);
$requestConfig = $path + $args;

// Bootstrap
$application->bootstrap();

// Create request from CLI params
$front = $bootstrap->getResource('FrontController');
$front->setRequest(new SKL_Controller_Request_Cli($requestConfig))
      ->setRouter(new SKL_Controller_Router_Cli())
      ->setResponse(new Zend_Controller_Response_Cli());

// Disable layout
Zend_Layout::getMvcInstance()->disableLayout();

// Let's go
$application->run();
