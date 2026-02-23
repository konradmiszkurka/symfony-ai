<?php

declare(strict_types=1);

namespace App\Match\Application\Command;

final readonly class ImportCompetitionCommand
{
    public function __construct(
        public string $competitionCode,
    ) {
    }
}
