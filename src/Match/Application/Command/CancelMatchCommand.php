<?php

declare(strict_types=1);

namespace App\Match\Application\Command;

final readonly class CancelMatchCommand
{
    public function __construct(
        public string $matchId,
    ) {}
}
