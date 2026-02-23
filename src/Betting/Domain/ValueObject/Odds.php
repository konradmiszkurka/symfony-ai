<?php

declare(strict_types=1);

namespace App\Betting\Domain\ValueObject;

use App\Betting\Domain\Exception\InvalidOddsException;

final readonly class Odds implements \Stringable
{
    private function __construct(
        public float $value,
    ) {}

    public static function fromProbability(Probability $probability): self
    {
        if ($probability->value <= 0.0) {
            return new self(999.99);
        }

        $value = 1.0 / $probability->value;

        if ($value < 1.0) {
            throw InvalidOddsException::belowMinimum($value);
        }

        return new self($value);
    }

    public static function create(float $value): self
    {
        if ($value < 1.0) {
            throw InvalidOddsException::belowMinimum($value);
        }

        return new self($value);
    }

    public function __toString(): string
    {
        return \number_format($this->value, 2);
    }
}
