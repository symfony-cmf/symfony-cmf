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

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\NodeHelper;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\ConsoleParametersParser;

class PurgeCommand extends PhpcrCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('phpcr:purge')
            ->setDescription('Purge the content repository')
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
        $force = ConsoleParametersParser::isTrueString($input->getOption('force'));

        if (! $force) {
            $dialog = new DialogHelper();
            $res = $dialog->askConfirmation($output, 'Are you sure you want to delete all the nodes of the content repository?', false);
        }

        if ($force || $res) {
            $this->node_helper->deleteAllNodes();
            $output->writeln("Done\n");
        }

        return 0;
    }
}
