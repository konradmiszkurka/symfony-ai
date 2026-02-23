<?php

declare(strict_types=1);

namespace App\Match\Domain\Exception;

use App\Match\Domain\ValueObject\MatchId;
use App\Shared\Domain\Exception\DomainException;

final class MatchNotFoundException extends DomainException
{
    public static function withId(MatchId $id): self
    {
        return new self(sprintf('Match with ID "%s" was not found.', $id->value));
    }
}
