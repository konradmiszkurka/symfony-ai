<?php

declare(strict_types=1);

namespace App\Match\Domain\Entity;

use App\Match\Domain\ValueObject\TeamId;
use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'teams')]
final class Team extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $shortName;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $tla;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $crest;

    #[ORM\Column(type: 'integer', nullable: true, unique: true)]
    private ?int $externalId;

    private function __construct(
        TeamId $id,
        string $name,
        ?string $shortName = null,
        ?string $tla = null,
        ?string $crest = null,
        ?int $externalId = null,
    ) {
        $this->id = $id->value;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->tla = $tla;
        $this->crest = $crest;
        $this->externalId = $externalId;
    }

    public static function create(
        string $name,
        ?string $shortName = null,
        ?string $tla = null,
        ?string $crest = null,
        ?int $externalId = null,
    ): self {
        return new self(
            TeamId::generate(),
            $name,
            $shortName,
            $tla,
            $crest,
            $externalId,
        );
    }

    public function getId(): TeamId
    {
        return TeamId::fromString($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getTla(): ?string
    {
        return $this->tla;
    }

    public function getCrest(): ?string
    {
        return $this->crest;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function updateDetails(string $name, ?string $shortName, ?string $tla, ?string $crest): void
    {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->tla = $tla;
        $this->crest = $crest;
    }
}
