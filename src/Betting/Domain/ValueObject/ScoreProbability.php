<?php

declare(strict_types=1);

namespace App\Betting\Domain\ValueObject;

final readonly class ScoreProbability
{
    public function __construct(
        public int $homeGoals,
        public int $awayGoals,
        public Probability $probability,
    ) {}

    public function label(): string
    {
        return \sprintf('%d-%d', $this->homeGoals, $this->awayGoals);
    }
}
