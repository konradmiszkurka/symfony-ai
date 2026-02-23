<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\DTO\MatchDTO;
use App\Match\Application\Query\GetMatchByIdQuery;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\MatchId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetMatchByIdHandler
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
    ) {}

    public function __invoke(GetMatchByIdQuery $query): MatchDTO
    {
        $match = $this->matchRepository->getById(MatchId::fromString($query->matchId));

        return MatchDTO::fromEntity($match);
    }
}
