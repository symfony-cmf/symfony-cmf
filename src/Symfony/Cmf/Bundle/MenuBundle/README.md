# Symfony CMF Menu Bundle to tie KnpMenuBundle into symfony cmf

This bundle provides menus from a doctrine object manager with the help of
KnpMenuBundle.

See http://cmf.symfony-project.org/ for more information about the CMF.

## Menu entries

Document\MenuItem defines menu entries. You can build menu items based on
routes, absolute or relative urls or referenceable phpcr-odm content documents.

The menu tree is built from documents under [menu_basepath]/[menuname]. To
prevent accidentally exposing nodes, only nodes ending on -item are considered
menu items.
You can use different document classes for menu items, as long as they implement
Knp\Menu\NodeInterface to integrate with KnpMenuBundle.

The currently highlighted entry is determined by checking if the content
associated with a menu document is the same as the content DoctrineRouter
has put into the request.


## Configuration

symfony_cmf_menu:
    menu_basepath: /phpcr/path/to/menutree
    document_manager: doctrine_phpcr.odm.default_document_manager



TODO: document a bit more.

## Dependencies

* SymfonyCmfChainRoutingBundle for the doctrine router service symfony_cmf_chain_routing.doctrine_router
* KnpMenuBundle