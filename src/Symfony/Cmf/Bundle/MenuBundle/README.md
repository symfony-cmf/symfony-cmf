# Symfony CMF Menu Bundle to tie KnpMenuBundle into symfony cmf

This bundle provides menus from a doctrine object manager with the help of
KnpMenuBundle.

See http://cmf.symfony-project.org/ for more information about the CMF.

## Menu entries

Document\MenuItem defines menu entries. You can build menu items based on
symfony routes, absolute or relative urls or referenceable phpcr-odm content
documents.

The menu tree is built from documents under [menu_basepath]/[menuname]. To
prevent accidentally exposing nodes, only nodes ending on -item are considered
menu items.
You can use different document classes for menu items, as long as they implement
Knp\Menu\NodeInterface to integrate with KnpMenuBundle.

The currently highlighted entry is determined by checking if the content
associated with a menu document is the same as the content DoctrineRouter
has put into the request.

Until we have a decent tutorial, you can look into the
[cmf-sandbox](https://github.com/symfony-cmf/cmf-sandbox) and specifically
the [menu fixtures](https://github.com/symfony-cmf/cmf-sandbox/blob/master/src/Sandbox/MainBundle/Resources/data/fixtures/030_LoadMenuData.php)

## Configuration

    symfony_cmf_menu:
        menu_basepath: /phpcr/path/to/menutree
        document_manager: doctrine_phpcr.odm.default_document_manager
        menu_document_class: null
        content_url_generator: symfony_cmf_chain_routing.doctrine_router
        content_key: null (resolves to DoctrineRouter::CONTENT_KEY)
        route_name: null

## How to use non-default other components

If you use the cmf menu with phpcr-odm, you just need to store Route documents
unter ``menu_basepath``. If you use a different object manager, you need to
make sure that the route root document is found with

    $dm->find(route_document_class, menu_basepath . menu_name)

The route document must implement ``Knp\Menu\NodeInterface`` - see
Document/MenuItem.php for an example. You probably need to specify
menu_document_class too, as only phpcr-odm can determine the document from the
database content.

If you use the cmf menu with the DoctrineRouter, you need no route name as the
menu document just needs to provide a field content_key in the options.
If you want to use a different service to generate URLs, you need to make sure
your menu entries provide information in your selected content_key that the url
generator can use to generate the url. Depending on your generator, you might
need to specify a route_name too.
Note that if you just want to generate normal symfony routes with a menu that
is in the database, you can pass the core router service as content_url_generator,
make sure the content_key never matches and make your menu documents provide
the route name and eventual routeParameters.


## Dependencies

* KnpMenuBundle

Unless you change defaults and provide your own implementations, also depends on

* SymfonyCmfChainRoutingBundle for the doctrine router service symfony_cmf_chain_routing.doctrine_router
* Doctrine PHPCR-ODM to load route documents from the content repository