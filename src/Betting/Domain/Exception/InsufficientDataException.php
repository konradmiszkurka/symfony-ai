<?php

declare(strict_types=1);

namespace App\Betting\Domain\Exception;

final class InsufficientDataException extends \DomainException
{
    public static function notEnoughMatches(string $teamId, string $side, int $required, int $actual): self
    {
        return new self(\sprintf(
            'Team %s needs at least %d %s matches for calculation, only %d found.',
            $teamId,
            $required,
            $side,
            $actual,
        ));
    }

    public static function noLeagueData(string $leagueCode): self
    {
        return new self(\sprintf(
            'No finished matches found for league %s to compute averages.',
            $leagueCode,
        ));
    }
}
