<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Helper;

/**
* Helper to map from nt:file nodes to an url suitable for an
* <img src="url" /> or <a href="url></a> tag.
*
* A helper could provide a route to handle requests for files. The default
* implementation makes sure that the file exists someplace underneath the
* web folder and then returns the path from web root. This is straightforward
* and allows for integration with file based image bundles like
* AvalancheImagineBundle.
*/
interface FileMapperInterface
{
    /**
    * @param $imgnode the node containing the jcr:content node with a jcr:data property with file data
    *
    * @return string a url suitable for inclusion in your img references or anchor href links
    */
    function getUrl(\PHPCR\NodeInterface $dataNode);
}
