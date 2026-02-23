<?php

declare(strict_types=1);

namespace App\Betting\Domain\Repository;

use App\Betting\Domain\Entity\Prediction;
use App\Betting\Domain\ValueObject\PredictionId;

interface PredictionRepositoryInterface
{
    public function save(Prediction $prediction): void;

    public function findById(PredictionId $id): ?Prediction;

    public function findByMatchId(string $matchId): ?Prediction;

    /** @return list<Prediction> */
    public function findByLeagueCode(string $leagueCode): array;

    /** @return list<Prediction> */
    public function findAll(): array;

    public function count(): int;
}
