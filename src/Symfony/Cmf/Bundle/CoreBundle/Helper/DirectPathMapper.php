<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Helper;

/**
 * The direct mapper just exposes the paths within phpcr, minus a base path
 * that is used to organize the tree in phpcr.
 *
 * This class makes sure that there is exactly one slash between basepath and path inside cms.
 *
 * @author David Buchmann <david@liip.ch>
 */
class DirectPathMapper implements PathMapperInterface
{
    /**
     * @var string
     * path to the root of the navigation tree this mapper has to map
     */
    protected $basepath;

    /**
     * @var int
     * length of base path, to cut phpcr path to url
     */
    protected $basepathlen;

    /**
     * @param string $basepath phpcr path to the root of the navigation tree, without trailing slash
     */
    public function __construct($basepath)
    {
        $this->basepathlen = strlen($basepath);
        if ($basepath[$this->basepathlen-1] == '/') {
            //ensure trailing slash
            $basepath = substr($basepath, 0, -1);
            $this->basepathlen--;
        }
        $this->basepath = $basepath;
    }

    /**
     * map the web url to the id used to retrieve content from storage
     *
     * @param string $url the request path starting with / (but without the prefix that might be used to get into this menu context)
     * @return mixed storage identifier = absolute node path within phpcr
     */
    public function getStorageId($url)
    {
        if (strlen($url) == 0 || $url == '/') {
            return $this->basepath; //avoid trailing slash duplication for root node
        }
        if ($url[0] != '/') {
            $url = "/$url";
        }
        return $this->basepath . $url;
    }

    /**
     * map the storage id to a web url
     * i.e. translate path to node in phpcr into url for that page
     *
     * @param mixed $storageId id of the storage backend = absolute path to node in phpcr. if this is not a path below the basepath, the result will be wrong.
     * @return string $url the url starting with / (but without the prefix that might be used to get into this menu context)
     */
    public function getUrl($storageId)
    {
        $path = substr($storageId, $this->basepathlen);
        return ltrim($path, '/');
    }
}
