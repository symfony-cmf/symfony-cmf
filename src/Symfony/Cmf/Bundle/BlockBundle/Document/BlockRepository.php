<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Sonata\BlockBundle\Model\BlockRepositoryInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;

class BlockRepository implements BlockRepositoryInterface
{

    protected $container;
    protected $odm;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \use Doctrine\ODM\PHPCR\DocumentManager
     */
    public function __construct(ContainerInterface $container, DocumentManager $odm)
    {
        $this->container = $container;
        $this->odm = $odm;
    }

    /**
     * Creates an empty block instance
     *
     * @return BlockInterface
     */
    function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * Deletes a block
     *
     * @param BlockInterface $block
     *
     * @return void
     */
    function delete(BlockInterface $block)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Finds one block by the given criteria
     *
     * @param array $criteria
     *
     * @return BlockInterface
     */
    function findOneBy(array $criteria)
    {
        // TODO: this is weird - the repository should get the full path as an argument. Adapt twig-extension in BlockBundle?
        if (array_key_exists('name', $criteria)) {
            $currentPage = $this->container->get('request')->attributes->get('contentDocument');
            return $this->odm->find(null,  $currentPage->getPath() . '/' . $criteria['name']);
        }

        return null;
    }

    /**
     * Finds one block by the given criteria
     *
     * @param array $criteria
     *
     * @return BlockInterface
     */
    function findBy(array $criteria)
    {
        // TODO: Implement findBy() method.
    }

    /**
     * Returns the block's fully qualified class name
     *
     * @return string
     */
    function getClass()
    {
        // TODO: Implement getClass() method.
    }

    /**
     * Save a block
     *
     * @param BlockInterface $block
     *
     * @return void
     */
    function save(BlockInterface $block)
    {
        // TODO: Implement save() method.
    }
}
