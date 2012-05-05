<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Document\SimpleBlock;

class SimpleBlockService extends BaseBlockService implements BlockServiceInterface
{

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $form
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildEditForm() method.
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $form
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function buildCreateForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildCreateForm() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        // TODO: id and name contain slashes and dots but are used as css-id and -class: Do something about it
        // TODO: make sure only response-content gets printed
        if ($block->getEnabled()) {
            $response = $this->renderResponse('SymfonyCmfBlockBundle::block_simple.html.twig', array(
                'block'     => $block
            ), $response);
        }

        return $response;
    }

    /**
     * @param \Sonata\AdminBundle\Validator\ErrorElement $errorElement
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * Returns the default settings link to the service
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        // TODO: Implement getDefaultSettings() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function load(BlockInterface $block)
    {
        // TODO: Implement load() method.
    }

    /**
     * @param $media
     * @return array
     */
    public function getJavacripts($media)
    {
        // TODO: Implement getJavacripts() method.
    }

    /**
     * @param $media
     * @return array
     */
    public function getStylesheets($media)
    {
        // TODO: Implement getStylesheets() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return array
     */
    public function getCacheKeys(BlockInterface $block)
    {
        // TODO: Implement getCacheKeys() method.
    }
}
