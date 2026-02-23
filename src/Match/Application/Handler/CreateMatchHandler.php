<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\CreateMatchCommand;
use App\Match\Domain\Entity\FootballMatch;
use App\Match\Domain\Repository\LeagueRepositoryInterface;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateMatchHandler
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
        private TeamRepositoryInterface $teamRepository,
        private LeagueRepositoryInterface $leagueRepository,
    ) {}

    public function __invoke(CreateMatchCommand $command): void
    {
        $homeTeam = $this->teamRepository->getById(TeamId::fromString($command->homeTeamId));
        $awayTeam = $this->teamRepository->getById(TeamId::fromString($command->awayTeamId));
        $league = $this->leagueRepository->getById(LeagueId::fromString($command->leagueId));

        $id = MatchId::generate();
        $scheduledAt = new \DateTimeImmutable($command->scheduledAt);

        $match = FootballMatch::schedule(
            id: $id,
            homeTeam: $homeTeam,
            awayTeam: $awayTeam,
            league: $league,
            scheduledAt: $scheduledAt,
        );

        $this->matchRepository->save($match);
    }
}
