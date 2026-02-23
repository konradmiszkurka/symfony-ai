<?php

declare(strict_types=1);

namespace App\Match\Application\Query;

final readonly class GetMatchByIdQuery
{
    public function __construct(
        public string $matchId,
    ) {}
}
