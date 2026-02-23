<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\DTO\TeamDTO;
use App\Match\Application\Query\ListTeamsQuery;
use App\Match\Domain\Repository\TeamRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class ListTeamsHandler
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
    ) {}

    /** @return list<TeamDTO> */
    public function __invoke(ListTeamsQuery $query): array
    {
        $teams = $query->country !== null
            ? $this->teamRepository->findByCountry($query->country)
            : $this->teamRepository->findAll();

        return array_map(TeamDTO::fromEntity(...), $teams);
    }
}
