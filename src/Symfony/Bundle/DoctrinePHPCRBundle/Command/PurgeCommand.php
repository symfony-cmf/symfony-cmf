<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use PHPCR\Util\Console\Command\PurgeCommand as BasePurgeCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class PurgeCommand extends BasePurgeCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:phpcr:purge')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
        ;
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

        return parent::execute($input, $output);
    }
}
