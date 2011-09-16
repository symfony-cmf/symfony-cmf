<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\NodeHelper;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\ConsoleParametersParser;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class PurgeCommand extends DoctrineCommandHelper
{
    protected function configure()
    {
        parent::configure();

        $this->setName('phpcr:purge')
            ->setDescription('Purge the content repository')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Set to "yes" to bypass the confirmation dialog', "no")
            ->setHelp(<<<EOF
The <info>phpcr:purge</info> command remove all the non-standard nodes from the content repository
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
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));

        $force = ConsoleParametersParser::isTrueString($input->getOption('force'));

        if (! $force) {
            $dialog = new DialogHelper();
            $res = $dialog->askConfirmation($output, 'Are you sure you want to delete all the nodes of the content repository?', false);
        }

        if ($force || $res) {
            $nodeHelper = new NodeHelper($this->getHelper('phpcr')->getSession());
            $nodeHelper->deleteAllNodes();
            $output->writeln("Done\n");
        }

        return 0;
    }
}
