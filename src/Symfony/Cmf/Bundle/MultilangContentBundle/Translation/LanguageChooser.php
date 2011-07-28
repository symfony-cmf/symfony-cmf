<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Translation;

use Symfony\Component\HttpFoundation\Session;

/**
 * Class to get the list of preferred languages.
 *
 * The order is defined as list of language codes per language.
 *
 * TODO: extract interface so we can implement and plug in other strategies
 *
 * @author brian.king (at) liip.ch
 */
class LanguageChooser {

    protected $session;
    protected $langPreference;
    protected $defaultLang;
    protected $preferred;
    protected $langMeta;

    /**
     * @param Session $session the web request session to get the current
     *      locale from
     * @param array $lang_preference array of arrays with a language order list
     *      for each language
     * @param string $default_lang the default language
     * @param array $lang_meta meta information about the languages in the
     *      lang_preferences. keys are lang code, values is array as returned
     *      by getLanguageMeta
     */
    public function __construct(Session $session, $lang_preference, $default_lang, $lang_meta) {
        $this->session = $session;
        $this->langPreference = $lang_preference;
        $this->defaultLang = $default_lang;
        $this->langMeta = $lang_meta;
    }

    /**
     * Gets an ordered list of preferred languages.
     *
     * @return array $preferredLanguages
     */
    public function getPreferredLanguages() {
        if (is_null($this->preferred)) {
            $this->setPreferredLanguage($this->session->getLocale());
        }
        return $this->preferred;
    }

    /**
     * Get the ordered list of languages in default order
     *
     * @return array preferred language order for the default language
     */
    public function getDefaultLanguages() {
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
        return $this->langMeta[$lang];
    }

    /**
     * Set the preferred language.
     *
     * If it is not available, the default language is used.
     * @param string $lang the language to set.
     */
    protected function setPreferredLanguage($lang) {
        // Use the default language for lang preferences if the given language is not one of the available languages.
        if (!in_array($lang, array_keys($this->langPreference))) {
            $this->preferred = $this->langPreference[$this->defaultLang];
        } else {
            $this->preferred = $this->langPreference[$lang];
        }
    }
}
