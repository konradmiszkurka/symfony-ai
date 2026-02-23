<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\CreateTeamCommand;
use App\Match\Domain\Entity\Team;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use App\Match\Domain\ValueObject\TeamId;
use App\Match\Domain\ValueObject\TeamName;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateTeamHandler
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
    ) {}

    public function __invoke(CreateTeamCommand $command): void
    {
        $id = TeamId::generate();
        $name = TeamName::fromString($command->name);

        $team = Team::create(
            id: $id,
            name: $name,
            shortName: $command->shortName,
            country: $command->country,
            logoUrl: $command->logoUrl,
        );

        $this->teamRepository->save($team);
    }
}
