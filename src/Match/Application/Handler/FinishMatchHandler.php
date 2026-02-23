<?php

declare(strict_types=1);

namespace App\Match\Application\Handler;

use App\Match\Application\Command\FinishMatchCommand;
use App\Match\Domain\Repository\MatchRepositoryInterface;
use App\Match\Domain\ValueObject\MatchId;
use App\Match\Domain\ValueObject\Score;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class FinishMatchHandler
{
    public function __construct(
        private MatchRepositoryInterface $matchRepository,
    ) {}

    public function __invoke(FinishMatchCommand $command): void
    {
        $match = $this->matchRepository->getById(MatchId::fromString($command->matchId));

        $score = Score::create($command->homeGoals, $command->awayGoals);
        $match->finish($score);

        $this->matchRepository->save($match);
    }
}
