<?php

declare(strict_types=1);

namespace App\Match\UI\Command;

use App\Match\Application\Facade\MatchFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-football-data',
    description: 'Import leagues, teams and matches from football-data.org',
)]
final class ImportFootballDataCommand extends Command
{
    private const array DEFAULT_COMPETITIONS = ['PL', 'BL1', 'PD', 'SA'];

    public function __construct(
        private readonly MatchFacade $matchFacade,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'competitions',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Competition codes to import (e.g. PL BL1 PD SA)',
                self::DEFAULT_COMPETITIONS,
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Only show what would be imported without actually importing',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $competitions = $input->getArgument('competitions');
        $dryRun = $input->getOption('dry-run');

        $io->title('Football Data Import');

        if ($dryRun) {
            $io->note('DRY RUN — no data will be imported.');
            $io->listing(array_map(
                static fn (string $code) => \sprintf('Would import competition: %s', $code),
                $competitions,
            ));
            $io->success('Dry run complete.');

            return Command::SUCCESS;
        }

        $io->progressStart(\count($competitions));

        foreach ($competitions as $code) {
            $io->text(\sprintf('Importing %s...', $code));

            try {
                $result = $this->matchFacade->importCompetition($code);
                $io->text(\sprintf(
                    '  -> %s: %d teams imported, %d matches imported, %d matches updated',
                    $result->competitionCode,
                    $result->teamsImported,
                    $result->matchesImported,
                    $result->matchesUpdated,
                ));
            } catch (\Throwable $e) {
                $io->error(\sprintf('Failed to import %s: %s', $code, $e->getMessage()));
            }

            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success('Import completed.');

        return Command::SUCCESS;
    }
}
