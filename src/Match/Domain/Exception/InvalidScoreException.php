<?php

declare(strict_types=1);

namespace App\Match\Domain\Exception;

use App\Match\Domain\ValueObject\MatchId;
use App\Shared\Domain\Exception\DomainException;

final class InvalidScoreException extends DomainException
{
    public static function matchNotInProgress(MatchId $id): self
    {
        return new self(sprintf('Cannot update score: match %s is not in progress.', $id->value));
    }
}
