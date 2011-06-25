<?php
define('APPLICATION_ENV', 'development');

// Init application
require_once __DIR__ . '/../init.php';

$application->bootstrap();

// Retrieve Doctrine Container resource
/** @var Doctrine\ORM\EntityManager */
$em = $application->getBootstrap()->getResource('doctrine');
$doctrineConfig = $application->getBootstrap()->getOption('doctrine');

// Console
$cli = new \Symfony\Component\Console\Application(
    'Doctrine Command Line Interface',
    \Doctrine\Common\Version::VERSION
);

try {
    // Bootstrapping Console HelperSet
    $helperSet = array(

        // DBAL init
        'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
        
        // ORM init
        'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),

        // CLI dialog init
        'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
    );
    
} catch (\Exception $e) {
    $cli->renderException($e, new \Symfony\Component\Console\Output\ConsoleOutput());
}

$cli->setCatchExceptions(true);
$cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet($helperSet));

$cli->addCommands(array(
    // DBAL Commands
    new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
    new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

    // ORM Commands
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
    new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
    new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),

    // Generate entities, repositories and proxies with default path
    new \Tools\Console\Command\GenerateRepositoriesCommand($doctrineConfig['path']['repositories']),
    new \Tools\Console\Command\GenerateEntitiesCommand($doctrineConfig['path']['entities']),
    new \Tools\Console\Command\GenerateProxiesCommand($doctrineConfig['path']['proxies']),

    // Generate entities and repositories
    new \Tools\Console\Command\UpdateCommand($doctrineConfig['path']['entities']),

    // Migrations Commands
    new \Tools\Console\Command\DiffCommand(),
    new \Tools\Console\Command\ExecuteCommand(),
    new \Tools\Console\Command\GenerateCommand(),
    new \Tools\Console\Command\MigrateCommand(),
    new \Tools\Console\Command\StatusCommand(),
    new \Tools\Console\Command\VersionCommand()
));

$cli->run();

