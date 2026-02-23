<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence;

use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\Exception\MatchNotFoundException;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;
use App\Match\Domain\ValueObject\TeamId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineMatchRepository implements MatchRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findById(MatchId $id): ?FootballMatch
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m', 'ht', 'at', 'l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.id = :id')
            ->setParameter('id', $id->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getById(MatchId $id): FootballMatch
    {
        $match = $this->findById($id);

        if ($match === null) {
            throw MatchNotFoundException::withId($id);
        }

        return $match;
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

    /** @return list<FootballMatch> */
    public function findAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m', 'ht', 'at', 'l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->orderBy('m.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<FootballMatch> */
    public function findByLeague(LeagueId $leagueId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m', 'ht', 'at', 'l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.league = :leagueId')
            ->setParameter('leagueId', $leagueId->value)
            ->orderBy('m.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<FootballMatch> */
    public function findByTeam(TeamId $teamId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m', 'ht', 'at', 'l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.homeTeam = :teamId OR m.awayTeam = :teamId')
            ->setParameter('teamId', $teamId->value)
            ->orderBy('m.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<FootballMatch> */
    public function findByStatus(MatchStatus $status): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m', 'ht', 'at', 'l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.status = :status')
            ->setParameter('status', $status)
            ->orderBy('m.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return list<FootballMatch> */
    public function findByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m', 'ht', 'at', 'l')
            ->from(FootballMatch::class, 'm')
            ->join('m.homeTeam', 'ht')
            ->join('m.awayTeam', 'at')
            ->join('m.league', 'l')
            ->where('m.scheduledAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('m.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
