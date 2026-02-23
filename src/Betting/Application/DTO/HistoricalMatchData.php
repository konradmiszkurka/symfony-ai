<?php

declare(strict_types=1);

namespace App\Betting\Application\DTO;

final readonly class HistoricalMatchData
{
    public function __construct(
        public string $homeTeamId,
        public string $awayTeamId,
        public int $homeGoals,
        public int $awayGoals,
    ) {}
}
