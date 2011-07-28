<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Doctrine\ODM\PHPCR\Tools\Console\Command\RegisterNodeTypesCommand as BaseRegisterNodeTypesCommand;
use Doctrine\ODM\PHPCR\Tools\Console\Helper\DocumentManagerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper to use RegisterNodeTypeCommand with Symfony's app/console
 *
 * @see Doctrine/ODM/PHPCR/Tools/Console/Command/RegisterNodeTypesCommand
 * 
 * TODO: a option for different document manager once this is implemented
 */
class RegisterNodeTypesCommand extends BaseRegisterNodeTypesCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:phpcr:register-node-types')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));

        parent::execute($input, $output);
    }  
}
