<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Api\DTO;

final readonly class ApiCompetitionDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $code,
        public string $areaName,
        public ?string $season,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            code: $data['code'],
            areaName: $data['area']['name'] ?? 'Unknown',
            season: isset($data['currentSeason']['startDate'])
                ? substr($data['currentSeason']['startDate'], 0, 4)
                : null,
        );
    }
}
