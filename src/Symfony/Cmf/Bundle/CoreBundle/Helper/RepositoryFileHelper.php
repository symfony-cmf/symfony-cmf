<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Helper;

use Symfony\Bundle\DoctrinePHPCRBundle\JackalopeLoader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use PHPCR\NodeInterface;
use Symfony\Cmf\Bundle\CoreBundle\Helper\DirectPathMapper;
use Symfony\Cmf\Bundle\CoreBundle\Helper\ExtensionGuesser;
#use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

/**
 * Helper class to get paths for and save files to filesystem from repository
 */
class RepositoryFileHelper implements FileMapperInterface
{
    /**
     * @var JackalopeLoader
     */
    protected $session;

    /**
     * @var string  (e.g. /var/www/foo/images)  : absolute path, no trailing slash
     */
    protected $fileBasePath;

    /**
     * @var string  (e.g. images_source)  : path relative to the web directory, no trailing slash
     */
    protected $webRelativePath;

    /**
     * @var Symfony\Cmf\Bundle\CoreBundle\Helper\DirectPathMapper
     */
    protected $pathMapper;


    /**
     * @param JackalopeLoader $loader
     * @param string $pathPrefix Content repository path prefix (e.g. /cms/content)
     * @param string $fileBasePath
     * @param string $webRelativePath a path relative to the web directory
     */
    public function __construct(JackalopeLoader $loader, $pathPrefix, $fileBasePath, $webRelativePath)
    {
        $this->session = $loader->getSession();
        $this->fileBasePath = $fileBasePath;
        $this->pathMapper = new DirectPathMapper($pathPrefix);
        $this->webRelativePath = '/' . $webRelativePath;
    }

    /**
     * Gets a relative filesystem path based on the repository path, AND
     * creates the file on the filesystem if it's in the repository
     * and not yet on the filesystem.
     * The repository path points to a nt-resource node, whose title
     * should be the filename, and which has a child+property
     * jcr:content/jcr:data where the file data is stored.
     *
     * @param string $path path to the nt-resource node.
     * @return string with a path to the file, relative to the web directory.
     */
    public function getUrl(NodeInterface $node)
    {

        $hasData = false;
        if ($node->hasNode('jcr:content')) {
            $contentNode = $node->getNode('jcr:content');
            if ($contentNode->hasProperty('jcr:data')) {
                $hasData = true;
            }
        }
        if (!$hasData) {
            //TODO: notfound exception is not appropriate ... how to best do this?
            //throw new NotFoundHttpException('no picture found at ' . $node->getPath());
            return 'notfound';
        }

        $path = $node->getPath();
        $relativePath = $this->pathMapper->getUrl($path);
        $extension = $this->getExtension($contentNode);
        $fullPath = $this->fileBasePath . '/' . $relativePath . $extension;

        if (!file_exists($fullPath)) {
            if (!$this->saveData($contentNode, $fullPath)) {
                throw new FileException('failed to save data to file: ' . $fullPath);
            }
        }

        return $this->webRelativePath . '/' . $relativePath . $extension;
    }

    /**
     *
     *
     * @param NodeInterface $contentNode
     * @param string $filesystemPath
     * @return Boolean
     */
    protected function saveData($contentNode, $filesystemPath)
    {
        $data = $contentNode->getProperty('jcr:data')->getString();
        $dirname = dirname($filesystemPath);
        if (! file_exists($dirname)) {
            mkdir($dirname, 0755, true);
        }

        return file_put_contents($filesystemPath, $data);
    }

    /* TODO: should this be provided as a service? If so, then probably the parent node of the content node should be passed in,
     *       to allow flexibility in implementation.
     *       Possible extension scenarios:
     *          * get extension from the content node's parent's path name
     *          * get extension from some property in the content node
     */
    protected function getExtension($contentNode)
    {
        if ($contentNode->hasProperty('jcr:mimeType')) {
            $mimeType = $contentNode->getPropertyValue('jcr:mimeType');
            $extension = ExtensionGuesser::guess($mimeType);
            #$extension = ExtensionGuesser::getInstance()->guess($mimeType);
            if (null !== $extension) {
                return '.' . $extension;
            }
        }

        return '';
    }
}
