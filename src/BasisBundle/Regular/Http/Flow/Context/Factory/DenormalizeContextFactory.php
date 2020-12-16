<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use Closure;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\ContextInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\ExceptionalContext;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\Factory\ContextFactoryInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\InvalidContext;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\SuccessContext;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\DataExtractorInterface;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\Exception\UnableToDenormalizeDataException;

final class DenormalizeContextFactory implements ContextFactoryInterface
{
    private DataExtractorInterface $dataExtractor;
    private ValidatorInterface $validator;
    private Closure $constraintsProvider;
    private DenormalizerInterface $denormalizer;
    private string $class;

    public function __construct(
        DataExtractorInterface $dataExtractor,
        ValidatorInterface $validator,
        callable $constraintsProvider,
        DenormalizerInterface $denormalizer,
        string $class
    ) {
        $this->dataExtractor = $dataExtractor;
        $this->validator = $validator;
        $this->constraintsProvider = Closure::fromCallable($constraintsProvider);
        $this->denormalizer = $denormalizer;
        $this->class = $class;
    }

    public function createFromRequest(Request $request): ContextInterface
    {
        try {
            $data = $this->dataExtractor->extract($request);

            $violationList = $this->validator->validate($data, $this->constraintsProvider->__invoke());

            if ($violationList->count() > 0) {
                return new InvalidContext($violationList);
            }

            $dto = $this->denormalizer->denormalize($data, $this->class);

            if (!$dto instanceof $this->class) {
                throw new UnableToDenormalizeDataException(
                    sprintf(
                        "Expected instance of '%s', '%s' given",
                        $this->class,
                        get_class($dto)
                    )
                );
            }

            assert($dto instanceof ServiceDtoInterface);

            return new SuccessContext($dto);
        } catch (Throwable $t) {
            return new ExceptionalContext($t);
        }

    }
}