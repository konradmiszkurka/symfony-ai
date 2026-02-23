<?php

declare(strict_types=1);

namespace App\Match\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class MatchNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(\sprintf('Match with id "%s" not found.', $id));
    }
}
