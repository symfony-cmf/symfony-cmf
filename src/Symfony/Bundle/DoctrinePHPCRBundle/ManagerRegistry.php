<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle;

use Symfony\Bridge\Doctrine\ManagerRegistry as BaseManagerRegistry;
use Doctrine\ODM\PHPCR\PHPCRException;

class ManagerRegistry extends BaseManagerRegistry
{
    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * @param string $alias
     * @return string
     * @throws PHPCRException
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getDocumentNamespace($alias);
            } catch (PHPCRException $e) {
            }
        }

        throw PHPCRException::unknownDocumentNamespace($alias);
    }
}
