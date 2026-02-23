<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Api;

use App\Match\Domain\ValueObject\MatchStatus;

final class ApiMatchStatusMapper
{
    private const array STATUS_MAP = [
        'SCHEDULED' => MatchStatus::Scheduled,
        'TIMED' => MatchStatus::Scheduled,
        'IN_PLAY' => MatchStatus::InProgress,
        'PAUSED' => MatchStatus::InProgress,
        'FINISHED' => MatchStatus::Finished,
        'AWARDED' => MatchStatus::Finished,
        'POSTPONED' => MatchStatus::Postponed,
        'CANCELLED' => MatchStatus::Cancelled,
        'SUSPENDED' => MatchStatus::Cancelled,
    ];

    public static function toDomain(string $apiStatus): MatchStatus
    {
        return self::STATUS_MAP[$apiStatus] ?? MatchStatus::Scheduled;
    }
}
