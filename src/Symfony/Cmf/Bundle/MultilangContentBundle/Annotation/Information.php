<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Annotation;

/**
 * Tranlsation annotation information for a document
 *
 * @author brian.king (at) liip.ch
 */
class Information {

    /** translations should be stored as child */
    const STRATEGY_CHILD = 1;
    /** translations should be stored as attributes */
    const STRATEGY_ATTRIBUTE = 2;

    /**
     * @var int
     * the translation strategy to use. must be one of the self:: constants
     */
    protected $translationStrategy = self::STRATEGY_CHILD;

    /** array of translated property names */
    protected $translatedProperties = array();

    /** string containing name of the language indicator property */
    protected $languageIndicator;

    public function setTranslationStrategy($label)
    {
        if (! strcasecmp('attribute', $label)) {
            $this->translationStrategy = self::STRATEGY_ATTRIBUTE;
        } else if (! strcasecmp('child', $label)) {
            $this->translationStrategy = self::STRATEGY_CHILD;
        } else {
            throw new \Exception("Unknown translation strategy $label");
        }
    }

    /**
     * @return int one of the strategy constant values
     */
    public function getTranslationStrategy()
    {
        return $this->translationStrategy;
    }

    /**
     * @param string $propertyName
     */
    public function addTranslatedProperty($propertyName)
    {
        $this->translatedProperties[]= $propertyName;
    }

    /**
     * @param array $translatedProperties
     */
    public function setTranslatedProperties($translatedProperties)
    {
        $this->translatedProperties = $translatedProperties;
    }

    /**
     * @return array of translated property names
     */
    public function getTranslatedProperties()
    {
        return $this->translatedProperties;
    }

    /**
     * @param string $languageIndicator name of language indicator property
     */
    public function setLanguageIndicator($languageIndicator)
    {
        $this->languageIndicator = $languageIndicator;
    }

    /**
     * @return string language indicator property
     */
    public function getLanguageIndicator()
    {
        return $this->languageIndicator;
    }

    /**
     * @return bool
     */
    public function isTranslatable()
    {
        return (!is_null($this->languageIndicator) && count($this->translatedProperties) > 0);
    }
}
