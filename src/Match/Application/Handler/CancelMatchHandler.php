<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\CancelMatchCommand;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\MatchId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CancelMatchHandler
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
    ) {}

    public function __invoke(CancelMatchCommand $command): void
    {
        $match = $this->matchRepository->getById(MatchId::fromString($command->matchId));

        $match->cancel();

        $this->matchRepository->save($match);
    }
}
