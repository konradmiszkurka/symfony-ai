<?php

declare(strict_types=1);

namespace App\Betting\Infrastructure\Persistence;

use App\Betting\Domain\Entity\Prediction;
use App\Betting\Domain\Repository\PredictionRepositoryInterface;
use App\Betting\Domain\ValueObject\PredictionId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrinePredictionRepository implements PredictionRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Prediction $prediction): void
    {
        $this->entityManager->persist($prediction);
        $this->entityManager->flush();
    }

    public function findById(PredictionId $id): ?Prediction
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Prediction::class, 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByMatchId(string $matchId): ?Prediction
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Prediction::class, 'p')
            ->where('p.matchId = :matchId')
            ->setParameter('matchId', $matchId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return list<Prediction> */
    public function findByLeagueCode(string $leagueCode): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Prediction::class, 'p')
            ->where('p.leagueCode = :leagueCode')
            ->setParameter('leagueCode', $leagueCode)
            ->orderBy('p.matchStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<Prediction> */
    public function findAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Prediction::class, 'p')
            ->orderBy('p.matchStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function count(): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Prediction::class, 'p')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
