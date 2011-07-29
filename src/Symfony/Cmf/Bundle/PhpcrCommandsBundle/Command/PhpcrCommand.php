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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\NodeHelper;


abstract class PhpcrCommand extends ContainerAwareCommand
{
    /**
     * @var \PHPCR\SessionInterface
     */
    protected $session;

    /**
     * @var NodeHelper
     */
    protected $node_helper;


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->session = $this->getContainer()->get('doctrine_phpcr.default_session'); // TODO: make it possible to specify which session
        $this->node_helper = new NodeHelper($this->session);
    }

}
