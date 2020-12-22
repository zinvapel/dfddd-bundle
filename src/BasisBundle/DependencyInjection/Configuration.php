<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('basis');

        $treeBuilder->getRootNode()
            ->fixXmlConfig('route')
            ->children()
                ->arrayNode('routes')
                    ->useAttributeAsKey('name')
                    ->fixXmlConfig('response', 'responses')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('context')
                                ->isRequired()
                                ->children()
                                    ->enumNode('factory_type')
                                        ->values(['denormalize', 'custom'])
                                        ->isRequired()
                                    ->end()
                                    ->enumNode('data_extractor')
                                        ->values(['route', 'empty', 'json', 'post_json', 'get'])
                                        ->defaultValue('empty')
                                    ->end()
                                    ->scalarNode('dto_class')->end()
                                    ->scalarNode('constraints_provider')->end()
                                    ->scalarNode('custom')->end()
                                ->end()
                                ->validate()
                                    ->ifTrue(static function ($conf) {
                                        return isset($conf['factory_type']);
                                    })
                                    ->then(static function ($conf) {
                                        if ($conf['factory_type'] === 'denormalize') {
                                            if (!isset($conf['dto_class'])) {
                                                throw new \RuntimeException('Dto class does not provided');
                                            }
                                            if (!isset($conf['constraints_provider'])) {
                                                throw new \RuntimeException('Constraints does not provided');
                                            }
                                        }

                                        if ($conf['factory_type'] === 'custom') {
                                            if (!isset($conf['custom'])) {
                                                throw new \RuntimeException('Custom context factory does not provided');
                                            }
                                        }

                                        return $conf;
                                    })
                                ->end()
                            ->end()
                            ->arrayNode('service')
                                ->fixXmlConfig('decorator')
                                ->isRequired()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->arrayNode('decorators')
                                        ->defaultValue([])
                                        ->arrayPrototype()
                                            ->children()
                                                ->enumNode('type')
                                                    ->values(['transactional', 'event_dispatch'])
                                                    ->isRequired()
                                                ->end()
                                                ->integerNode('priority')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('responses')
                                ->isRequired()
                                ->useAttributeAsKey('class')
                                ->arrayPrototype()
                                    ->children()
                                        ->enumNode('factory_type')
                                            ->values(['no_content', 'json', 'custom'])
                                            ->isRequired()
                                        ->end()
                                        ->integerNode('status_code')->end()
                                        ->scalarNode('custom')->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(fn () => true)
                                        ->then(static function ($conf) {
                                            if ($conf['factory_type'] === 'custom' && !isset($conf['custom'])) {
                                                throw new \RuntimeException('Custom response factory does not provided');
                                            }

                                            if ($conf['factory_type'] !== 'no_content' && !isset($conf['status_code'])) {
                                                throw new \RuntimeException("'status_code' does not provided");
                                            }

                                            return $conf;
                                        })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}