<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\NodeHelper;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeWalker;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper\ConsoleDumperNodeVisitor;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper\ConsoleDumperPropertyVisitor;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper\SystemNodeFilter;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class SqlCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:phpcr:sql')
            ->addArgument('sql', InputArgument::REQUIRED, 'JCR SQL statement to execute')
            ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
            ->setDescription('Execute a JCR SQL2 statement')
            ->setHelp("The <info>phpcr:sql</info> command executes a JCR SQL2 statement on the content repository");
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

        $sql = $input->getArgument('sql');

        $session = $this->getHelper('phpcr')->getSession();
        $qm = $session->getWorkspace()->getQueryManager();
        $query = $qm->createQuery($sql, \PHPCR\Query\QueryInterface::JCR_SQL2);

        $result = $query->execute();
        foreach ($result as $i => $row) {
            $values = $row->getValues();
            $output->writeln("\n".($i+1).'. Row:');
            foreach ($values as $column => $value) {
                $output->writeln("$column: $value");
            }
        }

        return 0;
    }
}
