<?php

declare(strict_types=1);

namespace App\Match\Domain\Repository;

use App\Match\Domain\Entity\League;
use App\Match\Domain\ValueObject\LeagueId;

interface LeagueRepositoryInterface
{
    public function findById(LeagueId $id): ?League;

    /** @throws \App\Match\Domain\Exception\LeagueNotFoundException */
    public function getById(LeagueId $id): League;

    public function save(League $league): void;

    public function remove(League $league): void;

    /** @return list<League> */
    public function findAll(): array;

    /** @return list<League> */
    public function findBySeason(string $season): array;
}
