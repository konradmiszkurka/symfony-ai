<?php

declare(strict_types=1);

namespace App\Match\Application\Facade;

use App\Match\Application\Command\ImportCompetitionCommand;
use App\Match\Application\DTO\ImportResultDTO;
use App\Match\Application\DTO\MatchDTO;
use App\Match\Application\Handler\ImportCompetitionHandler;
use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\Entity\League;
use App\Match\Domain\Entity\Team;
use App\Match\Domain\Repository\LeagueRepositoryInterface;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\MatchStatus;

final readonly class MatchFacade
{
    public function __construct(
        private LeagueRepositoryInterface $leagueRepository,
        private TeamRepositoryInterface $teamRepository,
        private MatchRepositoryInterface $matchRepository,
        private ImportCompetitionHandler $importHandler,
    ) {
    }

    public function importCompetition(string $competitionCode): ImportResultDTO
    {
        return ($this->importHandler)(new ImportCompetitionCommand($competitionCode));
    }

    /** @return list<League> */
    public function getAllLeagues(): array
    {
        return $this->leagueRepository->findAll();
    }

    public function getLeague(string $id): ?League
    {
        return $this->leagueRepository->findById(LeagueId::fromString($id));
    }

    /** @return list<Team> */
    public function getAllTeams(): array
    {
        return $this->teamRepository->findAll();
    }

    public function getTeam(string $id): ?Team
    {
        return $this->teamRepository->findById(\App\Match\Domain\ValueObject\TeamId::fromString($id));
    }

    public function getMatch(string $id): ?FootballMatch
    {
        return $this->matchRepository->findById(MatchId::fromString($id));
    }

    /** @return list<FootballMatch> */
    public function getMatchesByFilters(
        ?string $leagueId = null,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ): array {
        return $this->matchRepository->findByFilters(
            leagueId: $leagueId ? LeagueId::fromString($leagueId) : null,
            status: $status ? MatchStatus::from($status) : null,
            dateFrom: $dateFrom ? new \DateTimeImmutable($dateFrom) : null,
            dateTo: $dateTo ? new \DateTimeImmutable($dateTo) : null,
        );
    }

    /** @return list<FootballMatch> */
    public function getLatestMatches(int $limit = 5): array
    {
        return $this->matchRepository->findLatest($limit);
    }

    public function getLeagueCount(): int
    {
        return $this->leagueRepository->count();
    }

    public function getTeamCount(): int
    {
        return $this->teamRepository->count();
    }

    public function getMatchCount(): int
    {
        return $this->matchRepository->count();
    }

    public function getMatchCountByStatus(MatchStatus $status): int
    {
        return $this->matchRepository->countByStatus($status);
    }

    public static function toMatchDTO(FootballMatch $match): MatchDTO
    {
        return new MatchDTO(
            id: $match->getId()->value,
            homeTeamName: $match->getHomeTeam()->getName(),
            awayTeamName: $match->getAwayTeam()->getName(),
            leagueName: $match->getLeague()->getName(),
            leagueCode: $match->getLeague()->getCode(),
            startDate: $match->getStartDate(),
            status: $match->getStatus()->value,
            homeScore: $match->getHomeScore(),
            awayScore: $match->getAwayScore(),
            matchday: $match->getMatchday(),
        );
    }
}
