# A first draft of a multilanguage bundle.

This is the multilanguage bundle for the Symfony2 content management framework.
See http://cmf.symfony-project.org/ for more information about the CMF.

This bundle provides annotations for multilanguage documents and a helper
service to render a language chooser.

# Features

* New document annotations for multilanguage documents. Translations are stored
    as attributes or child nodes of the node.
* A language preferences service
* A service to render a language chooser to switch between the languages of the
    current page

# Usage

## Annotations

To use the annotations, you need to have registered them in the AnnotationRegistry

    AnnotationRegistry::registerFile(__DIR__.'/../vendor/symfony-cmf/src/Symfony/Cmf/Bundle/MultilangContentBundle/Annotation/TranslationAnnotations.php');


* ``@cmfTranslate\Multilang`` => class annotation for general options. currently has attribute ``strategy``
* Multilang attribute ``strategy`` => whether to store translated fields in a translation child node or in translation attributes
* ``@cmfTranslate\Language`` => language this document is currently in
* ``@cmfTranslate\Translated`` => language specific field

Documents can use ``phpcrodm`` annotations for language independent fields, but
translated fields may have no phpcrodm annotation or they will be stored twice.

On loading, the best available language is automatically loaded into the
document, you don't need to do anything special. The bundle registers for the
doctrine events and acts on postLoad.

For storing documents, you need the service
``symfony_cmf_multilang_content.node_translation`` and call
``persistTranslation($document)`` on it for each language. After each call,
update the language specific fields and the Language field according to your
data and call persistTranslation again.

### Multilang Document Example

    //import the annotation namespaces
    use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Symfony\Cmf\Bundle\MultilangContentBundle\Annotation as cmfTranslate;

    /**
     * @cmfTranslate\Multilang(strategy="child")
     *
     * or strategy="attribute" to store the title in attributes
     */
    class Document
    {
        /** @@PHPCRODM\Id */
        protected $path;
        /** @@PHPCRODM\Node */
        protected $node;

        /**
         * @validation\NotBlank
         * @cmfTranslate\Language
         */
        public $lang;
        /**
         * @validation\NotBlank
         * @cmfTranslate\Translated
         */
        public $title;
        /**
         * @PHPCRODM\String
         */
        public $url;

        public function getNode()
        {
          return $this->node;
        }
    }

    //controller
    $manager = get_the_odm_document_manager;
    $translator = $this->container->get('symfony_cmf_multilang_content.node_translation');
    $document = new Document(); //your odm document class
    $document->url = 'http://github.com';
    $document->lang = 'en';
    $document->title = 'my title';
    $translator->persistTranslation($document);
    $document->lang = 'de';
    $document->title = 'mein titel';
    $translator->persistTranslation($document);
    $manager->flush();


## Language preferences

The ``symfony_cmf_multilang_content.chooser`` provides a method
getPreferredLanguages that returns an ordered list of languages to choose from.

The default implementation uses a static list that can be configured with:


    symfony_cmf_multilang_content:
        default_lang: %default_lang%
        lang_preference:
            de: [de, fr, en]
            fr: [fr, de, en]
            en: [en, de, fr]

You could provide your own implementation, i.e. providing user preferences for
logged in users.


## Language chooser

You can render a language selection list in your templates using the language
selector controller. The idea is to have links to all language versions of the
current page.

    {% render "symfony_cmf_multilang_content.languageSelectorController:languagesAction"
            with {"url" : url, "languageUrls": languageUrls|default(false) } %}

For cmf pages, you do not need to specify the languageUrls, as the urls are
generated in the selector controller from the default language preferences
order.
But you can specify the languageUrls as parameter to the language chooser
i.e. to use a custom route name.


# TODO

* is the architecture of this bundle sane?
* can we do without the need for ``persistTranslation``?
* how to delete a translation?
* Use a namespace for the child node instead of lang~
* How to handle namespaced properties with the property strategy?
