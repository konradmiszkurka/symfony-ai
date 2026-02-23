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
}
