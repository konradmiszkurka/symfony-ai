<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\CreateLeagueCommand;
use App\Match\Domain\Entity\League;
use App\Match\Domain\Repository\LeagueRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\LeagueName;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateLeagueHandler
{
    public function __construct(
        private LeagueRepositoryInterface $leagueRepository,
    ) {}

    public function __invoke(CreateLeagueCommand $command): void
    {
        $id = LeagueId::generate();
        $name = LeagueName::fromString($command->name);

        $league = League::create(
            id: $id,
            name: $name,
            country: $command->country,
            season: $command->season,
            logoUrl: $command->logoUrl,
        );

        $this->leagueRepository->save($league);
    }
}
