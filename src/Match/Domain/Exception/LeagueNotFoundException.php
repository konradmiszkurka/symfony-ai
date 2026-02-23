<?php

declare(strict_types=1);

namespace App\Match\Domain\Exception;

use App\Match\Domain\ValueObject\LeagueId;
use App\Shared\Domain\Exception\DomainException;

final class LeagueNotFoundException extends DomainException
{
    public static function withId(LeagueId $id): self
    {
        return new self(sprintf('League with ID "%s" was not found.', $id->value));
    }
}
