<?php

declare(strict_types=1);

namespace App\Match\Application\DTO;

final readonly class ImportResultDTO
{
    public function __construct(
        public string $competitionCode,
        public int $teamsImported,
        public int $matchesImported,
        public int $matchesUpdated,
    ) {
    }
}
