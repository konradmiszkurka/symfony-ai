<?php

declare(strict_types=1);

namespace App\Match\Domain\Entity;

use App\Match\Domain\ValueObject\LeagueId;
use App\Shared\Domain\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'leagues')]
final class League extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 10)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $country;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $currentSeason;

    #[ORM\Column(type: 'integer', nullable: true, unique: true)]
    private ?int $externalId;

    /** @var Collection<int, FootballMatch> */
    #[ORM\OneToMany(targetEntity: FootballMatch::class, mappedBy: 'league')]
    private Collection $matches;

    private function __construct(
        LeagueId $id,
        string $name,
        string $code,
        string $country,
        ?string $currentSeason = null,
        ?int $externalId = null,
    ) {
        $this->id = $id->value;
        $this->name = $name;
        $this->code = $code;
        $this->country = $country;
        $this->currentSeason = $currentSeason;
        $this->externalId = $externalId;
        $this->matches = new ArrayCollection();
    }

    public static function create(
        string $name,
        string $code,
        string $country,
        ?string $currentSeason = null,
        ?int $externalId = null,
    ): self {
        return new self(
            LeagueId::generate(),
            $name,
            $code,
            $country,
            $currentSeason,
            $externalId,
        );
    }

    public function getId(): LeagueId
    {
        return LeagueId::fromString($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCurrentSeason(): ?string
    {
        return $this->currentSeason;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function updateDetails(string $name, string $country, ?string $currentSeason): void
    {
        $this->name = $name;
        $this->country = $country;
        $this->currentSeason = $currentSeason;
    }

    /** @return Collection<int, FootballMatch> */
    public function getMatches(): Collection
    {
        return $this->matches;
    }
}
