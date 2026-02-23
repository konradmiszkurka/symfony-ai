<?php

declare(strict_types=1);

namespace App\Match\Domain\Entity;

use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;
use App\Match\Domain\ValueObject\Score;
use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'football_matches')]
final class FootballMatch extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: League::class, inversedBy: 'matches')]
    #[ORM\JoinColumn(name: 'league_id', referencedColumnName: 'id', nullable: false)]
    private League $league;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'home_team_id', referencedColumnName: 'id', nullable: false)]
    private Team $homeTeam;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'away_team_id', referencedColumnName: 'id', nullable: false)]
    private Team $awayTeam;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'string', length: 20, enumType: MatchStatus::class)]
    private MatchStatus $status;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $homeScore;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $awayScore;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $matchday;

    #[ORM\Column(type: 'integer', nullable: true, unique: true)]
    private ?int $externalId;

    private function __construct(
        MatchId $id,
        League $league,
        Team $homeTeam,
        Team $awayTeam,
        \DateTimeImmutable $startDate,
        MatchStatus $status,
        ?int $homeScore,
        ?int $awayScore,
        ?int $matchday,
        ?int $externalId,
    ) {
        $this->id = $id->value;
        $this->league = $league;
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->startDate = $startDate;
        $this->status = $status;
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
        $this->matchday = $matchday;
        $this->externalId = $externalId;
    }

    public static function create(
        League $league,
        Team $homeTeam,
        Team $awayTeam,
        \DateTimeImmutable $startDate,
        MatchStatus $status = MatchStatus::Scheduled,
        ?int $homeScore = null,
        ?int $awayScore = null,
        ?int $matchday = null,
        ?int $externalId = null,
    ): self {
        return new self(
            MatchId::generate(),
            $league,
            $homeTeam,
            $awayTeam,
            $startDate,
            $status,
            $homeScore,
            $awayScore,
            $matchday,
            $externalId,
        );
    }

    public function getId(): MatchId
    {
        return MatchId::fromString($this->id);
    }

    public function getLeague(): League
    {
        return $this->league;
    }

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getStatus(): MatchStatus
    {
        return $this->status;
    }

    public function getScore(): ?Score
    {
        if (null === $this->homeScore || null === $this->awayScore) {
            return null;
        }

        return new Score($this->homeScore, $this->awayScore);
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function getMatchday(): ?int
    {
        return $this->matchday;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function updateScore(int $homeScore, int $awayScore): void
    {
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }

    public function updateStatus(MatchStatus $status): void
    {
        $this->status = $status;
    }

    public function finish(int $homeScore, int $awayScore): void
    {
        $this->status = MatchStatus::Finished;
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }

    public function cancel(): void
    {
        $this->status = MatchStatus::Cancelled;
    }

    public function postpone(): void
    {
        $this->status = MatchStatus::Postponed;
    }
}
