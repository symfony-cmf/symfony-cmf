<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\BlockBundle\Model\BlockInterface;

class BlockServiceManager implements BlockServiceManagerInterface
{

    protected $blockServices;
    protected $container;


    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container     = $container;
        $this->blockServices = array();
    }

    /**
     * @param string $name
     * @param string $service
     * @return void
     */
    public function addBlockService($name, $service)
    {
        // TODO: Implement addBlockService() method.
    }

    /**
     * Render a block
     *
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderBlock(BlockInterface $block)
    {
        return new \Symfony\Component\HttpFoundation\Response('Render block of type: ' . $block->getType());
    }

    /**
     * Return the block service linked to the link
     *
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return BlockServiceInterface
     */
    public function getBlockService(BlockInterface $block)
    {
        // TODO: Implement getBlockService() method.
    }

    /**
     * @param array $blockServices
     * @return void
     */
    public function setBlockServices(array $blockServices)
    {
        // TODO: Implement setBlockServices() method.
    }

    /**
     * @return array
     */
    public function getBlockServices()
    {
        // TODO: Implement getBlockServices() method.
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasBlockService($name)
    {
        return isset($this->blockServices[$name]) ? true : false;
    }

}
