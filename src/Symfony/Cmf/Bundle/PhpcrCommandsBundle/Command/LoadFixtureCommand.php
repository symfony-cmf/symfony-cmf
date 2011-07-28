<?php

/*
 * This file is part of the Symfony/Cmf/PhpcrCommandsBundle
 *
 * (c) Daniel Barsotti <daniel.barsotti@liip.ch>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Cmf\Bundle\PhpcrCommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\DoctrineFixturesBundle\Common\DataFixtures\Loader;

use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\Fixtures\PHPCRExecutor;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\Fixtures\PHPCRPurger;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\ConsoleParametersParser;

class LoadFixtureCommand extends ContainerAwareCommand
{
    /**
     * @var DocumentManager
     */
    protected $dm;


    protected function configure()
    {
        parent::configure();

        $this->setName('phpcr:fixtures:load')
            ->setDescription('Load fixtures PHPCR files')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'The path to the fixtures')
            ->addOption('purge', null, InputOption::VALUE_OPTIONAL, 'Set to true if the database must be purged')
            ->setHelp(<<<EOF
The <info>phpcr:fixtures:load</info> command loads PHPCR fixtures
EOF
        );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return integer 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('path');
        if (! is_dir($path)) {
            throw new \Exception("Invalid path '$path'");
        }

        $purge = false;
        if ($purge_option = $input->getOption('purge')) {
            $purge = ($purge_option == '1' || ConsoleParametersParser::isTrueString($purge_option));
        }

        $this->dm = $this->getContainer()->get('doctrine_phpcr.odm.default_document_manager');

        $loader = new Loader($this->getContainer());
        $loader->loadFromDirectory($path);

        $purger = new PHPCRPurger();
        $executor = new PHPCRExecutor($this->dm, $purger);
        $executor->execute($loader->getFixtures(), ! $purge);

        return 0;
    }
}
