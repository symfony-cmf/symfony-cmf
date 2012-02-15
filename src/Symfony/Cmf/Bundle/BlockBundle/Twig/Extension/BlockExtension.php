<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;

class BlockExtension extends \Twig_Extension
{
    protected $container;
    protected $odm;
    protected $blockServiceManager;


    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \use Doctrine\ODM\PHPCR\DocumentManager
     * @param \Sonata\BlockBundle\Model\BlockManagerInterface $blockServiceManager
     */
    public function __construct(ContainerInterface $container, DocumentManager $odm, BlockServiceManagerInterface $blockServiceManager)
    {
        $this->container = $container;
        $this->odm = $odm;
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
        $block = $this->odm->find(null,  $this->getCurrentPage()->getPath() . '/' . $name);

        if ($block) {
            return $this->blockServiceManager->renderBlock($block);
        } else {
            // TODO: What do we do in that case?
            return '';
        }

    }

    /**
     * @return \Symfony\Cmf\Bundle\ContentBundle\Document\StaticContent
     */
    protected function getCurrentPage() {
        return $this->container->get('request')->attributes->get('contentDocument');
    }
}

