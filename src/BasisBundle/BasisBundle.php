<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zinvapel\Basis\BasisBundle\DependencyInjection\CompilerPass\BuildRoutesCompilerPass;

final class BasisBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new BuildRoutesCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);//, -1000);
    }

}