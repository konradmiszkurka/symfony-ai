<?php

declare(strict_types=1);

namespace App\Match\Application\Command;

final readonly class CreateMatchCommand
{
    public function __construct(
        public string $homeTeamId,
        public string $awayTeamId,
        public string $leagueId,
        public string $scheduledAt,
    ) {}
}
