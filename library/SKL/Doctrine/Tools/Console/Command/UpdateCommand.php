<?php
/*
 * Generate entities, repositories
 */

namespace Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console,
    Doctrine\ORM\Tools\Console\MetadataFilter,
    Doctrine\ORM\Tools\EntityGenerator,
    Doctrine\ORM\Tools\DisconnectedClassMetadataFactory,
    Doctrine\ORM\Tools\EntityRepositoryGenerator;
/**
 * Command to (re)generate the proxy classes used by doctrine.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.0
 * @version $Revision$
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 */
class UpdateCommand extends Console\Command\Command
{
    /**
     * Path to models directory
     * @var string
     */
    protected $_destinationPath;


    public function __construct($destinationPath = null, $name = null)
    {
        $this->_destinationPath = dirname($destinationPath);
        parent::__construct($name);
    }
    
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('orm:update')
        ->setDescription('Generates entities and repositories')
        ->setDefinition(array(
            new InputOption(
                'filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'A string pattern used to match entities that should be processed.'
            ),
            new InputArgument(
                'dest-path', InputArgument::OPTIONAL,
                'The path to generate your classes.'
            ),
        ))
        ->setHelp(<<<EOT
Generates classes.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $em = $this->getHelper('em')->getEntityManager();

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);
        $metadatas = $cmf->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $input->getOption('filter'));

        if ( $this->_destinationPath ) {
            $destPath = $this->_destinationPath;
        } else {
            // Process destination directory
            $destPath = realpath($input->getArgument('dest-path'));
        }

        if ( ! file_exists($destPath)) {
            throw new \InvalidArgumentException(
                sprintf("Entities destination directory '<info>%s</info>' does not exist.", $destPath)
            );
        } else if ( ! is_writable($destPath)) {
            throw new \InvalidArgumentException(
                sprintf("Entities destination directory '<info>%s</info>' does not have write permissions.", $destPath)
            );
        }

        if (count($metadatas)) {
            // Create EntityGenerator
            $entityGenerator = new EntityGenerator();

            $entityGenerator->setGenerateAnnotations(null);
            $entityGenerator->setGenerateStubMethods(null);
            $entityGenerator->setRegenerateEntityIfExists(null);
            $entityGenerator->setUpdateEntityIfExists(null);
            $entityGenerator->setNumSpaces(null);

            foreach ($metadatas as $metadata) {
                $output->write(
                    sprintf('Processing entity "<info>%s</info>"', $metadata->name) . PHP_EOL
                );
            }

            // Generating Entities
            $entityGenerator->generate($metadatas, $destPath);

            // Outputting information message
            $output->write(PHP_EOL . sprintf('Entity classes generated to "<info>%s</INFO>"', $destPath) . PHP_EOL);
        } else {
            $output->write('No Metadata Classes to process.' . PHP_EOL);
        }

        if (count($metadatas)) {
            $numRepositories = 0;
            $generator = new EntityRepositoryGenerator();

            foreach ($metadatas as $metadata) {
                if ($metadata->customRepositoryClassName) {
                    $output->write(
                        sprintf('Processing repository "<info>%s</info>"', $metadata->customRepositoryClassName) . PHP_EOL
                    );

                    $generator->writeEntityRepositoryClass($metadata->customRepositoryClassName, $destPath);

                    $numRepositories++;
                }
            }

            if ($numRepositories) {
                // Outputting information message
                $output->write(PHP_EOL . sprintf('Repository classes generated to "<info>%s</INFO>"', $destPath) . PHP_EOL);
            } else {
                $output->write('No Repository classes were found to be processed.' . PHP_EOL);
            }
        } else {
            $output->write('No Metadata Classes to process.' . PHP_EOL);
        }

    }
}
