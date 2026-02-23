<?php

declare(strict_types=1);

namespace App\Match\Application\Query;

final readonly class ListTeamsQuery
{
    public function __construct(
        public ?string $country = null,
    ) {}
}
