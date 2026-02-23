<?php

declare(strict_types=1);

namespace App\Match\Application\DTO;

final readonly class MatchDTO
{
    public function __construct(
        public string $id,
        public string $homeTeamName,
        public string $awayTeamName,
        public string $leagueName,
        public string $leagueCode,
        public \DateTimeImmutable $startDate,
        public string $status,
        public ?int $homeScore,
        public ?int $awayScore,
        public ?int $matchday,
    ) {
    }
}
