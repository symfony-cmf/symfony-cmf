<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Translation;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class to get the list of preferred languages.
 *
 * The order is defined as list of language codes per language.
 *
 * TODO: extract interface so we can implement and plug in other strategies.
 * getPreferredLanguages might be a problem as some strategies may not care
 * about the request language.
 *
 * @author brian.king (at) liip.ch
 */
class LanguageChooser
{
    protected $langPreference;
    protected $defaultLang;
    protected $langMeta;

    /**
     * @param array $lang_preference array of arrays with a language order list
     *      for each language
     * @param string $default_lang the default language
     * @param array $lang_meta meta information about the languages in the
     *      lang_preferences. keys are lang code, values is array as returned
     *      by getLanguageMeta
     */
    public function __construct($request, $lang_preference, $default_lang, $lang_meta)
    {
        $this->langPreference = $lang_preference;
        $this->defaultLang = $default_lang;
        $this->langMeta = $lang_meta;
    }

    /**
     * Gets an ordered list of preferred languages.
     *
     * @param string $for_lang for which language you need the language order, i.e. the current request language
     *
     * @return array $preferredLanguages
     */
    public function getPreferredLanguages($for_lang=null)
    {
        // Use the default language for lang preferences if the given language is not one of the available languages.
        if (!in_array($for_lang, array_keys($this->langPreference))) {
            $preferred = $this->langPreference[$this->defaultLang];
        } else {
            $preferred = $this->langPreference[$for_lang];
        }
        return $preferred;
    }

    /**
     * Get the ordered list of languages in default order
     *
     * @return array preferred language order for the default language
     */
    public function getDefaultLanguages()
    {
        return $this->langPreference[$this->defaultLang];
    }

    /**
     * Get meta information for a language
     *
     * fullname: full language name.
     * completion: part to append after $lang to make this language a readable name
     *
     * @param string $lang the language code
     * @return array with fields fullname, completion
     */
    public function getLanguageMeta($lang)
    {
        if (! array_key_exists($lang, $this->langMeta)) {
            throw new \IllegalArgumentException("No meta for $lang");
        }
        return $this->langMeta[$lang];
    }
}
