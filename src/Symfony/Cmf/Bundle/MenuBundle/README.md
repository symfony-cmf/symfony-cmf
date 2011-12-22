# Symfony CMF Menu Bundle to tie KnpMenuBundle into symfony cmf

This bundle provides menus from phpcr-odm with the help of KnpMenuBundle.

See http://cmf.symfony-project.org/ for more information about the CMF.

## Menu entries

Document\MenuItem defines menu entries. You can build menu items based on
routes, absolute or relative urls or referenceable phpcr-odm content documents.

The menu tree is built from documents under [menu_basepath]/[menuname].

The currently highlighted entry is determined by checking if the content
associated with a menu document is the same as the content DoctrineRouter
has put into the request.


## Configuration

symfony_cmf_menu:
    menu_basepath: /phpcr/path/to/menutree
    document_manager: service.name.of.document.manager.to.use


TODO: document a bit more.

## Dependencies

* SymfonyCmfChainRoutingBundle
* KnpMenuBundle