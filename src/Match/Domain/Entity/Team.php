<?php

declare(strict_types=1);

namespace App\Match\Domain\Entity;

use App\Match\Domain\ValueObject\TeamId;
use App\Match\Domain\ValueObject\TeamName;
use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'team')]
final class Team extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'team_id')]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $shortName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $logoUrl;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function create(
        TeamId $id,
        TeamName $name,
        ?string $shortName = null,
        ?string $country = null,
        ?string $logoUrl = null,
    ): self {
        $team = new self();
        $team->id = $id->value;
        $team->name = $name->value;
        $team->shortName = $shortName;
        $team->country = $country;
        $team->logoUrl = $logoUrl;
        $team->createdAt = new \DateTimeImmutable();
        $team->updatedAt = new \DateTimeImmutable();

        return $team;
    }

    public function rename(TeamName $name): void
    {
        $this->name = $name->value;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDetails(
        ?string $shortName = null,
        ?string $country = null,
        ?string $logoUrl = null,
    ): void {
        $this->shortName = $shortName;
        $this->country = $country;
        $this->logoUrl = $logoUrl;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): TeamId
    {
        return TeamId::fromString($this->id);
    }

    public function getName(): TeamName
    {
        return TeamName::fromString($this->name);
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getCountry(): ?string
    {
        return $this->country;
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
