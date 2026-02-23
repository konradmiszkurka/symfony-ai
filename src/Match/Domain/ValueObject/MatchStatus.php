<?php

declare(strict_types=1);

namespace App\Match\Domain\ValueObject;

enum MatchStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Finished = 'finished';
    case Postponed = 'postponed';
    case Cancelled = 'cancelled';

    /** @return list<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Scheduled => [self::InProgress, self::Cancelled, self::Postponed],
            self::InProgress => [self::Finished, self::Cancelled],
            self::Postponed => [self::Scheduled, self::Cancelled],
            self::Finished, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
