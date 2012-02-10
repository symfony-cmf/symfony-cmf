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

        $grandson = $node_mock_prototype->getMock();
        $grandson->expects($this->any())->
                method('getPath')->
                will($this->returnValue('/com/anonimarmonisti/grandson'));
        $grandson->expects($this->any())->
                method('getNodes')->
                will($this->returnValue(array()));
        
        $grandchildren = array(
            'grandson'   => $grandson,
        );
        
        $anonimarmonisti = $node_mock_prototype->getMock();
        $anonimarmonisti->expects($this->any())->
                method('getPath')->
                will($this->returnValue('/com/anonimarmonisti'));
        $anonimarmonisti->expects($this->any())->
                method('getNodes')->
                will($this->returnValue($grandchildren));
        
        $romereview = $node_mock_prototype->getMock();
        $romereview->expects($this->any())->
                method('getPath')->
                will($this->returnValue('/com/romereview'));
        $romereview->expects($this->any())->
                method('getNodes')->
                will($this->returnValue(array()));
        
        $_5etto = $node_mock_prototype->getMock();
        $_5etto->expects($this->any())->
                method('getPath')->
                will($this->returnValue('/com/5etto'));
        $_5etto->expects($this->any())->
                method('getNodes')->
                will($this->returnValue(array()));
        
        $wordpress = $node_mock_prototype->getMock();
        $wordpress->expects($this->any())->
                method('getPath')->
                will($this->returnValue('/com/wordpress'));
        $wordpress->expects($this->any())->
                method('getNodes')->
                will($this->returnValue(array()));
        
        $children = array(
            'anonimarmonisti'   => $anonimarmonisti,
            'romereview'        => $romereview,
            '5etto'             => $_5etto,
            'wordpress'         => $wordpress,
        );
        
        $this->com->expects($this->exactly(1))->
                method('getNodes')->
                will($this->returnValue($children));

        $expected = array (
            array (
                'data'      => 'anonimarmonisti',
                'attr'      => array(
                                'id' =>     '/com/anonimarmonisti',
                                'rel' =>    'folder',
                            ),
                'state'     =>  'closed',
                'children'  => array(
                    array(
                        'data'      => 'grandson',
                        'attr'      => array(
                                        'id' =>     '/com/anonimarmonisti/grandson',
                                        'rel' =>    'default',
                                    ),
                        'state' =>  null,
                    ),
                ),
            ),
            array (
                'data' => 'romereview',
                'attr' => array(
                    'id' =>     '/com/romereview',
                    'rel' =>    'default',
                ),
                'state' => null,
            ),
            array (
                'data' => '5etto',
                'attr' => array(
                    'id' =>     '/com/5etto',
                    'rel' =>    'default',
                ),
                'state' => null,
            ),
            array (
                'data' => 'wordpress',
                'attr' => array(
                    'id' =>     '/com/wordpress',
                    'rel' =>    'default',
                ),
                'state' => null,
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
        
        $this->com->expects($this->any())->
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
    
    public function testMoveNodes()
    {
        $workspace = $this->getMockBuilder('Jackalope\Workspace')->
            disableOriginalConstructor()->
            setMethods(array('move'))->
            getMock();
        
        $this->session->expects($this->once())->
            method('getWorkspace')->
            with(array('/mother/litigated_son', '/father/litigated_son'));
        
        $this->tree->move('/mother/litigated_son', '/father');
    }

}
