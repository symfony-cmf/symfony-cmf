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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\NodeHelper;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\ConsoleParametersParser;

use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeWalker;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeDumper\ConsoleDumperNodeVisitor;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeDumper\ConsoleDumperPropertyVisitor;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeDumper\SystemNodeFilter;

class DumpCommand extends PhpcrCommand
{
    /**
     * @var boolean
     */
    protected $dump_sys;

    /**
     * @var boolean
     */
    protected $dump_props;


    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('phpcr:dump')
            ->addOption('sys_nodes', null, InputOption::VALUE_OPTIONAL, 'Set to "yes" to dump the system nodes', "no")
            ->addOption('props', null, InputOption::VALUE_OPTIONAL, 'Set to "yes" to dump the node properties', "no")
            ->addArgument('path', InputArgument::OPTIONAL, 'Path of the node to dump', '/')
            ->setDescription('Dump the content repository')
            ->setHelp("The <info>phpcr:dump</info> command dumps a node (specified by the <info>path</info> argument) and its subnodes in a yaml-like style.\n\nIf the <info>props</info> option is set to yes the nodes properties are displayed as yaml arrays.\nBy default the command filters out system nodes and properties (i.e. nodes and properties with names starting with 'jcr:'), the <info>sys_nodes</info> option allows to turn this filter off.");
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
        $path = $input->getArgument('path');

        $node_visitor = new ConsoleDumperNodeVisitor($output);

        $prop_visitor = null;
        if (ConsoleParametersParser::isTrueString($input->getOption('props'))) {
            $prop_visitor = new ConsoleDumperPropertyVisitor($output);
            $prop_visitor->setContainer($this->getContainer());
        }

        $walker = new TreeWalker($node_visitor, $prop_visitor);

        if (! ConsoleParametersParser::isTrueString($input->getOption('sys_nodes'))) {
            $filter = new SystemNodeFilter();
            $walker->addNodeFilter($filter);
            $walker->addPropertyFilter($filter);
        }

        if ($root = $this->node_helper->getNode($path)) {
            $walker->traverse($root);
        } else {
            $output->writeln("Node '$path' not found");
        }

        return 0;
    }

}
