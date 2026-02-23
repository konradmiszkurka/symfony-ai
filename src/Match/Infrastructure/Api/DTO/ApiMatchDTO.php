<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Api\DTO;

final readonly class ApiMatchDTO
{
    public function __construct(
        public int $id,
        public int $homeTeamId,
        public int $awayTeamId,
        public string $utcDate,
        public string $status,
        public ?int $homeGoals,
        public ?int $awayGoals,
        public ?int $matchday,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            homeTeamId: $data['homeTeam']['id'],
            awayTeamId: $data['awayTeam']['id'],
            utcDate: $data['utcDate'],
            status: $data['status'],
            homeGoals: $data['score']['fullTime']['home'] ?? null,
            awayGoals: $data['score']['fullTime']['away'] ?? null,
            matchday: $data['matchday'] ?? null,
        );
    }
}
