<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tests\Unit;

use \PHPCR;
use \Jackalope;

/**
 * Unit test for PHPCRTree class
 * 
 * @author Jacopo Jakuza Romei <jromei@gmail.com>
 * @see \Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree\PHPCRTree
 * 
 */
class PHPCRTreeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->com = $this->getMockBuilder('Jackalope\Node')->
            disableOriginalConstructor()->
            getMock();
        
        $this->session = $this->getMockBuilder('PHPCR\SessionInterface')->
            disableOriginalConstructor()->
            getMock();
        
        $this->session->expects($this->any())->
                method('getNode')->
                with('/com')->
                will($this->returnValue($this->com));
        
        $this->tree = new \Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree\PHPCRTree($this->session);
    }
    
    public function testPHPCRChildren()
    {
        $node_mock_prototype = $this->getMockBuilder('Jackalope\Node')->
            disableOriginalConstructor()->
            setMethods(array('getPath', 'getNodes'));
        
        $anonimarmonisti = $node_mock_prototype->getMock();
        $anonimarmonisti->expects($this->once())->
                method('getPath')->
                will($this->returnValue('/com/anonimarmonisti'));
        $anonimarmonisti->expects($this->once())->
                method('getNodes')->
                will($this->returnValue(true));
        
        $romereview = $node_mock_prototype->getMock();
        $romereview->expects($this->once())->
                method('getPath')->
                will($this->returnValue('/com/romereview'));
        $romereview->expects($this->once())->
                method('getNodes')->
                will($this->returnValue(true));
        
        $_5etto = $node_mock_prototype->getMock();
        $_5etto->expects($this->once())->
                method('getPath')->
                will($this->returnValue('/com/5etto'));
        $_5etto->expects($this->once())->
                method('getNodes')->
                will($this->returnValue(true));
        
        $wordpress = $node_mock_prototype->getMock();
        $wordpress->expects($this->once())->
                method('getPath')->
                will($this->returnValue('/com/wordpress'));
        $wordpress->expects($this->once())->
                method('getNodes')->
                will($this->returnValue(true));
        
        $children = array(
            'anonimarmonisti'   => $anonimarmonisti,
            'romereview'        => $romereview,
            '5etto'             => $_5etto,
            'wordpress'         => $wordpress,
        );
        
        $this->com->expects($this->once())->
                method('getNodes')->
                will($this->returnValue($children));

        $expected = array (
            array (
                'text' => 'anonimarmonisti',
                'id' => '/com/anonimarmonisti',
                'hasChildren' => true,
            ),
            array (
                'text' => 'romereview',
                'id' => '/com/romereview',
                'hasChildren' => true,
            ),
            array (
                'text' => '5etto',
                'id' => '/com/5etto',
                'hasChildren' => true,
            ),
            array (
                'text' => 'wordpress',
                'id' => '/com/wordpress',
                'hasChildren' => true,
            )
        );

        $this->assertEquals($expected, $this->tree->getChildren('/com'));
    }

    public function testPHPCRProperties()
    {
        $date = new \DateTime("2011-08-31 11:02:39");

        $properties = array(
            'jcr:createdBy'     => 'user',
            'jcr:created'       => $date,
            'jcr:primaryType'   => 'nt:folder',
        );
        
        $this->com->expects($this->once())->
                method('getPropertiesValues')->
                will($this->returnValue($properties));

        $expected = array (
            array (
                'name' => 'jcr:createdBy',
                'value' => 'user',
            ),
            array (
                'name' => 'jcr:created',
                'value' =>  $date,
            ),
            array (
                'name' => 'jcr:primaryType',
                'value' => 'nt:folder',
            ),
        );

        $this->assertEquals($expected, $this->tree->getProperties('/com'));
    }
}
