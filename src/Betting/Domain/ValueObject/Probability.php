<?php

declare(strict_types=1);

namespace App\Betting\Domain\ValueObject;

use App\Betting\Domain\Exception\InvalidProbabilityException;

final readonly class Probability implements \Stringable
{
    private function __construct(
        public float $value,
    ) {}

    public static function create(float $value): self
    {
        if ($value < 0.0 || $value > 1.0) {
            throw InvalidProbabilityException::outOfRange($value);
        }

        return new self($value);
    }

    public function asPercentage(): float
    {
        return $this->value * 100;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
