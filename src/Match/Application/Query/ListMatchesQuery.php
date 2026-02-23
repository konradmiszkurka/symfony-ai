<?php

declare(strict_types=1);

namespace App\Match\Application\Query;

final readonly class ListMatchesQuery
{
    public function __construct(
        public ?string $leagueId = null,
        public ?string $teamId = null,
        public ?string $status = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
    ) {}
}
