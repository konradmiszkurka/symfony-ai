<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\DTO\TeamDTO;
use App\Match\Application\Query\GetTeamByIdQuery;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use App\Match\Domain\ValueObject\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetTeamByIdHandler
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
    ) {}

    public function __invoke(GetTeamByIdQuery $query): TeamDTO
    {
        $team = $this->teamRepository->getById(TeamId::fromString($query->teamId));

        return TeamDTO::fromEntity($team);
    }
}
