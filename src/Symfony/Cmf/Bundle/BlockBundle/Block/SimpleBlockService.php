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
    function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildEditForm() method.
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $form
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    function buildCreateForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildCreateForm() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if ($block->getEnabled()) {
            //$response->setContent($block->getTitle() . $block->getContent());
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
    function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * @return string
     */
    function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * Returns the default settings link to the service
     *
     * @return array
     */
    function getDefaultSettings()
    {
        // TODO: Implement getDefaultSettings() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    function load(BlockInterface $block)
    {
        // TODO: Implement load() method.
    }

    /**
     * @param $media
     * @return array
     */
    function getJavacripts($media)
    {
        // TODO: Implement getJavacripts() method.
    }

    /**
     * @param $media
     * @return array
     */
    function getStylesheets($media)
    {
        // TODO: Implement getStylesheets() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return array
     */
    function getCacheKeys(BlockInterface $block)
    {
        // TODO: Implement getCacheKeys() method.
    }
}
