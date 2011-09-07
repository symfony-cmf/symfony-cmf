# Doctrine PHPCR Bundle

This bundle integrates Doctrine PHPCR ODM and PHPCR backends like Jackalope or
Midgard2 CR into Symfony2.

It is part of the [symfony-cmf](https://github.com/symfony-cmf/symfony-cmf)
repository and additionally exposed standalone repository in case you do not
want the rest of the symfony-cmf bundles.

**Never commit to the standalone repository.** All commits and pull requests go
to the main repository https://github.com/symfony-cmf/symfony-cmf
Maintainers: See docs/StandaloneBundles.md for how to update the standalone
repository.


# Installation

### With symfony-cmf

Please see the main README at https://github.com/symfony-cmf/symfony-cmf

### Standalone

* Grab this repository and [Doctrine PHPCR ODM](http://github.com/doctrine/phpcr-odm) into your Symfony project
* Add `Symfony\Bundle\DoctrinePHPCRBundle\DoctrinePHPCRBundle` to your Kernel's registerBundles() method
* Add autoloader for Doctrine\PHPCR, Doctrine\ODM\PHPCR and Doctrine\Bundle namespaces


## Configuration

The configuration is similar to Doctrine ORM and MongoDB configuration for Symfony2 as its based
on the AbstractDoctrineBundle aswell:

    doctrine_phpcr:
        # configure the PHPCR session
        session:
            backend:
                ## backend type: jackrabbit, doctrinedbal or midgard
                type: jackrabbit

                ## doctrinedbal only, required
                connection: <service name of the doctrine dbal connection>

                ## jackrabbit only, required
                url: http://localhost:8080/server/
                ## jackrabbit only, optional. see https://github.com/jackalope/jackalope/blob/master/src/Jackalope/RepositoryFactoryJackrabbit.php
                default_header: ...
                expect: 'Expect: 100-continue'

                ## tweak options for jackrabbit and doctrinedbal (all jackalope versions)
                # optional, below set to the default
                # enable if you want to have an exception right away if backend login fails
                check_login_on_server: false
                # enable if you experience segmentation faults while working with binary data in documents
                disable_stream_wrapper: false
                # enable if you do not want to use transactions and you neither want the odm to automatically use transactions
                # its highly recommended NOT to disable transactions
                disable_transactions: false
            workspace: default
            username: admin
            password: admin
        # enable the ODM layer
        odm:
            auto_mapping: true

## Services

You can access the PHPCR services like this:

    <?php

    namespace Acme\DemoBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class DefaultController extends Controller
    {
        public function indexAction()
        {
            // PHPCR session instance
            $session = $this->container->get('doctrine_phpcr.default_session');
            // PHPCR ODM document manager instance
            $documentManager = $this->container->get('doctrine_phpcr.odm.default_document_manager');
        }
    }
