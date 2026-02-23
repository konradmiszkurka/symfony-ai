<?php

declare(strict_types=1);

namespace App\Match\Domain\Event;

use App\Shared\Domain\DomainEvent;

final readonly class ScoreUpdatedEvent implements DomainEvent
{
    public function __construct(
        public string $matchId,
        public int $homeScore,
        public int $awayScore,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
