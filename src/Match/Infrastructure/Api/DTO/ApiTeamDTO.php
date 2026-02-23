<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Api\DTO;

final readonly class ApiTeamDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $shortName,
        public ?string $tla,
        public ?string $crest,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            shortName: $data['shortName'] ?? null,
            tla: $data['tla'] ?? null,
            crest: $data['crest'] ?? null,
        );
    }
}
