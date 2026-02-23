<?php

declare(strict_types=1);

namespace App\Match\Domain\Repository;

use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;

interface MatchRepositoryInterface
{
    public function save(FootballMatch $match): void;

    public function remove(FootballMatch $match): void;

    public function findById(MatchId $id): ?FootballMatch;

    public function findByExternalId(int $externalId): ?FootballMatch;

    /**
     * @return list<FootballMatch>
     */
    public function findByFilters(
        ?LeagueId $leagueId = null,
        ?MatchStatus $status = null,
        ?\DateTimeImmutable $dateFrom = null,
        ?\DateTimeImmutable $dateTo = null,
    ): array;

    /** @return list<FootballMatch> */
    public function findLatest(int $limit = 5): array;

    public function count(): int;

    public function countByStatus(MatchStatus $status): int;
}
