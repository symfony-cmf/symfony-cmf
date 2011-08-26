<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * Base class for functional tests in the cmf that base on PHPCR
 *
 * Works only for Jackalope with phpcr atm. Should be refactored
 * to use Session->import once this is implemented
 *
 * Connection information is read from jackalope.options... parameters
 *
 * Fixtures are loaded from PHPCR XML dumps.
 * To acquire some fixtures, create the necessary content in your repository,
 * then locate jack.jar inside phpcr-odm to dump the repository. You should
 * specify the base xpath to the name of the root node of your content.
 * Without that, you will dump all kinds of basic definitions and get a file
 * of 40+ M.
 *
 * java -jar /path/to/jack.jar export dump.xml repository-base-xpath=/cms
 *
 * Jack dumps the file on one single line. You can format that for better
 * reading, i.e. xmllint --format dump.xml > dump-formatted.xml
 *
 * The document view is more readable than the system view. However, types
 * are not stored and you can get problems, i.e. with node references.
 *
 * java -jar /path/to/jack.jar exportdocument dump.xml repository-base-xpath=/cms
 *
 * @author David Buchmann <david@liip.ch>
 */
class CmfTestCase extends BaseWebTestCase
{
    protected $importexport;

    /**
     * setup this test
     * for fixtures path, you typically use something like __DIR__/../Fixtures/
     *
     * @param string $fixturesPath the path to the directory with the fixture xmls for testing.
     */
    public function __construct($fixturesPath)
    {
        parent::__construct();

        $this->importexport = new \JackrabbitFixtureLoader($fixturesPath);
    }

    /**
     * load a fixture xml file (system view or document view) into the
     * repository, overwriting its current content.
     *
     * the file is build from the fixturesPath, this name and the extension .xml
     *
     * @param string $name the name of the fixture to load
     */
    public function loadFixture($name)
    {
        //TODO: improve importexport to have an other way to pass options
        $container = $this->getContainer();
        $GLOBALS['jackrabbit.uri'] = $container->getParameter('jackrabbit_url');
        $GLOBALS['phpcr.workspace'] = $container->getParameter('phpcr_workspace');
        $GLOBALS['phpcr.user'] = $container->getParameter('phpcr_user');
        $GLOBALS['phpcr.pass'] = $container->getParameter('phpcr_pass');

        $this->importexport->import($name);
    }

    /**
     * check if jackrabbit is running at the address configured for doctrine-phpcr
     *
     * if not, marks the test as skipped
     * if there is a reply but one that does not look like jackrabbit, test fails
     */
    public function assertJackrabbitRunning()
    {
        static $available;
        static $url;
        if (null === $available) {
            $container = $this->getContainer();
            $url = $container->getParameter('jackrabbit_url');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($ch);

            curl_close($ch);

            $available = (Boolean) $res;
            if ($available) {
                $this->assertContains('Available Workspace Resources', $res, "This seems to be not jackrabbit but something else at $url");
            }
        }

        if (! $available) {
            $this->markTestSkipped("Jackrabbit is not listening at $url");
        }

    }
}
