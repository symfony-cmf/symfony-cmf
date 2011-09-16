<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\DoctrineFixturesBundle\Common\DataFixtures\Loader;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\Fixtures\PHPCRExecutor;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\Fixtures\PHPCRPurger;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\ConsoleParametersParser;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class LoadFixtureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('phpcr:fixtures:load')
            ->setDescription('Load fixtures PHPCR files')
            ->addOption('document_manager', null, InputOption::VALUE_OPTIONAL, 'The document manager to use for this command')
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
        DoctrineCommandHelper::setApplicationDocumentManager($this->getApplication(), $input->getOption('document_manager'));

        $path = $input->getOption('path');
        if (! is_dir($path)) {
            throw new \Exception("Invalid path '$path'");
        }

        $purge = false;
        if ($purgeOption = $input->getOption('purge')) {
            $purge = ($purgeOption == '1' || ConsoleParametersParser::isTrueString($purgeOption));
        }

        $dm = $this->getHelper('phpcr')->getDocumentManager();

        $loader = new Loader($this->getContainer());
        $loader->loadFromDirectory($path);

        $purger = new PHPCRPurger();
        $executor = new PHPCRExecutor($dm, $purger);
        $executor->execute($loader->getFixtures(), ! $purge);

        return 0;
    }
}
