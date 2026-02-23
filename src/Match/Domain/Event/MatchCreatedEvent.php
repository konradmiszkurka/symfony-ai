<?php

declare(strict_types=1);

namespace App\Match\Domain\Event;

use App\Shared\Domain\DomainEvent;

final readonly class MatchCreatedEvent implements DomainEvent
{
    public function __construct(
        public string $matchId,
        public string $homeTeamId,
        public string $awayTeamId,
        public string $leagueId,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
