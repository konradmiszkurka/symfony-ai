<?php

declare(strict_types=1);

namespace App\Match\Domain\Event;

use App\Shared\Domain\DomainEvent;

final readonly class MatchCancelledEvent implements DomainEvent
{
    public \DateTimeImmutable $occurredOn;

    public function __construct(
        public string $matchId,
        public string $cancelledAt,
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
