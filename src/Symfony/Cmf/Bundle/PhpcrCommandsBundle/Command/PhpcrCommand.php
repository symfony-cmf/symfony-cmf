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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\NodeHelper;


abstract class PhpcrCommand extends Command
{
    /**
     * @var JackalopeLoader
     */
    protected $jackalope_loader;

    /**
     * @var NodeHelper
     */
    protected $node_helper;


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->jackalope_loader = $this->container->get('jackalope.loader');
        $this->node_helper = new NodeHelper($this->jackalope_loader);
    }

}
