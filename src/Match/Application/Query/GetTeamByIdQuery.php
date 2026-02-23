<?php

declare(strict_types=1);

namespace App\Match\Application\Query;

final readonly class GetTeamByIdQuery
{
    public function __construct(
        public string $teamId,
    ) {}
}
