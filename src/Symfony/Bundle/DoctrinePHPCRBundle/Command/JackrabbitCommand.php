<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use Jackalope\Tools\Console\Command\JackrabbitCommand as BaseJackrabbitCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class JackrabbitCommand extends BaseJackrabbitCommand implements ContainerAwareInterface
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:phpcr:jackrabbit')
            ->setHelp(<<<EOF
The <info>doctrine:phpcr:jackrabbit</info> command allows to have a minimal control on the Jackrabbit server from within a
Symfony 2 command.

If the <info>jackrabbit_jar</info> option is set, it will be used as the Jackrabbit server jar file.
Otherwise you will have to set the doctrine_phpcr.jackrabbit_jar config parameter to a valid Jackrabbit
server jar file.
EOF
            )
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
        if ($this->getContainer()->hasParameter('doctrine_phpcr.jackrabbit_jar')) {
            $this->setJackrabbitPath($this->getContainer()->getParameter('doctrine_phpcr.jackrabbit_jar'));
        }

        return parent::execute($input, $output);
    }



    /**
     * @var ContainerInterface
     */
    private $container;

    protected function getContainer()
    {
        if (null === $this->container) {
            $this->container = $this->getApplication()->getKernel()->getContainer();
        }

        return $this->container;
    }

    /**
     * @see ContainerAwareInterface::setContainer()
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
