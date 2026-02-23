<?php

declare(strict_types=1);

namespace App\Betting\Domain\ValueObject;

use App\Betting\Domain\Exception\InvalidExpectedGoalsException;

final readonly class ExpectedGoals implements \Stringable
{
    private function __construct(
        public float $value,
    ) {}

    public static function create(float $value): self
    {
        if ($value < 0.0) {
            throw InvalidExpectedGoalsException::negative($value);
        }

        return new self($value);
    }

    public function __toString(): string
    {
        return \number_format($this->value, 3);
    }
}
