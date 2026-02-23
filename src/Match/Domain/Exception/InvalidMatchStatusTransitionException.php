<?php

declare(strict_types=1);

namespace App\Match\Domain\Exception;

use App\Match\Domain\ValueObject\MatchStatus;
use App\Shared\Domain\Exception\DomainException;

final class InvalidMatchStatusTransitionException extends DomainException
{
    public static function create(MatchStatus $from, MatchStatus $to): self
    {
        return new self(sprintf(
            "Cannot transition match from '%s' to '%s'.",
            $from->value,
            $to->value,
        ));
    }
}
