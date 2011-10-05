<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\NodeHelper;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\ConsoleParametersParser;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeWalker;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper\ConsoleDumperNodeVisitor;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper\ConsoleDumperPropertyVisitor;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper\SystemNodeFilter;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class DumpCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:phpcr:dump')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
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
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));

        $path = $input->getArgument('path');

        $nodeVisitor = new ConsoleDumperNodeVisitor($output);

        $propVisitor = null;
        if (ConsoleParametersParser::isTrueString($input->getOption('props'))) {
            $propVisitor = new ConsoleDumperPropertyVisitor(
                $output,
                $this->getContainer()->getParameter('doctrine_phpcr.dump_max_line_length')
            );
        }

        $walker = new TreeWalker($nodeVisitor, $propVisitor);

        if (! ConsoleParametersParser::isTrueString($input->getOption('sys_nodes'))) {
            $filter = new SystemNodeFilter();
            $walker->addNodeFilter($filter);
            $walker->addPropertyFilter($filter);
        }

        $nodeHelper = new NodeHelper($this->getHelper('phpcr')->getSession());
        if ($root = $nodeHelper->getNode($path)) {
            $walker->traverse($root);
        } else {
            $output->writeln("Node '$path' not found");
        }

        return 0;
    }

}
