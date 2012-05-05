<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;

class PHPCRBlockLoader implements BlockLoaderInterface
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
     * {@inheritdoc}
     */
    public function load($configuration)
    {
        if ($this->support($configuration)) {
            return $this->findByName($configuration['name']);
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function support($configuration)
    {
        if (!is_array($configuration)) {
            return false;
        }

        if (!isset($configuration['name'])) {
            return false;
        }

        return true;
    }

    /**
     * Finds one block by the given name
     *
     * @param string $name
     *
     * @return BlockInterface
     */
    public function findByName($name)
    {
        if ($this->isAbsolutePath($name)) {
            return $this->odm->find(null, $name);
        } else {
            $currentPage = $this->container->get('request')->attributes->get('contentDocument');
            return $this->odm->find(null,  $currentPage->getPath() . '/' . $name);
        }

        return null;
    }


    /**
     * @param \string $path
     *
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return substr($path, 0, 1) == '/';
    }

}