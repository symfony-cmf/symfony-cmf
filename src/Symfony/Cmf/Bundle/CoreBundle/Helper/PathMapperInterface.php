<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Helper;

/**
 * Translate between urls and node ids.
 *
 * The path mapper knows how to translate cmf paths into node ids to be used
 * with storage, and how to get the url for a storage id.
 *
 * URLs always start with a / even though they might be prefixed in the routing,
 * to separate different menues, or language versions, ...
 *
 * Please not that the mapper may use generic rules to translate between the two
 * and does not necessarily validate if the element identified by the url really
 * exists.
 *
 * @author David Buchmann <david@liip.ch>
 */
interface PathMapperInterface
{
    /**
     * map the web url to the id used to retrieve content from storage
     *
     * @param string $url the request url (without the prefix that might be used to get into this menu context)
     * @return mixed storage identifier (i.e. absolute node path within phpcr)
     */
    public function getStorageId($url);

    /**
     * map the storage id to a web url
     * i.e. translate path to node in phpcr into url for that page
     *
     * @param mixed $storageId id of the storage backend (i.e. path to node in phpcr)
     * @param string $url the url (without the prefix that might be used to get into this menu context)
     */
    public function getUrl($storageId);
}
