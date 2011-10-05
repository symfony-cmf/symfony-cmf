<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\JackrabbitHelper;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class JackrabbitCommand extends ContainerAwareCommand
{
    /**
     * Path to Jackrabbit jar file
     * @var string
     */
    protected $jrpath;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:phpcr:jackrabbit')
            ->addArgument('cmd', InputArgument::REQUIRED, 'Command to execute (start | stop | status)')
            ->addOption('jackrabbit_jar', null, InputOption::VALUE_OPTIONAL, 'Path to the Jackrabbit jar file')
            ->setDescription('Start and stop the Jackrabbit server')
            ->setHelp(<<<EOF
The <info>phpcr:jackrabbit</info> command allows to have a minimal control on the Jackrabbit server from within a
Symfony 2 command.

If the <info>jackrabbit_jar</info> option is set, it will be used as the Jackrabbit server jar file.
Otherwise you will have to set the doctrine_phpcr.jackrabbit_jar config parameter to a valid Jackrabbit
server jar file.
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
        $cmd = $input->getArgument('cmd');

        if (! in_array(strtolower($cmd), array('start', 'stop', 'status'))) {
            $output->writeln($this->asText());
            return 1;
        }

        $jar = $input->getOption('jackrabbit_jar');
        if ($jar) {
            $this->jrpath = $jar;
        } elseif ($this->getContainer()->hasParameter('doctrine_phpcr.jackrabbit_jar')) {
            $this->jrpath = $this->getContainer()->getParameter('doctrine_phpcr.jackrabbit_jar');
        }

        if (!file_exists($this->jrpath)) {
            throw new \Exception("Invalid Jackrabbit JAR file ' {$this->jrpath}'");
        }

        $helper = new JackrabbitHelper($this->jrpath);

        switch(strtolower($cmd)) {
            case 'start':
                $helper->startServer();
                break;
            case 'stop':
                $helper->stopServer();
                break;
            case 'status':
                $output->writeln("Jackrabbit server " . ($helper->isServerRunning() ? 'is running' : 'is not running'));
                break;
        }

        return 0;
    }
}
