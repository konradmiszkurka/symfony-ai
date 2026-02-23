<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence;

use App\Match\Domain\Entity\Team;
use App\Match\Domain\Exception\TeamNotFoundException;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use App\Match\Domain\ValueObject\TeamId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTeamRepository implements TeamRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findById(TeamId $id): ?Team
    {
        return $this->entityManager->find(Team::class, $id->value);
    }

    public function getById(TeamId $id): Team
    {
        $team = $this->findById($id);

        if ($team === null) {
            throw TeamNotFoundException::withId($id);
        }

        return $team;
    }

    public function save(Team $team): void
    {
        $this->entityManager->persist($team);
        $this->entityManager->flush();
    }

    public function remove(Team $team): void
    {
        $this->entityManager->remove($team);
        $this->entityManager->flush();
    }

    /** @return list<Team> */
    public function findAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<Team> */
    public function findByCountry(string $country): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't')
            ->where('t.country = :country')
            ->setParameter('country', $country)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
