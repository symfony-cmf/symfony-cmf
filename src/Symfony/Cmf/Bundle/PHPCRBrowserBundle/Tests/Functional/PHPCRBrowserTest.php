<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * Functional test for PHPCRBrowser
 *
 * @todo This test is based on default sandbox fixtures. Though it is a read-only test it would be good to make it standalone with its own fixtures.
 * @todo This test relies on an overall routing that is not enforced at single test level.
 * 
 * @author Jacopo Jakuza Romei <jromei@gmail.com>
 */
class PHPCRBrowserTest extends BaseWebTestCase
{
    
    public function testGetChildrenListFromRoot()
    {
        $client = $this->createClient();

        // TODO This is fragile, depending on project overall routing configuration
        $crawler = $client->request('GET', '/children');
        
        $this->assertEquals(
            $crawler->text(), 
            '[{"text":"jcr:system","id":"\/jcr:system","hasChildren":true},{"text":"cms","id":"\/cms","hasChildren":true},{"text":"menus","id":"\/menus","hasChildren":true}]'
        );
    }

    public function testGetChildrenListFromInnerNode()
    {
        $client = $this->createClient();

        // TODO This is fragile, depending on project overall routing configuration
        $crawler = $client->request('GET', '/children?root=%2Fcms%2Fcontent');
        
        $this->assertEquals(
            $crawler->text(), 
            '[{"text":"static","id":"\/cms\/content\/static","hasChildren":true}]'
        );
    }
    
    public function testGetNodeProperties()
    {
        $client = $this->createClient();

        // TODO This is fragile, depending on project overall routing configuration
        $crawler = $client->request('GET', '/properties?root=%2Fcms%2Fcontent%2Fstatic%2Fhome');
        
        $this->assertStringStartsWith(
            '[{"name":"title","value":"Homepage"},{"name":"name","value":"home"}',
            $crawler->text()
        );
    }
    
    
}