<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper;

use Symfony\Component\Console\Output\OutputInterface;

use PHPCR\ItemVisitorInterface;
use PHPCR\ItemInterface;
use PHPCR\PropertyInterface;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class ConsoleDumperPropertyVisitor implements ItemVisitorInterface
{
    protected $output;

    protected $level = 0;

    protected $maxLineLength = 120;
    
    public function __construct(OutputInterface $output, $maxLineLength = null)
    {
        $this->output = $output;

        if (null !== $maxLineLength) {
            $this->maxLineLength = $maxLineLength;
        }
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

        $value = $item->getString();

        if (! is_string($value)) {
            $value = print_r($value, true);
        }

        if (strlen($value) > $this->maxLineLength) {
            $value = substr($value, 0, $this->maxLineLength) . '...';
        }

        $value = str_replace(array("\n", "\t"), '', $value);

        $this->output->writeln(str_repeat('  ', $this->level + 1) . '- <info>' . $item->getName() . '</info> = ' . $value);
    }
}
