# Helper things to work with multilanguage

This is the multilanguage bundle for the Symfony2 content management framework.
See http://cmf.symfony-project.org/ for more information about the CMF.

This bundle provides translated versions of the documents in the other cmf
bundles and a helper service to render a language chooser.

# Features

* Multilanguage version of the menu item document and content (TODO). (For routes, you
    create separate instances to place in the different locations in the tree)
* A service to render a language chooser to switch between the languages of the
    current page, based on the doctrine router.

# Usage

## Multilanguage documents

Just use them instead of the default documents when creating content. Loading
content detects multilanguage automatically.
Read more in the documentation of the [phpcr-odm](https://github.com/doctrine/phpcr-odm)

## Language chooser

You can render a language selection list in your templates using the language
selector controller. The idea is to have links to all language versions of the
current page.

    {% render "symfony_cmf_multilang_content.languageSelectorController:languagesAction"
            with {"id" : page.path, "languageUrls": languageUrls|default(false) } %}

For cmf pages, it is enough to specify the id (aka repository path) for the
content document. You do not need to specify the languageUrls, as the urls are
generated in the selector controller from the default language preferences
order.
But you can specify the languageUrls as parameter to the language chooser
i.e. to use a custom route name.


# History

This bundle contained a user space implementation of multilanguage annotations.
Those where [ported to become part of phpcr-odm](https://github.com/doctrine/phpcr-odm/pull/81).