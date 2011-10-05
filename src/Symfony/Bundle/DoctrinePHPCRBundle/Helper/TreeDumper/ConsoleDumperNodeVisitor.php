<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper;

use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\ItemVisitorInterface;
use PHPCR\ItemInterface;
use PHPCR\NodeInterface;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class ConsoleDumperNodeVisitor implements ItemVisitorInterface
{
    protected $output;

    protected $level = 0;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function visit(ItemInterface $item)
    {
        if (! $item instanceof NodeInterface) {
            throw new \Exception("Internal error: did not expect to visit a non-node object: $item");
        }

        $name = $item->getName();

        // TODO: is there a better way to detect root node?
        $is_root = $item->getPrimaryNodeType()->isNodeType('rep:root');
        if ($is_root) {
            $name = 'ROOT';
        }

        $this->output->writeln(str_repeat('  ', $this->level) . '<comment>' . $name . '</comment>:');
    }
}
