<?php

declare(strict_types=1);

namespace App\Match\Domain\Exception;

use App\Match\Domain\ValueObject\TeamId;
use App\Shared\Domain\Exception\DomainException;

final class TeamNotFoundException extends DomainException
{
    public static function withId(TeamId $id): self
    {
        return new self(sprintf('Team with ID "%s" was not found.', $id->value));
    }
}
