<?php

declare(strict_types=1);

namespace App\Match\Application\DTO;

use App\Match\Domain\ValueObject\Score;

final readonly class ScoreDTO
{
    public function __construct(
        public int $homeGoals,
        public int $awayGoals,
        public int $total,
        public bool $isDraw,
        public ?string $winner,
    ) {}

    public static function fromValueObject(Score $score): self
    {
        return new self(
            homeGoals: $score->homeGoals,
            awayGoals: $score->awayGoals,
            total: $score->total(),
            isDraw: $score->isDraw(),
            winner: $score->winner(),
        );
    }
}
