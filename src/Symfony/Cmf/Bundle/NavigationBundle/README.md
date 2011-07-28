# Navigation Bundle

This is the navigation bundle for the Symfony2 content management framework.
See http://cmf.symfony-project.org/ for more information about the CMF.

This bundle contains a helper service to walk the navigation tree, as well as a
simple controller with default templates to output the navigation elements.


# Features

* Menu tree, sitemaps
* Breadcrumb
* Child list
* Navigation entries to controller or/and to content node mapping
    * Several navigation entries showing the same content
    * Navigation entries redirecting to other navigation items


# Usage

A lot can be replaced in this bundle. The easiest is to use the provided
navigation document and the actions of the navigation renderer controller.

## Rendering navigational pieces

You can use the renderer controller ``symfony_cmf_navigation.renderer``
to output navigation elements on your pages.

    {% render "symfony_cmf_navigation.renderer:breadcrumbAction" with { 'url': url } %}
    {% render "symfony_cmf_navigation.renderer:menuAction" with {"url" : url} %}
    {% render "symfony_cmf_navigation.renderer:childrenAction" with {"url" : url} %}
    {% render "symfony_cmf_navigation.renderer:sitemapAction" with {"url" : url} %}

You can use the ``symfony_cmf_navigation.hierarchyWalker`` directly with your
own visitors to implement new behaviour. Have a look at the provided controller
to see how the components are used.


## Cms route

Include the navigation route in your main routing.yml as the *very last* entry:

    cmf:
        resource: "@SymfonyCmfNavigationBundle/Resources/config/routing.yml"

This route is a catch all route that matches on all requests and lets the
``symfony_cmf_navigation.controller`` handle them.
The default controller tries to find a navigation entry in the repository and
throws a not found exception if there is none. if there is a navigation entry,
the controller checks whether it should redirect, and otherwise tries to load
the associated content document and then calls the mapped content controller.

Alternativly, you can roll your own route and configure the parameter
``symfony_cmf_navigation.mainmenu_routename`` to the name of your route.


## Creating navigation entries programmatically

This bundle brings a default Navigation document class in
``Document/Navigation.php``. Navigation entries can reference a content
document and/or specify a controller to be used to render them.

    // have some content for this navigation entry (optional)
    $content = new StaticPage();
    /*
     * this is a workaround because we need the phpcr nod
     * once the reference annotation of phpcr-odm is implemented, this will be
     * refactored
     */
    $document_manager->persist($content);
    $document_manager->flushNoSave();

    $nav = new Navigation();
    $nav->setPath('/cms/navigation/main/entry');
    $nav->setLabel('Entry');
    $nav->setInfo('Some additional info about this entry for tooltips and sitemap');
    $nav->setReference($content->getNode());
    /*
     * set the controller alias if you have no mapping for the content or if
     * this page should be rendered by a special controller
     */
    $nav->setController('static_pages');
    $document_manager->persist($nav);
    $document_manager->flush();

If you use anything else than the standard content of ContentBundle, you have
to configure mappings in ``symfony_cmf_navigation.controllers_by_content`` and
``...controllers_by_alias``

    symfony_cmf_navigation:
        controllers_by_alias:
            static_pages: AcmeCoreContent:indexAction
            my_feature: AcmeFeatureDefault:indexAction
        controllers_by_content:
            Acme\Core\Document\MyDoc: AcmeCoreContent:mydocAction

Content controllers must expect exactly two parameters:
* $page is the referenced document (or null if no referenced document)
* $url is the url path to this navigation entry as used with the navigation route.

AcmeCoreContent is resolved as if you would use it in twig. static_pages will
request ``Acme\CoreBundle\Controller\ContentController`` and call
``indexAction($page, $url)`` on it.
Without an explicit controller alias, navigation entries referencing a MyDoc
content will execute the mydocAction on the acme core content controller.

You can define your own Navigation document class and overwrite the parameter
``symfony_cmf_navigation.document`` in your app config.yml. Note that if you
intend to use it with the provided hierarchywalker, the document needs to have
the same properties and getters as the default one.


# TODO

* config options for the configurable stuff
* tests
* support http links as navigation items
* generic content controller showing fields based on annotations
