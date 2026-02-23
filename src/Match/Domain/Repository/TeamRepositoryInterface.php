<?php

declare(strict_types=1);

namespace App\Match\Domain\Repository;

use App\Match\Domain\Entity\Team;
use App\Match\Domain\ValueObject\TeamId;

interface TeamRepositoryInterface
{
    public function save(Team $team): void;

    public function remove(Team $team): void;

    public function findById(TeamId $id): ?Team;

    public function findByExternalId(int $externalId): ?Team;

    /** @return list<Team> */
    public function findAll(): array;

    public function count(): int;
}
