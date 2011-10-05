<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Tests\Helper;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Symfony\Bundle\DoctrinePHPCRBundle\Helper\JackrabbitHelper;

/**
 * Test jackrabbit helper
 *
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class JackrabbitHelperTest extends WebTestCase
{
    protected $helper;
    protected $default_jackrabbit_jar;

    public function setUp()
    {
        if (! $this->getContainer()->hasParameter('doctrine_phpcr.jackrabbit_jar')) {
            $this->markTestSkipped('Default Jackrabbit jar file not set');
        }

        $this->default_jackrabbit_jar = $this->getContainer()->getParameter('doctrine_phpcr.jackrabbit_jar');

        if (!file_exists($this->default_jackrabbit_jar)) {
            $this->markTestSkipped('Default Jackrabbit jar file not found');
        }

        $this->helper = new JackrabbitHelper($this->default_jackrabbit_jar);
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals($this->default_jackrabbit_jar, 'jackrabbit_jar', $this->helper);
        $this->assertAttributeEquals(dirname($this->default_jackrabbit_jar), 'workspace_dir', $this->helper);
    }

    public function testStartStop()
    {
        $this->assertFalse($this->helper->isServerRunning());
        $this->assertEquals('', $this->helper->getServerPid());

        $this->helper->startServer();
        $this->assertTrue($this->helper->isServerRunning());
        $this->assertTrue(is_numeric($this->helper->getServerPid()));

        $this->helper->stopServer();
        $this->assertFalse($this->helper->isServerRunning());
        $this->assertEquals('', $this->helper->getServerPid());
    }
}
