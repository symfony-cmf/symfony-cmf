<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Translation;

use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Cmf\Bundle\MultilangContentBundle\Annotation\Information;

/**
 * A document translator that stores all translated properties in a language
 * specific child node.
 *
 * @author brian.king (at) liip.ch
 */
class SimpleNodeTranslator
{
    /**
     * ContainerInterface
     */
    protected $container;

    /**
     * DocumentManager
    */
    protected $odm;

    /** jackalope session */
    protected $session;

    /** helper to get document annoations for translatable properties */
    protected $reader;

    protected $langPrefix;

    /**
     * @param ContainerInterface $container
     * @param DocumentManager $odm
     * @param object $jackalope_loader to get the PHPCR session. TODO: pass phpcr session directly?
     * @param object $annotation_reader the annotation reader to use
     * @param string $lang_prefix the prefix for the language child node. TODO: should use a namespace for this.
     */
    public function __construct(ContainerInterface $container, DocumentManager $odm, $jackalope_loader, $annotation_reader, $lang_prefix)
    {
        $this->container = $container;
        $this->odm = $odm;
        $this->session = $jackalope_loader->getSession();
        $this->reader = $annotation_reader;
        $this->langPrefix = $lang_prefix;
    }


    /**
     * Persist a new Document to the data store, and add the appropriate child translation node.
     * If an existing, attached Document is passed in and updateExistingParent is true,
     * the existing Document will be stored.
     *
     * @param Document $document
     * @param bool=false $updateExistingParent
     *
     * @throws InavlidArgumentException if the document is not a suitable document (missing getNode method)
     * @throws Jackalope\Transport\Davex\HTTPErrorException (HTTP 405 Method
     *    Not Allowed: MKCOL) when passing a non-attached Document with a path
     *    that already exists in the repository. TODO: jackalope should throw a PHPCR exception
     */
    public function persistTranslation($document, $updateExistingParent=false)
    {
        if (! is_object($document) || ! method_exists($document, 'getNode')) {
            //test this case. we can not type hint and otherwise the exception can be very weird or even fatal error
            throw new \InvalidArgumentException('document parameter is not a valid Document, it misses the getNode method');
        }

        $translationInfo = $this->reader->translationInformation(get_class($document));

        if (!$translationInfo->isTranslatable()) {
            throw new \InvalidArgumentException("this document has no translated fields: ".get_class($document));
        }

        if (!$document->getNode() || $updateExistingParent) {
            $this->odm->persist($document);
            // Flush to store so that the document will have an associated node, but do not commit
            $this->odm->flushNoSave();
        }

        $languageField = $translationInfo->getLanguageIndicator();

        switch($translationInfo->getTranslationStrategy()) {
            case Information::STRATEGY_CHILD:
                $this->persistTranslationNode($document, $translationInfo, $this->langPrefix . $document->$languageField);
                break;
            case Information::STRATEGY_ATTRIBUTE:
                $this->persistTranslationAttribute($document, $translationInfo, $this->langPrefix . $document->$languageField);
                break;
            default:
                throw new \Exception('Unknown translation strategy constant '.$translationInfo->getTranslationStrategy());
        }
    }

    protected function persistTranslationNode($document, $translationInfo, $childName)
    {
        $node = $document->getNode();
        if ($node->hasNode($childName)) {
            $childNode = $node->getNode($childName);
        } else {
            $childNode = $node->addNode($childName);
        }

        $langprop = $translationInfo->getLanguageIndicator();
        foreach ($translationInfo->getTranslatedProperties() as $property) {
            if ($langprop != $property) {
                $childNode->setProperty($property, $document->$property);
            }
        }

        //TODO: Remove translation properties from pre-existing child node if they are not set in this document.
        //      But how to tell if they are (or even were once but are no longer) translation properties?
    }

    protected function persistTranslationAttribute($document, $translationInfo, $attributePrefix)
    {
        $node = $document->getNode();
        $langprop = $translationInfo->getLanguageIndicator();
        foreach ($translationInfo->getTranslatedProperties() as $property) {
            if ($langprop != $property) {
                $node->setProperty("$attributePrefix-$property", $document->$property);
            }
        }
    }
}
