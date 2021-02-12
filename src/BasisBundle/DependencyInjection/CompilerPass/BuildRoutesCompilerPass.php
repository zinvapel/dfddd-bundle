<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Zinvapel\Basis\BasisBundle\Http\Flow\Controller;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\ExceptionDto;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\FailedDto;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\InvalidDto;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\NotFoundDto;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\ForbiddenDto;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\EmptyData;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\JsonBody;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\RouteParameters;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Symfony\Get;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Symfony\PostJson;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DenormalizeContextFactory;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\HeaderProvider\StaticKeyValue;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\MappableWithFallback;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\NoContent;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\Predefined;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\Serialize;
use Zinvapel\Basis\BasisBundle\Regular\ServiceDecorator\EventDispatch;
use Zinvapel\Basis\BasisBundle\Regular\ServiceDecorator\Transactional;

final class BuildRoutesCompilerPass implements CompilerPassInterface
{
    private const DATA_EXTRACTOR_MAP = [
        'route' => RouteParameters::class,
        'empty' => EmptyData::class,
        'json' => JsonBody::class,
        'post_json' => PostJson::class,
        'get' => Get::class,
    ];
    
    private const DECORATOR_MAP = [
        'transactional' => [
            'class' => Transactional::class,
            'args' => ['doctrine.orm.entity_manager']
        ],
        'strict_transactional' => [
            'class' => Transactional::class,
            'args' => ['doctrine.orm.entity_manager'],
            'default_args' => [false],
        ],
        'event_dispatch' => [
            'class' => EventDispatch::class,
            'args' => ['event_dispatcher']
        ],
    ];

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getParameter('basis')['routes'] ?? [] as $name => $config) {
            $this->buildRoute($container, $name, $config);
        }
    }

    public function buildRoute(ContainerBuilder $container, string $name, array $config): void
    {
        $definition =
            new Definition(
                Controller::class,
                [
                    $this->buildContextFactory($container, $config['context']),
                    $this->buildService($container, $config['service']),
                    $this->buildResponseTransformer($container, $config['responses'])
                ]
            );
        $definition->setPublic(true);

        $container->setDefinition('basis.route.'.$name.'.controller', $definition);
    }

    private function buildContextFactory(ContainerBuilder $container, $context): Definition
    {
        if (!class_exists($context['dto_class'])) {
            throw new InvalidArgumentException(sprintf("Class does not exist '%s'", $context['dto_class']));
        }

        $definition = new Definition();

        switch ($context['factory_type']) {
            case 'denormalize':
                if ($container->hasDefinition($context['constraints_provider'])) {
                    $constraintsProvider = $container->getDefinition($context['constraints_provider']);
                } else {
                    if (is_callable($context['constraints_provider'])) {
                        $constraintsProvider = $context['constraints_provider'];
                    } elseif (method_exists($context['constraints_provider'], '__invoke')) {
                        $constraintsProvider = new $context['constraints_provider'];
                    } else {
                        [$class, $method] = explode('::', $context['constraints_provider']);

                        if ($container->has($class)) {
                            $constraintsProvider = [$container->getDefinition($class), $method];
                        } else {
                            $constraintsProvider = [$class, $method];
                        }
                    }
                }

                if (!is_callable($constraintsProvider)) {
                    throw new InvalidArgumentException("Context 'constraints_provider' should be a callable");
                }

                return
                    $definition
                        ->setClass(DenormalizeContextFactory::class)
                        ->setArgument(
                            '$dataExtractor',
                            new Reference(self::DATA_EXTRACTOR_MAP[$context['data_extractor'] ?? 'empty'])
                        )
                        ->setArgument('$validator', $container->getDefinition('validator'))
                        ->setArgument('$constraintsProvider', $constraintsProvider)
                        ->setArgument('$denormalizer', $container->getDefinition('serializer'))
                        ->setArgument('$class', $context['dto_class'])
                    ;
            default:
                if ($container->hasDefinition($context['custom'])) {
                    return $container->getDefinition($context['custom']);
                }

                throw new InvalidArgumentException("Context 'custom' does not exist");
        }
    }
    
    private function buildService(ContainerBuilder $container, array $config): Reference
    {
        $definition = $container->getDefinition($config['name']);

        foreach ($config['decorators'] ?? [] as $decorator) {
            $decoratorDefinition =
                new Definition(
                    self::DECORATOR_MAP[$decorator['type']]['class'],
                    array_merge(
                        array_map(
                            fn (string $arg) => new Reference($arg),
                            self::DECORATOR_MAP[$decorator['type']]['args']
                        ),
                        [
                            new Reference($config['name'].'.decorator.'.$decorator['type'].'.inner'),
                        ],
                        self::DECORATOR_MAP[$decorator['type']]['default_args'] ?? []
                    )
                );
            $decoratorDefinition->setDecoratedService($config['name'], null, $config['priority'] ?? 0);
            $container->setDefinition($config['name'].'.decorator.'.$decorator['type'], $decoratorDefinition);
        }

        return new Reference($config['name']);
    }

    private function buildResponseTransformer(ContainerBuilder $container, array $responses): Definition
    {
        $jsonHeader = new Definition(StaticKeyValue::class, ['content-type', 'application/json']);
        $context = [AbstractObjectNormalizer::SKIP_NULL_VALUES => true, AbstractObjectNormalizer::GROUPS => ['body']];
        $definition = new Definition(MappableWithFallback::class);
        $map = [
            NotFoundDto::class =>
                new Definition(
                    Serialize::class,
                    [
                        $container->getDefinition('serializer'),
                        $jsonHeader,
                        'json',
                        Response::HTTP_NOT_FOUND,
                        $context
                    ]
                ),
            ForbiddenDto::class =>
                new Definition(
                    Serialize::class,
                    [
                        $container->getDefinition('serializer'),
                        $jsonHeader,
                        'json',
                        Response::HTTP_FORBIDDEN,
                        $context
                    ]
                ),
            InvalidDto::class =>
                new Definition(
                    Serialize::class,
                    [
                        $container->getDefinition('serializer'),
                        $jsonHeader,
                        'json',
                        Response::HTTP_BAD_REQUEST,
                        $context
                    ]
                ),
            FailedDto::class =>
                new Definition(
                    Serialize::class,
                    [
                        $container->getDefinition('serializer'),
                        $jsonHeader,
                        'json',
                        Response::HTTP_BAD_REQUEST,
                        $context
                    ]
                ),
            ExceptionDto::class =>
                new Definition(
                    Serialize::class,
                    [
                        $container->getDefinition('serializer'),
                        $jsonHeader,
                        'json',
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        $context
                    ]
                ),
        ];

        foreach ($responses as $class => $response) {
            $classDefinition = null;
            switch ($response['factory_type']) {
                case 'no_content':
                    $map[$class] = new Definition(NoContent::class);
                    break;
                case 'custom':
                    $map[$class] = $container->getDefinition($response['custom']);
                    break;
                default:
                    $map[$class] =
                        new Definition(
                            Serialize::class,
                            [
                                $container->getDefinition('serializer'),
                                $jsonHeader,
                                'json',
                                $response['status_code'],
                                $context
                            ]
                        );
            }
        }

        return
            $definition
                ->setArgument(0, $map)
                ->setArgument(1, new Definition(Predefined::class, [Response::HTTP_INTERNAL_SERVER_ERROR]))
            ;
    }
}