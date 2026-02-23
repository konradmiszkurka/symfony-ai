<?php

declare(strict_types=1);

namespace App\Match\Application\DTO;

use App\Match\Domain\Entity\FootballMatch;

final readonly class MatchDTO
{
    public function __construct(
        public string $id,
        public TeamDTO $homeTeam,
        public TeamDTO $awayTeam,
        public LeagueDTO $league,
        public string $scheduledAt,
        public string $status,
        public ?ScoreDTO $score,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromEntity(FootballMatch $match): self
    {
        $score = $match->getScore();

        return new self(
            id: $match->getId()->value,
            homeTeam: TeamDTO::fromEntity($match->getHomeTeam()),
            awayTeam: TeamDTO::fromEntity($match->getAwayTeam()),
            league: LeagueDTO::fromEntity($match->getLeague()),
            scheduledAt: $match->getScheduledAt()->format(\DateTimeInterface::ATOM),
            status: $match->getStatus()->value,
            score: $score !== null ? ScoreDTO::fromValueObject($score) : null,
            createdAt: $match->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $match->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
