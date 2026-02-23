<?php

declare(strict_types=1);

namespace App\Betting\Application\DTO;

final readonly class ScoreProbabilityDTO
{
    public function __construct(
        public int $homeGoals,
        public int $awayGoals,
        public float $probability,
    ) {}
}
