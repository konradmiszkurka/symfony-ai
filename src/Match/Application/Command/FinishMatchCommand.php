<?php

declare(strict_types=1);

namespace App\Match\Application\Command;

final readonly class FinishMatchCommand
{
    public function __construct(
        public string $matchId,
        public int $homeGoals,
        public int $awayGoals,
    ) {}
}
