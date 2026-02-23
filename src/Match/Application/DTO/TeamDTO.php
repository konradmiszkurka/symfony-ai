<?php

declare(strict_types=1);

namespace App\Match\Application\DTO;

use App\Match\Domain\Entity\Team;

final readonly class TeamDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $shortName,
        public ?string $country,
        public ?string $logoUrl,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromEntity(Team $team): self
    {
        return new self(
            id: $team->getId()->value,
            name: $team->getName()->value,
            shortName: $team->getShortName(),
            country: $team->getCountry(),
            logoUrl: $team->getLogoUrl(),
            createdAt: $team->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $team->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
