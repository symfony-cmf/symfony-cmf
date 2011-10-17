<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Tests\Unit\Helper;

use Symfony\Cmf\Bundle\CoreBundle\Helper\DirectPathMapper;

class HierarchyWalkerTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;

    public function setUp()
    {
        $this->mapper = new DirectPathMapper('/path/to/base');
    }

    public function testGetStorage()
    {
        $id = $this->mapper->getStorageId('/my/url');
        $this->assertEquals('/path/to/base/my/url', $id);
    }

    public function testGetStorageRoot()
    {
        $id = $this->mapper->getStorageId('/');
        $this->assertEquals('/path/to/base', $id);
    }

    public function testGetUrl()
    {
        $url = $this->mapper->getUrl('/path/to/base/my/url');
        $this->assertEquals('/my/url', $url);
    }

    public function testGetUrlRoot()
    {
        $url = $this->mapper->getUrl('/path/to/base');
        $this->assertEquals('/', $url);
    }
}
