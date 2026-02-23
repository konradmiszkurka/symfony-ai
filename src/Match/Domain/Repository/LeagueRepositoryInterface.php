<?php

declare(strict_types=1);

namespace App\Match\Domain\Repository;

use App\Match\Domain\Entity\League;
use App\Match\Domain\ValueObject\LeagueId;

interface LeagueRepositoryInterface
{
    public function save(League $league): void;

    public function findById(LeagueId $id): ?League;

    public function findByExternalId(int $externalId): ?League;

    public function findByCode(string $code): ?League;

    /** @return list<League> */
    public function findAll(): array;

    public function count(): int;
}
