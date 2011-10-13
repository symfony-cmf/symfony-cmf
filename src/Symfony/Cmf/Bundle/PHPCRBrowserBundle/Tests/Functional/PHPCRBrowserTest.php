<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * Functional test for PHPCRBrowser
 *
 * @author Jacopo Jakuza Romei <jromei@gmail.com>
 */
class PHPCRBrowserTest extends BaseWebTestCase
{
    
    public function testGetChildrenList()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/phpcrbrowser');
        
        $this->assertEquals($crawler->filter('#tree')->count(), 1);
    }

    
}

