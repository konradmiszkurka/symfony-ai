<?php

declare(strict_types=1);

namespace App\Match\Domain\Repository;

use App\Match\Domain\Entity\Team;
use App\Match\Domain\ValueObject\TeamId;

interface TeamRepositoryInterface
{
    public function findById(TeamId $id): ?Team;

    /** @throws \App\Match\Domain\Exception\TeamNotFoundException */
    public function getById(TeamId $id): Team;

    public function save(Team $team): void;

    public function remove(Team $team): void;

    /** @return list<Team> */
    public function findAll(): array;

    /** @return list<Team> */
    public function findByCountry(string $country): array;
}
