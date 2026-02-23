<?php

declare(strict_types=1);

namespace App\Match\Application\Command;

final readonly class CreateLeagueCommand
{
    public function __construct(
        public string $name,
        public ?string $country = null,
        public ?string $season = null,
        public ?string $logoUrl = null,
    ) {}
}
