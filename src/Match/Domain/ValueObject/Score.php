<?php

declare(strict_types=1);

namespace App\Match\Domain\ValueObject;

final readonly class Score implements \Stringable
{
    private function __construct(
        public int $homeGoals,
        public int $awayGoals,
    ) {}

    public static function create(int $homeGoals, int $awayGoals): self
    {
        if ($homeGoals < 0 || $awayGoals < 0) {
            throw new \InvalidArgumentException('Goals cannot be negative.');
        }

        return new self($homeGoals, $awayGoals);
    }

    public static function initial(): self
    {
        return new self(0, 0);
    }

    public function total(): int
    {
        return $this->homeGoals + $this->awayGoals;
    }

    public function isDraw(): bool
    {
        return $this->homeGoals === $this->awayGoals;
    }

    /** @return 'home'|'away'|null */
    public function winner(): ?string
    {
        if ($this->isDraw()) {
            return null;
        }

        return $this->homeGoals > $this->awayGoals ? 'home' : 'away';
    }

    public function equals(self $other): bool
    {
        return $this->homeGoals === $other->homeGoals
            && $this->awayGoals === $other->awayGoals;
    }

    public function __toString(): string
    {
        return sprintf('%d-%d', $this->homeGoals, $this->awayGoals);
    }
}
