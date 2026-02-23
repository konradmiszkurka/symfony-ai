<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\DTO\MatchDTO;
use App\Match\Application\Query\ListMatchesQuery;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\LeagueId;
use App\Match\Domain\ValueObject\MatchStatus;
use App\Match\Domain\ValueObject\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class ListMatchesHandler
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
    ) {}

    /** @return list<MatchDTO> */
    public function __invoke(ListMatchesQuery $query): array
    {
        $matches = match (true) {
            $query->leagueId !== null => $this->matchRepository->findByLeague(
                LeagueId::fromString($query->leagueId),
            ),
            $query->teamId !== null => $this->matchRepository->findByTeam(
                TeamId::fromString($query->teamId),
            ),
            $query->status !== null => $this->matchRepository->findByStatus(
                MatchStatus::from($query->status),
            ),
            $query->dateFrom !== null && $query->dateTo !== null => $this->matchRepository->findByDateRange(
                new \DateTimeImmutable($query->dateFrom),
                new \DateTimeImmutable($query->dateTo),
            ),
            default => $this->matchRepository->findAll(),
        };

        return array_map(MatchDTO::fromEntity(...), $matches);
    }
}
