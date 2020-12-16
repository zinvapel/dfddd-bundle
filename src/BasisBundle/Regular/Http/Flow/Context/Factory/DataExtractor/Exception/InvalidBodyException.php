<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Exception;

use Exception;
use Zinvapel\Basis\BasisBundle\Core\Exception\PublicExceptionInterface;

final class InvalidBodyException extends Exception implements PublicExceptionInterface
{

}