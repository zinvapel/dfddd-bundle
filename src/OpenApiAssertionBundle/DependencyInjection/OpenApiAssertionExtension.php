<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class OpenApiAssertionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));

        $loader->load('autowire.yaml');
        $loader->load('commands.yaml');
        $loader->load('proto/generate.yaml');
        $loader->load('proto/transform/assert.yaml');
    }
}