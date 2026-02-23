<?php

declare(strict_types=1);

namespace App\Match\Domain\Repository;

use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;
use App\Match\Domain\ValueObject\TeamId;

interface MatchRepositoryInterface
{
    public function findById(MatchId $id): ?FootballMatch;

    /** @throws \App\Match\Domain\Exception\MatchNotFoundException */
    public function getById(MatchId $id): FootballMatch;

    public function save(FootballMatch $match): void;

    public function remove(FootballMatch $match): void;

    /** @return list<FootballMatch> */
    public function findAll(): array;

    /** @return list<FootballMatch> */
    public function findByLeague(LeagueId $leagueId): array;

    /** @return list<FootballMatch> */
    public function findByTeam(TeamId $teamId): array;

    /** @return list<FootballMatch> */
    public function findByStatus(MatchStatus $status): array;

    /** @return list<FootballMatch> */
    public function findByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array;
}
