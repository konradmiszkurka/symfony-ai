<?php

declare(strict_types=1);

namespace App\Match\Domain\Event;

use App\Shared\Domain\DomainEvent;

final readonly class MatchFinishedEvent implements DomainEvent
{
    public \DateTimeImmutable $occurredOn;

    public function __construct(
        public string $matchId,
        public int $homeGoals,
        public int $awayGoals,
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
