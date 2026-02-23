<?php

declare(strict_types=1);

namespace App\Match\Application\Command;

final readonly class CreateTeamCommand
{
    public function __construct(
        public string $name,
        public ?string $shortName = null,
        public ?string $country = null,
        public ?string $logoUrl = null,
    ) {}
}
