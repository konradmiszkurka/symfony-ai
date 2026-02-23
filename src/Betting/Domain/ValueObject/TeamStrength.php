<?php

declare(strict_types=1);

namespace App\Betting\Domain\ValueObject;

final readonly class TeamStrength
{
    public function __construct(
        public float $attackStrength,
        public float $defenseStrength,
    ) {}
}
