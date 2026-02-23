<?php

declare(strict_types=1);

namespace App\Match\Domain\ValueObject;

final readonly class LeagueName implements \Stringable
{
    private function __construct(
        public string $value,
    ) {}

    public static function fromString(string $value): self
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('League name cannot be empty.');
        }

        if (mb_strlen($trimmed) > 255) {
            throw new \InvalidArgumentException('League name cannot exceed 255 characters.');
        }

        return new self($trimmed);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
