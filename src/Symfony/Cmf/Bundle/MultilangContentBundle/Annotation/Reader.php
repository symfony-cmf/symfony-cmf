<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

require_once __DIR__.'/TranslationAnnotations.php';

/**
 * Annotation reader for the multilang properties
 *
 * @author brian.king (at) liip.ch
 */
class Reader {

    protected $reader;
    protected $propertyInformation;

    // TODO: this should be a singleton.

    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    public function translationInformation($className)
    {
        if (!isset($this->propertyInformation[$className])) {
            $info = new Information();
            $translatedProperties = array();

            $refClass = new \ReflectionClass($className);
            foreach($this->reader->getClassAnnotations($refClass) as $annotation) {
                if ($annotation instanceof Multilang) {
                    if (isset($annotation->strategy)) {
                        $info->setTranslationStrategy($annotation->strategy);
                    }
                }
            }

            $refProps = $refClass->getProperties();
            foreach ($refProps as $refProp) {
                $annotations = $this->reader->getPropertyAnnotations($refProp);

                if (count($annotations)) {
                    /* TODO: should we support a name parameter in the
                     * annotation for the jackalope node property name? here we
                     * always use the same name as the document property.
                     */
                    $refPropName = $refProp->getName();

                    foreach ($annotations as $annotation) {
                        if ($annotation instanceof Language) {
                            $info->setLanguageIndicator($refPropName);
                            $info->addTranslatedProperty($refPropName);
                        } else if ($annotation instanceof Translated) {
                            $info->addTranslatedProperty($refPropName);
                        }
                    }
                }
            }
            $this->propertyInformation[$className] = $info;
        }

        return $this->propertyInformation[$className];
    }
}
