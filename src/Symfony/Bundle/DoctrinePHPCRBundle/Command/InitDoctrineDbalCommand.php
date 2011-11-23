<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Jackalope\Tools\Console\Command\InitDoctrineDbalCommand as BaseInitDoctrineDbalCommand;

class InitDoctrineDbalCommand extends BaseInitDoctrineDbalCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:phpcr:init:dbal')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));

        parent::execute($input, $output);
    }
}
