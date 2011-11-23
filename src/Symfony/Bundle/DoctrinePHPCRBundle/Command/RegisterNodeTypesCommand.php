<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use PHPCR\Util\Console\Command\RegisterNodeTypesCommand as BaseRegisterNodeTypesCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper to use RegisterNodeTypeCommand with Symfony's app/console
 *
 * @see Doctrine/ODM/PHPCR/Tools/Console/Command/RegisterNodeTypesCommand
 */
class RegisterNodeTypesCommand extends BaseRegisterNodeTypesCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:phpcr:register-node-types')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));

        return parent::execute($input, $output);
    }
}
