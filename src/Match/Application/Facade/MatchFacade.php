<?php

declare(strict_types=1);

namespace App\Match\Application\Facade;

use App\Match\Application\Command\CancelMatchCommand;
use App\Match\Application\Command\CreateLeagueCommand;
use App\Match\Application\Command\CreateMatchCommand;
use App\Match\Application\Command\CreateTeamCommand;
use App\Match\Application\Command\FinishMatchCommand;
use App\Match\Application\Command\StartMatchCommand;
use App\Match\Application\Command\UpdateScoreCommand;
use App\Match\Application\DTO\MatchDTO;
use App\Match\Application\DTO\TeamDTO;
use App\Match\Application\Query\GetMatchByIdQuery;
use App\Match\Application\Query\GetTeamByIdQuery;
use App\Match\Application\Query\ListMatchesQuery;
use App\Match\Application\Query\ListTeamsQuery;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MatchFacade
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
    ) {}

    // Command methods - return void
    public function createTeam(string $name, ?string $shortName = null, ?string $country = null, ?string $logoUrl = null): void
    {
        $this->commandBus->dispatch(new CreateTeamCommand($name, $shortName, $country, $logoUrl));
    }

    public function createLeague(string $name, ?string $country = null, ?string $season = null, ?string $logoUrl = null): void
    {
        $this->commandBus->dispatch(new CreateLeagueCommand($name, $country, $season, $logoUrl));
    }

    public function createMatch(string $homeTeamId, string $awayTeamId, string $leagueId, string $scheduledAt): void
    {
        $this->commandBus->dispatch(new CreateMatchCommand($homeTeamId, $awayTeamId, $leagueId, $scheduledAt));
    }

    public function startMatch(string $matchId): void
    {
        $this->commandBus->dispatch(new StartMatchCommand($matchId));
    }

    public function updateScore(string $matchId, int $homeGoals, int $awayGoals): void
    {
        $this->commandBus->dispatch(new UpdateScoreCommand($matchId, $homeGoals, $awayGoals));
    }

    public function finishMatch(string $matchId, int $homeGoals, int $awayGoals): void
    {
        $this->commandBus->dispatch(new FinishMatchCommand($matchId, $homeGoals, $awayGoals));
    }

    public function cancelMatch(string $matchId): void
    {
        $this->commandBus->dispatch(new CancelMatchCommand($matchId));
    }

    // Query methods - return DTOs
    public function getMatch(string $matchId): MatchDTO
    {
        return $this->handleQuery(new GetMatchByIdQuery($matchId));
    }

    /** @return list<MatchDTO> */
    public function listMatches(
        ?string $leagueId = null,
        ?string $teamId = null,
        ?string $status = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ): array {
        return $this->handleQuery(new ListMatchesQuery($leagueId, $teamId, $status, $dateFrom, $dateTo));
    }

    public function getTeam(string $teamId): TeamDTO
    {
        return $this->handleQuery(new GetTeamByIdQuery($teamId));
    }

    /** @return list<TeamDTO> */
    public function listTeams(?string $country = null): array
    {
        return $this->handleQuery(new ListTeamsQuery($country));
    }

    private function handleQuery(object $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        return $handledStamp->getResult();
    }
}
