<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto;

use Symfony\Component\Serializer\Annotation as Serializer;

final class ViolationDto
{
    /**
     * @Serializer\Groups({"body"})
     */
    private string $path;

    /**
     * @Serializer\Groups({"body"})
     */
    private string $message;

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}