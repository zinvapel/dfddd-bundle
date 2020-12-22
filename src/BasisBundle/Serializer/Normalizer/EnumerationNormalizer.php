<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Serializer\Normalizer;

use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zinvapel\Enumeration\BaseEnumeration;

final class EnumerationNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = [])
    {
        return $object->getValue();
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof BaseEnumeration;
    }

    /**
     * {@inheritdoc}
     * @return array|object|null
     */
    public function denormalize($data, string $class, string $format = null, array $context = [])
    {
        try {
            $factory = [$class, 'create'];

            assert(is_callable($factory));

            return $factory($data);
        } catch (InvalidArgumentException $e) {
            if (isset($context[$class]['default'])) {
                $factory = [$class, 'create'];

                assert(is_callable($factory));

                return $factory($context[$class]['default']);
            }

            return null;
        }
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return is_subclass_of($type, BaseEnumeration::class);
    }
}