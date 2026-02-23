<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence;

use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineMatchRepository implements MatchRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(FootballMatch $match): void
    {
        $this->entityManager->persist($match);
        $this->entityManager->flush();
    }

    public function remove(FootballMatch $match): void
    {
        $this->entityManager->remove($match);
        $this->entityManager->flush();
    }

    public function findById(MatchId $id): ?FootballMatch
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m, ht, at, l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.id = :id')
            ->setParameter('id', $id->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByExternalId(int $externalId): ?FootballMatch
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m, ht, at, l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return list<FootballMatch> */
    public function findByFilters(
        ?LeagueId $leagueId = null,
        ?MatchStatus $status = null,
        ?\DateTimeImmutable $dateFrom = null,
        ?\DateTimeImmutable $dateTo = null,
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('m, ht, at, l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l');

        if (null !== $leagueId) {
            $qb->andWhere('l.id = :leagueId')
                ->setParameter('leagueId', $leagueId->value);
        }

        if (null !== $status) {
            $qb->andWhere('m.status = :status')
                ->setParameter('status', $status->value);
        }

        if (null !== $dateFrom) {
            $qb->andWhere('m.startDate >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        if (null !== $dateTo) {
            $qb->andWhere('m.startDate <= :dateTo')
                ->setParameter('dateTo', $dateTo);
        }

        return $qb->orderBy('m.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<FootballMatch> */
    public function findLatest(int $limit = 5): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m, ht, at, l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->orderBy('m.startDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function count(): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(FootballMatch::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByStatus(MatchStatus $status): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(FootballMatch::class, 'm')
            ->where('m.status = :status')
            ->setParameter('status', $status->value)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
