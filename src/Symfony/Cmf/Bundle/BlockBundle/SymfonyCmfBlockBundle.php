<?php

namespace Symfony\Cmf\Bundle\BlockBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Cmf\Bundle\BlockBundle\DependencyInjection\Compiler\BlockCompilerPass;

class SymfonyCmfBlockBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockCompilerPass());
    }
}
