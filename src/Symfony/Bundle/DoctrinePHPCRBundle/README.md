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

# Useful commands for PHPCR

# Additional requirements for the doctrine:phpcr:fixtures:load command

To use the doctrine:phpcr:fixtures:load command, you additionally need the Doctrine
data-fixtures and the symfony doctrine fixtures bundle:
- https://github.com/symfony/DoctrineFixturesBundle
- https://github.com/doctrine/data-fixtures


# Commands

The bundle provides a couple of symfony commands. To execute them, from your
main project folder run

    app/console.php <command> [options] [arguments]

## doctrine:phpcr:dump

    Usage:
    doctrine:phpcr:dump [--sys_nodes[="..."]] [--props[="..."]] [path]

    Arguments:
    path         Path of the node to dump (default: /)

    Options:
     --sys_nodes Set to "yes" to dump the system nodes (default: no)
     --props Set to "yes" to dump the node properties (default: no)

    Help:
    The doctrine:phpcr:dump command dumps a node (specified by the path argument) and its subnodes in a yaml-like style.

     If the props option is set to yes the nodes properties are displayed as yaml arrays.
     By default the command filters out system nodes and properties (i.e. nodes and properties with names starting
     with 'jcr:'), the sys_nodes option allows to turn this filter off.


## doctrine:phpcr:purge

    Usage:
    doctrine:phpcr:purge

    Options:
     --force Set to "yes" to bypass the confirmation dialog (default: no)

    Help:
     The doctrine:phpcr:purge command remove all the non-standard nodes from the content repository


## doctrine:phpcr:sql

    Usage:
    doctrine:phpcr:sql sql

    Arguments:
    sql  JCR SQL2 statement to execute

    Help:
    The phpcr:sql command executes a JCR SQL2 statement on the content repository


## doctrine:phpcr:jackrabbit

    Usage:
     doctrine:phpcr:jackrabbit [--jackrabbit_jar[="..."]] cmd

    Arguments:
     cmd               Command to execute (start | stop | status)

    Options:
     --jackrabbit_jar Path to the Jackrabbit jar file

    Help:
     The doctrine:phpcr:jackrabbit command allows to have a minimal control on the Jackrabbit server
     from within a Symfony 2 command.

     If the jackrabbit_jar option is set, it will be used as the Jackrabbit server jar file.
     Otherwise you will have to set the doctrine_phpcr.jackrabbit_jar config parameter to a
     valid Jackrabbit server jar file.


## doctrine:phpcr:fixtures:load

    Usage:
     doctrine:phpcr:fixtures:load [--path="..."] [--purge[="..."]]

    Options:
     * --path The path to the fixtures
     * --purge Set to true if the database must be purged

    Help:
     The doctrine:phpcr:fixtures:load command loads PHPCR fixtures


# Fixtures

The fixtures classes must implement Doctrine\\Common\\DataFixtures\\FixtureInterface.

Here is an example of fixture:

    namespace MyBundle\Data\Fixtures;

    use Doctrine\Common\DataFixtures\FixtureInterface;

    class LoadMyData implements FixtureInterface
    {
        public function load($manager)
        {
            // Create and persist your data here...
        }
    }


# NodeHelper

NodeHelper implements some utility methods to simplify the interaction with the PHPCR session.

DOC NEEDS TO BE REVIEWED

    /**
     * Return the phpcr session
     */
    public function getSession()

    /**
     * Return the root node
     */
    public function getRoot()

    /**
     * Get a node given its path. Return false if none found.
     */
    public function getNode($path)

    /**
     * Create a new node with the given name as a child of $parent node.
     * If $parent is null then the new node is created at the root.
     */
    public function createNode($name, Node $parent = null)

    /**
     * Create a node and it's parents, if necessary.  Like mkdir -p.
     */
    public function createPath($path)

    /**
     * Delete all the nodes in the repository which are not prefixed with jcr:
     */
    public function deleteAllNodes()

    public function isSystemNode(Node $node)

    public function isSystemProperty(Property $prop)


# TreeWalker

TODO: The navigation bundle also contains a tree walker. This might be redundant.

You can use the Helper\\TreeWalker class to easily traverse the content repository nodes and properties.

First create a TreeWalker specifying a visitor class for the nodes and optionally another for the properties.
The visitor classes must implement PHPCR\\ItemVisitorInterface.

    use PHPCR\ItemVisitorInterface;
    use PHPCR\ItemInterface;
    use PHPCR\NodeInterface;

    class MyNodeVisitor implements ItemVisitorInterface
    {
        public function visit(ItemInterface $item)
        {
            if (! $item instanceof NodeInterface) {
                throw new \Exception("Internal error: did not expect to visit a non-node object: $item");
            }

            // Do something with the node here...
        }
    }

    class MyPropertyVisitor implements ItemVisitorInterface
    {
        public function visit(ItemInterface $item)
        {
            if (! $item instanceof PropertyInterface) {
                throw new \Exception("Internal error: did not expect to visit a non-property object: $item");
            }

            // Do something with the property here...
        }
    }

Then call the traverse method passing the node where the traversal must start.

    $my_node_visitor = new MyNodeVisitor();
    $walker = new TreeWalker($my_node_visitor, $my_property_visitor);
    $walker->traverse($some_node);

You can filter out nodes or properties by setting a node or property filter.

A filter is simply a class implementing TreeWalkerFilterInterface.

    use PHPCR\ItemInterface;

    class MyNodeFilter implements TreeWalkerFilterInterface
    {
        public function mustVisit(ItemInterface $node)
        {
            // Return true if the node must be visited...
        }
    }

    $filter = new MyNodeFilter();
    $walker = new TreeWalker($my_node_visitor);
    $walker->addNodeFilter($filter);
