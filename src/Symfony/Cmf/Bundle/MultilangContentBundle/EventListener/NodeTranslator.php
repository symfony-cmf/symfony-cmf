<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\PHPCR\Event;
use Doctrine\ODM\PHPCR\Event\LifecycleEventArgs;

use Symfony\Cmf\Bundle\MultilangContentBundle\Annotation\Information;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An event listener to load the best available languague into a document when
 * the document is loaded.
 *
 * TODO: can we also automatically trigger on the persist event?
 *
 * @author brian.king (at) liip.ch
 */
class NodeTranslator implements EventSubscriber
{
    protected $reader;
    protected $langHelper;
    protected $langPrefix;
    protected $container;

    /**
     * @param object $annotation_reader the annotation reader to find out which properties are translated
     * @param Container $container the container to get the request from to get the locale from (can't inject the request because of scope issues)
     * @param object $lang_helper the language chooser
     * @param object $lang_prefix the translation child prefix. TODO: should use a namespace for this.
     */
    public function __construct(ContainerInterface $container, $annotation_reader, $lang_helper, $lang_prefix)
    {
        $this->container = $container;
        $this->reader = $annotation_reader;
        $this->langHelper = $lang_helper;
        $this->langPrefix = $lang_prefix;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Event::postLoad,
#            Event::postPersist,
#            Event::postUpdate,
        );
    }

    protected function getLocale()
    {
        return $this->container->get('request')->getLocale();
    }

    /**
     * Load the translatable properties from a subnode into the Node being read.
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();

        $translationInfo = $this->reader->translationInformation(get_class($document));
        if (!$translationInfo->isTranslatable()) {
            return;
        }

        switch($translationInfo->getTranslationStrategy()) {
            case Information::STRATEGY_CHILD:
                $this->loadTranslationFromNode($document, $translationInfo);
                break;
            case Information::STRATEGY_ATTRIBUTE:
                $this->loadTranslationFromAttribute($document, $translationInfo);
                break;
            default:
                throw new \Exception('Unknown translation strategy constant '.$translationInfo->getTranslationStrategy());
        }
    }

    protected function loadTranslationFromNode($document, $translationInfo)
    {
        $node = $document->getNode();
        // Get the best language for this user.
        $langs = $this->langHelper->getPreferredLanguages($this->getLocale());

        $child = null;
        foreach ($langs as $lang) {
            $childNodeName = $this->langPrefix . $lang;
            if ($node->hasNode($childNodeName)) {
                $child = $node->getNode($childNodeName);
                break;
            }
        }

        if (!$child) {
            // no translation found
            return;
        }

        foreach ($translationInfo->getTranslatedProperties() as $property) {
            if ($child->hasProperty($property)) {
                $document->$property = $child->getPropertyValue($property);
            }
        }

        $document->{$translationInfo->getLanguageIndicator()} = $lang;
    }

    protected function loadTranslationFromAttribute($document, $translationInfo)
    {
        $node = $document->getNode();
        $props = null;

        // Get the best language for this user.
        $langs = $this->langHelper->getPreferredLanguages($this->getLocale());
        foreach ($langs as $lang) {
            $prefix = $this->langPrefix . $lang .'-';
            if ($props = $node->getPropertiesValues($prefix.'*')) {
                break;
            }
        }

        foreach ($translationInfo->getTranslatedProperties() as $property) {
            if (isset($props[$prefix.$property])) {
                $document->$property = $props[$prefix.$property];
            }
        }
        $document->{$translationInfo->getLanguageIndicator()} = $lang;
    }

    /**
     * Store the translatable fields into an appropriate child Node after the current Node has been inserted.
     */
    /*
     * TODO: sync this with SimpleNodeTranslator if we go with an event way to store data
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->storeToChildNode($eventArgs->getDocument(), 'persist');
    }
    */
    /**
     * Store the translatable fields into an appropriate child Node after the current Node has been updated.
     */
    /*
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->storeToChildNode($eventArgs->getDocument(), 'update');
    }
    */
    /**
     * Stores translatable fields in a child node.
     * TODO: can this work without excessive calls to session->save()?
     */
    /*
    protected function storeToChildNode($document, $eventType)
    {
        $translationInfo = $this->reader->translationInformation(get_class($document));

        if (!$translationInfo->isTranslatable()) {
            return;
        }

        $node = $document->getNode();

        $languageField = $translationInfo->getLanguageIndicator();
        $childName = $this->langPrefix . $document->$languageField;

        if ($node->hasNode($childName)) {
            $childNode = $node->getNode($childName);
        } else {
            $childNode = $node->addNode($childName);
        }
        foreach ($translationInfo->getTranslatedProperties() as $property) {
            if (isset($document->$property)) {
                $childNode->setProperty($property, $document->$property);
            }
        }
        $this->session->save();
    }
    */
}
