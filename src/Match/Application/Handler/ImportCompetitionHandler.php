<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\ImportCompetitionCommand;
use App\Match\Application\DTO\ImportResultDTO;
use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\Entity\League;
use App\Match\Domain\Entity\Team;
use App\Match\Domain\Repository\LeagueRepositoryInterface;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use App\Match\Infrastructure\Api\ApiMatchStatusMapper;
use App\Match\Infrastructure\Api\FootballDataApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ImportCompetitionHandler
{
    public function __construct(
        private FootballDataApiClient $apiClient,
        private LeagueRepositoryInterface $leagueRepository,
        private TeamRepositoryInterface $teamRepository,
        private MatchRepositoryInterface $matchRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ImportCompetitionCommand $command): ImportResultDTO
    {
        $code = $command->competitionCode;
        $this->logger->info('Starting import for competition: {code}', ['code' => $code]);

        return $this->entityManager->wrapInTransaction(function () use ($code): ImportResultDTO {
            // 1. Import league
            $apiCompetition = $this->apiClient->getCompetition($code);
            $league = $this->leagueRepository->findByExternalId($apiCompetition->id);

            if (null === $league) {
                $league = League::create(
                    name: $apiCompetition->name,
                    code: $apiCompetition->code,
                    country: $apiCompetition->areaName,
                    currentSeason: $apiCompetition->season,
                    externalId: $apiCompetition->id,
                );
                $this->leagueRepository->save($league);
                $this->logger->info('Created league: {name}', ['name' => $apiCompetition->name]);
            } else {
                $league->updateDetails($apiCompetition->name, $apiCompetition->areaName, $apiCompetition->season);
                $this->leagueRepository->save($league);
            }

            // 2. Import teams
            $apiTeams = $this->apiClient->getTeams($code);
            $teamsImported = 0;
            $teamMap = []; // externalId => Team

            foreach ($apiTeams as $apiTeam) {
                $team = $this->teamRepository->findByExternalId($apiTeam->id);

                if (null === $team) {
                    $team = Team::create(
                        name: $apiTeam->name,
                        shortName: $apiTeam->shortName,
                        tla: $apiTeam->tla,
                        crest: $apiTeam->crest,
                        externalId: $apiTeam->id,
                    );
                    $this->teamRepository->save($team);
                    ++$teamsImported;
                } else {
                    $team->updateDetails($apiTeam->name, $apiTeam->shortName, $apiTeam->tla, $apiTeam->crest);
                    $this->teamRepository->save($team);
                }

                $teamMap[$apiTeam->id] = $team;
            }

            $this->logger->info('Teams imported: {count}', ['count' => $teamsImported]);

            // 3. Import matches
            $apiMatches = $this->apiClient->getMatches($code);
            $matchesImported = 0;
            $matchesUpdated = 0;

            foreach ($apiMatches as $apiMatch) {
                $homeTeam = $teamMap[$apiMatch->homeTeamId] ?? $this->teamRepository->findByExternalId($apiMatch->homeTeamId);
                $awayTeam = $teamMap[$apiMatch->awayTeamId] ?? $this->teamRepository->findByExternalId($apiMatch->awayTeamId);

                if (null === $homeTeam || null === $awayTeam) {
                    $this->logger->warning('Skipping match {id}: team not found', ['id' => $apiMatch->id]);
                    continue;
                }

                $status = ApiMatchStatusMapper::toDomain($apiMatch->status);
                $existingMatch = $this->matchRepository->findByExternalId($apiMatch->id);

                if (null === $existingMatch) {
                    $match = FootballMatch::create(
                        league: $league,
                        homeTeam: $homeTeam,
                        awayTeam: $awayTeam,
                        startDate: new \DateTimeImmutable($apiMatch->utcDate),
                        status: $status,
                        homeScore: $apiMatch->homeGoals,
                        awayScore: $apiMatch->awayGoals,
                        matchday: $apiMatch->matchday,
                        externalId: $apiMatch->id,
                    );
                    $this->matchRepository->save($match);
                    ++$matchesImported;
                } else {
                    $updated = false;

                    if ($existingMatch->getStatus() !== $status) {
                        $existingMatch->updateStatus($status);
                        $updated = true;
                    }

                    if ($apiMatch->homeGoals !== null && $apiMatch->awayGoals !== null) {
                        if ($existingMatch->getHomeScore() !== $apiMatch->homeGoals
                            || $existingMatch->getAwayScore() !== $apiMatch->awayGoals) {
                            $existingMatch->updateScore($apiMatch->homeGoals, $apiMatch->awayGoals);
                            $updated = true;
                        }
                    }

                    if ($updated) {
                        $this->matchRepository->save($existingMatch);
                        ++$matchesUpdated;
                    }
                }
            }

            $this->logger->info('Matches imported: {imported}, updated: {updated}', [
                'imported' => $matchesImported,
                'updated' => $matchesUpdated,
            ]);

            return new ImportResultDTO(
                competitionCode: $code,
                teamsImported: $teamsImported,
                matchesImported: $matchesImported,
                matchesUpdated: $matchesUpdated,
            );
        });
    }
}
