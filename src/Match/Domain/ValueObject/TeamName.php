<?php

declare(strict_types=1);

namespace App\Match\Domain\ValueObject;

final readonly class TeamName implements \Stringable
{
    public function __construct(
        public string $value,
    ) {
        if ('' === trim($value)) {
            throw new \InvalidArgumentException('Team name cannot be empty.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
