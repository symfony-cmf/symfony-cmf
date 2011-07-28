<?php
namespace Symfony\Cmf\Bundle\NavigationBundle\Tests\Functional\Service;

use Symfony\Cmf\Bundle\CoreBundle\Test\CmfTestCase;

use Symfony\Cmf\Bundle\CoreBundle\Helper\DirectPathMapper;
use Symfony\Cmf\Bundle\NavigationBundle\Service\HierarchyWalker;

/**
 * Test hiearchy walker service
 *
 * @author David Buchmann <david@liip.ch>
 */
class HierarchyWalkerTest extends CmfTestCase
{
    public function __construct()
    {
        parent::__construct(__DIR__.'/../../Fixtures/');
    }

    public function setUp()
    {
        $this->assertJackrabbitRunning();
        $this->loadFixture('simpletree.xml');
    }

    public function testGetChildList()
    {
        $walker = new HierarchyWalker($this->getContainer()->get('doctrine_phpcr.default_session'),
                                      new DirectPathMapper('/cms/navigation/main'));
        $childlist = $walker->getChildList('test/');

        $expected = array('/test/leveltwo'      => 'nav leveltwo',
                          '/test/otherleveltwo' => 'nav otherleveltwo');

        $this->assertEquals($expected, $childlist);
    }

    public function testGetParents()
    {
        $walker = new HierarchyWalker($this->getContainer()->get('doctrine_phpcr.default_session'),
                                      new DirectPathMapper('/cms/navigation/main'));
        $breadcrumb = $walker->getAncestors('test/leveltwo/levelthree');

        $expected = array('/'               => 'Home',
                          '/test'           => 'nav test',
                          '/test/leveltwo'  => 'nav leveltwo');

        $this->assertEquals($expected, $breadcrumb);
    }

    public function testGetMenu()
    {
        //TODO
    }
}

