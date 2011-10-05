<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Doctrine\ODM\PHPCR\Tools\Console\Command\RegisterSystemNodeTypesCommand as BaseRegisterSystemNodeTypesCommand;
use Doctrine\ODM\PHPCR\Tools\Console\Helper\DocumentManagerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper to use RegisterSystemNodeTypeCommand with Symfony's app/console
 *
 * @see Doctrine/ODM/PHPCR/Tools/Console/Command/RegisterSystemNodeTypesCommand
 */
class RegisterSystemNodeTypesCommand extends BaseRegisterSystemNodeTypesCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:phpcr:register-system-node-types')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));

        parent::execute($input, $output);
    }
}
