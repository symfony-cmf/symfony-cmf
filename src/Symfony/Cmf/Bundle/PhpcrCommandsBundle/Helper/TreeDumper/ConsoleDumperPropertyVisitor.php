<?php

/*
 * This file is part of the Symfony/Cmf/PhpcrCommandsBundle
 *
 * (c) Daniel Barsotti <daniel.barsotti@liip.ch>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeDumper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PHPCR\ItemVisitorInterface;
use PHPCR\ItemInterface;
use PHPCR\PropertyInterface;

class ConsoleDumperPropertyVisitor implements ItemVisitorInterface, ContainerAwareInterface
{
    protected $container;

    protected $output;

    protected $level = 0;

    protected $max_line_length = 120;
    
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function visit(ItemInterface $item)
    {
        if (! $item instanceof PropertyInterface) {
            throw new \Exception("Internal error: did not expect to visit a non-node object: $item");
        }

        $this->max_line_length = $this->container->getParameter('phpcr_commands.dump_max_line_length');

        $value = $item->getString();

        if (! is_string($value)) {
            $value = print_r($value, true);
        }

        if (strlen($value) > $this->max_line_length) {
            $value = substr($value, 0, $this->max_line_length) . '...';
        }

        $value = str_replace(array("\n", "\t"), '', $value);

        $this->output->writeln(str_repeat('  ', $this->level + 1) . '- <info>' . $item->getName() . '</info> = ' . $value);
    }
}
