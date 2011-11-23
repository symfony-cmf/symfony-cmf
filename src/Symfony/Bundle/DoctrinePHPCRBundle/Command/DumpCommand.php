<?php
namespace Symfony\Bundle\DoctrinePHPCRBundle\Command;

use PHPCR\Util\Console\Command\DumpCommand as BaseDumpCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class DumpCommand extends BaseDumpCommand implements ContainerAwareInterface
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:phpcr:dump')
             ->addOption('session', null, InputOption::VALUE_OPTIONAL, 'The session to use for this command')
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
        DoctrineCommandHelper::setApplicationPHPCRSession($this->getApplication(), $input->getOption('session'));
        if ($this->getContainer()->hasParameter('doctrine_phpcr.dump_max_line_length')) {
            $this->setDumpMaxLineLength($this->getContainer()->getParameter('doctrine_phpcr.dump_max_line_length'));
        }

        parent::execute($input, $output);
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
