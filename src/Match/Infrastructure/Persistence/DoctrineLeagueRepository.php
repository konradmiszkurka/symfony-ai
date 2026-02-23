<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence;

use App\Match\Domain\Entity\League;
use App\Match\Domain\Exception\LeagueNotFoundException;
use App\Match\Domain\Repository\LeagueRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineLeagueRepository implements LeagueRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findById(LeagueId $id): ?League
    {
        return $this->entityManager->find(League::class, $id->value);
    }

    public function getById(LeagueId $id): League
    {
        $league = $this->findById($id);

        if ($league === null) {
            throw LeagueNotFoundException::withId($id);
        }

        return $league;
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

    /** @return list<League> */
    public function findBySeason(string $season): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('l')
            ->from(League::class, 'l')
            ->where('l.season = :season')
            ->setParameter('season', $season)
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
