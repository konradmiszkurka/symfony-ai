<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence;

use App\Match\Domain\Entity\League;
use App\Match\Domain\Repository\LeagueRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineLeagueRepository implements LeagueRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(League $league): void
    {
        $this->entityManager->persist($league);
        $this->entityManager->flush();
    }

    public function remove(League $league): void
    {
        $this->entityManager->remove($league);
        $this->entityManager->flush();
    }

    public function findById(LeagueId $id): ?League
    {
        return $this->entityManager->find(League::class, $id->value);
    }

    public function findByExternalId(int $externalId): ?League
    {
        return $this->entityManager->createQueryBuilder()
            ->select('l')
            ->from(League::class, 'l')
            ->where('l.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByCode(string $code): ?League
    {
        return $this->entityManager->createQueryBuilder()
            ->select('l')
            ->from(League::class, 'l')
            ->where('l.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return list<League> */
    public function findAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('l')
            ->from(League::class, 'l')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function count(): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(l.id)')
            ->from(League::class, 'l')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
