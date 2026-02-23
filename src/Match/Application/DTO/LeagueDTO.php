<?php

declare(strict_types=1);

namespace App\Match\Application\DTO;

use App\Match\Domain\Entity\League;

final readonly class LeagueDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $country,
        public ?string $season,
        public ?string $logoUrl,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromEntity(League $league): self
    {
        return new self(
            id: $league->getId()->value,
            name: $league->getName()->value,
            country: $league->getCountry(),
            season: $league->getSeason(),
            logoUrl: $league->getLogoUrl(),
            createdAt: $league->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $league->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
