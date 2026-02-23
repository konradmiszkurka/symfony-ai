<?php

declare(strict_types=1);

namespace App\Betting\UI\Command;

use App\Betting\Application\Facade\BettingFacade;
use App\Match\Application\Facade\MatchFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-predictions',
    description: 'Generate Poisson-based match predictions',
)]
final class GeneratePredictionsCommand extends Command
{
    public function __construct(
        private readonly BettingFacade $bettingFacade,
        private readonly MatchFacade $matchFacade,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'league',
            null,
            InputOption::VALUE_OPTIONAL,
            'League code to generate predictions for (e.g. PL)',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $leagueCode = $input->getOption('league');

        $io->title('Generating Predictions');

        if (null !== $leagueCode && '' !== $leagueCode) {
            $league = $this->matchFacade->getAllLeagues();
            $leagueId = null;

            foreach ($league as $l) {
                if ($l->getCode() === $leagueCode) {
                    $leagueId = $l->getId()->value;
                    break;
                }
            }

            if (null === $leagueId) {
                $io->error(\sprintf('League with code "%s" not found.', $leagueCode));

                return Command::FAILURE;
            }

            $count = $this->bettingFacade->generatePredictionsForLeague($leagueId);
            $io->success(\sprintf('Generated %d predictions for league %s.', $count, $leagueCode));
        } else {
            $count = $this->bettingFacade->generateAllPredictions();
            $io->success(\sprintf('Generated %d predictions total.', $count));
        }

        return Command::SUCCESS;
    }
}
