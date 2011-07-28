# Core bundle with basic things for the CMF

This is the core bundle for the Symfony2 content management framework.
See http://cmf.symfony-project.org/ for more information about the CMF.

This bundle provides common functionality and utilities for the other cmf
bundles.

DISCLAIMER: This bundle is still in an experimental state.

### Installation
This bundle depends on jackrabbit_importexport for fixtures loading. You need to add this
to the autoload mechanism. Add in app/autoload.php:

```php
$phpcr_loader = new MapFileClassLoader(
  __DIR__.'/../vendor/doctrine-phpcr-odm/lib/vendor/jackalope/api-test/suite/inc/autoload.php'
);
$phpcr_loader->register();
```

### Testing

To create fixtures for the functional test, you can use the
jackrabbit-importexport tool provided by the jackalope api-tests.

1. Create your content using the editing functions you have built.
2. Check that the structure is correct.
3. Locate jack.jar inside doctrine-phpcr and tell it to dump a part of the tree
    with the exportdocument command (this can take a while). The command will
    look like:
java -jar jack.jar exportdocument dump.xml url=http://localhost:8080 user=admin pass=admin workspace=foo transport=davex repository-base-xpath=/yournode
4. Look into that file and adjust as needed
5. Place the fixtures file near your test and use CmfTestCase::loadFixture to
    load the file into the repository. This will overwrite everything you have
    in that repository.


### TODO
* Clean up bundle parameters (what belongs here, what in NavigationBundle?) and allow configuration
* more documentation

