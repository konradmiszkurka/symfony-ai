<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\StartMatchCommand;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\MatchId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class StartMatchHandler
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
    ) {}

    public function __invoke(StartMatchCommand $command): void
    {
        $match = $this->matchRepository->getById(MatchId::fromString($command->matchId));

        $match->start();

        $this->matchRepository->save($match);
    }
}
