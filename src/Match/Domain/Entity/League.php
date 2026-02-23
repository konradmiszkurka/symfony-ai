<?php

declare(strict_types=1);

namespace App\Match\Domain\Entity;

use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\LeagueName;
use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'league')]
final class League extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'league_id')]
    private LeagueId $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $season;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $logoUrl;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function create(
        LeagueId $id,
        LeagueName $name,
        ?string $country = null,
        ?string $season = null,
        ?string $logoUrl = null,
    ): self {
        $league = new self();
        $league->id = $id;
        $league->name = $name->value;
        $league->country = $country;
        $league->season = $season;
        $league->logoUrl = $logoUrl;
        $league->createdAt = new \DateTimeImmutable();
        $league->updatedAt = new \DateTimeImmutable();

        return $league;
    }

    public function rename(LeagueName $name): void
    {
        $this->name = $name->value;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDetails(
        ?string $country = null,
        ?string $season = null,
        ?string $logoUrl = null,
    ): void {
        $this->country = $country;
        $this->season = $season;
        $this->logoUrl = $logoUrl;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): LeagueId
    {
        return $this->id;
    }

    public function getName(): LeagueName
    {
        return LeagueName::fromString($this->name);
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
