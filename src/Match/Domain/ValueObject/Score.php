<?php

declare(strict_types=1);

namespace App\Match\Domain\ValueObject;

final readonly class Score implements \Stringable
{
    public function __construct(
        public int $home,
        public int $away,
    ) {
        if ($home < 0 || $away < 0) {
            throw new \InvalidArgumentException('Score cannot be negative.');
        }
    }

    public function total(): int
    {
        return $this->home + $this->away;
    }

    public function isDraw(): bool
    {
        return $this->home === $this->away;
    }

    /** @return 'home'|'away'|null */
    public function winner(): ?string
    {
        if ($this->isDraw()) {
            return null;
        }

        return $this->home > $this->away ? 'home' : 'away';
    }

    public function equals(self $other): bool
    {
        return $this->home === $other->home && $this->away === $other->away;
    }

    public function __toString(): string
    {
        return \sprintf('%d:%d', $this->home, $this->away);
    }
}
