<?php

declare(strict_types=1);

namespace App\Match\Domain\Entity;

use App\Match\Domain\Event\MatchCancelledEvent;
use App\Match\Domain\Event\MatchCreatedEvent;
use App\Match\Domain\Event\MatchFinishedEvent;
use App\Match\Domain\Event\MatchStartedEvent;
use App\Match\Domain\Event\ScoreUpdatedEvent;
use App\Match\Domain\Exception\InvalidMatchStatusTransitionException;
use App\Match\Domain\Exception\InvalidScoreException;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;
use App\Match\Domain\ValueObject\Score;
use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'football_match')]
#[ORM\Index(columns: ['status'], name: 'idx_match_status')]
#[ORM\Index(columns: ['scheduled_at'], name: 'idx_match_scheduled_at')]
final class FootballMatch extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'match_id')]
    private MatchId $id;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'home_team_id', referencedColumnName: 'id', nullable: false)]
    private Team $homeTeam;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'away_team_id', referencedColumnName: 'id', nullable: false)]
    private Team $awayTeam;

    #[ORM\ManyToOne(targetEntity: League::class)]
    #[ORM\JoinColumn(name: 'league_id', referencedColumnName: 'id', nullable: false)]
    private League $league;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $scheduledAt;

    #[ORM\Column(type: 'string', length: 20, enumType: MatchStatus::class)]
    private MatchStatus $status;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $homeGoals = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $awayGoals = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function schedule(
        MatchId $id,
        Team $homeTeam,
        Team $awayTeam,
        League $league,
        \DateTimeImmutable $scheduledAt,
    ): self {
        if ($homeTeam->getId()->equals($awayTeam->getId())) {
            throw new \InvalidArgumentException('Home team and away team must be different.');
        }

        $match = new self();
        $match->id = $id;
        $match->homeTeam = $homeTeam;
        $match->awayTeam = $awayTeam;
        $match->league = $league;
        $match->scheduledAt = $scheduledAt;
        $match->status = MatchStatus::Scheduled;
        $match->createdAt = new \DateTimeImmutable();
        $match->updatedAt = new \DateTimeImmutable();

        $match->recordEvent(new MatchCreatedEvent(
            matchId: $match->id->value,
            homeTeamId: $homeTeam->getId()->value,
            awayTeamId: $awayTeam->getId()->value,
            leagueId: $league->getId()->value,
            scheduledAt: $scheduledAt->format(\DateTimeInterface::ATOM),
        ));

        return $match;
    }

    public function start(): void
    {
        $this->transitionTo(MatchStatus::InProgress);
        $this->homeGoals = 0;
        $this->awayGoals = 0;
        $this->updatedAt = new \DateTimeImmutable();

        $this->recordEvent(new MatchStartedEvent(
            matchId: $this->id->value,
            startedAt: $this->updatedAt->format(\DateTimeInterface::ATOM),
        ));
    }

    public function updateScore(Score $score): void
    {
        if ($this->status !== MatchStatus::InProgress) {
            throw InvalidScoreException::matchNotInProgress($this->id);
        }

        $previousHomeGoals = $this->homeGoals;
        $previousAwayGoals = $this->awayGoals;

        $this->homeGoals = $score->homeGoals;
        $this->awayGoals = $score->awayGoals;
        $this->updatedAt = new \DateTimeImmutable();

        $this->recordEvent(new ScoreUpdatedEvent(
            matchId: $this->id->value,
            homeGoals: $score->homeGoals,
            awayGoals: $score->awayGoals,
            previousHomeGoals: $previousHomeGoals ?? 0,
            previousAwayGoals: $previousAwayGoals ?? 0,
        ));
    }

    public function finish(Score $score): void
    {
        $this->transitionTo(MatchStatus::Finished);
        $this->homeGoals = $score->homeGoals;
        $this->awayGoals = $score->awayGoals;
        $this->updatedAt = new \DateTimeImmutable();

        $this->recordEvent(new MatchFinishedEvent(
            matchId: $this->id->value,
            homeGoals: $score->homeGoals,
            awayGoals: $score->awayGoals,
        ));
    }

    public function cancel(): void
    {
        $this->transitionTo(MatchStatus::Cancelled);
        $this->updatedAt = new \DateTimeImmutable();

        $this->recordEvent(new MatchCancelledEvent(
            matchId: $this->id->value,
            cancelledAt: $this->updatedAt->format(\DateTimeInterface::ATOM),
        ));
    }

    public function postpone(): void
    {
        $this->transitionTo(MatchStatus::Postponed);
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function reschedule(\DateTimeImmutable $scheduledAt): void
    {
        $this->transitionTo(MatchStatus::Scheduled);
        $this->scheduledAt = $scheduledAt;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getScore(): ?Score
    {
        if ($this->homeGoals === null || $this->awayGoals === null) {
            return null;
        }

        return Score::create($this->homeGoals, $this->awayGoals);
    }

    public function getId(): MatchId
    {
        return $this->id;
    }

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    public function getLeague(): League
    {
        return $this->league;
    }

    public function getScheduledAt(): \DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function getStatus(): MatchStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function transitionTo(MatchStatus $newStatus): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw InvalidMatchStatusTransitionException::create($this->status, $newStatus);
        }

        $this->status = $newStatus;
    }
}
