<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Sonata\BlockBundle\Block\BlockServiceManagerInterface;

class BlockExtension extends \Twig_Extension
{
    private $blockServiceManager;

    /**
     * @param \Sonata\BlockBundle\Model\BlockManagerInterface $blockServiceManager
     */
    public function __construct(BlockServiceManagerInterface $blockServiceManager = null)
    {
        $this->blockServiceManager = $blockServiceManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'symfony_cmf_block'  => new \Twig_Function_Method($this, 'renderBlock', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'symfony_cmf_block';
    }

    /**
     * @param $name
     * @return string
     */
    public function renderBlock($name)
    {
        return 'Render block with name: ' . $name;
        //return $this->blockServiceManager->renderBlock($name);
    }
}

