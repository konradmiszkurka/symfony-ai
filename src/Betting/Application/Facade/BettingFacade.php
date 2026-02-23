<?php

declare(strict_types=1);

namespace App\Betting\Application\Facade;

use App\Betting\Application\DTO\HistoricalMatchData;
use App\Betting\Application\DTO\PredictionDTO;
use App\Betting\Application\DTO\ScoreProbabilityDTO;
use App\Betting\Domain\Entity\Prediction;
use App\Betting\Domain\Exception\InsufficientDataException;
use App\Betting\Domain\Repository\PredictionRepositoryInterface;
use App\Betting\Domain\ValueObject\ExpectedGoals;
use App\Betting\Domain\ValueObject\ScoreMatrix;
use App\Betting\Domain\ValueObject\TeamStrength;
use App\Match\Application\Facade\MatchFacade;
use App\Match\Domain\Entity\FootballMatch;

final readonly class BettingFacade
{
    private const int MIN_MATCHES = 3;

    public function __construct(
        private MatchFacade $matchFacade,
        private PredictionRepositoryInterface $predictionRepository,
    ) {}

    public function generatePredictionForMatch(string $matchId): void
    {
        $match = $this->matchFacade->getMatch($matchId);

        if (null === $match) {
            return;
        }

        $leagueId = $match->getLeague()->getId()->value;

        $finishedMatches = $this->matchFacade->getMatchesByFilters(
            leagueId: $leagueId,
            status: 'finished',
        );

        $historicalData = $this->buildHistoricalData($finishedMatches);

        if (\count($historicalData) === 0) {
            return;
        }

        $totalHomeGoals = 0;
        $totalAwayGoals = 0;

        foreach ($historicalData as $data) {
            $totalHomeGoals += $data->homeGoals;
            $totalAwayGoals += $data->awayGoals;
        }

        $matchCount = \count($historicalData);
        $avgHomeGoals = $totalHomeGoals / $matchCount;
        $avgAwayGoals = $totalAwayGoals / $matchCount;

        if ($avgHomeGoals <= 0.0 || $avgAwayGoals <= 0.0) {
            return;
        }

        $homeTeamId = $match->getHomeTeam()->getId()->value;
        $awayTeamId = $match->getAwayTeam()->getId()->value;

        try {
            $homeStrength = $this->calculateTeamStrength(
                $historicalData,
                $homeTeamId,
                'home',
                $avgHomeGoals,
                $avgAwayGoals,
            );

            $awayStrength = $this->calculateTeamStrength(
                $historicalData,
                $awayTeamId,
                'away',
                $avgHomeGoals,
                $avgAwayGoals,
            );
        } catch (InsufficientDataException) {
            return;
        }

        $expectedHomeGoals = ExpectedGoals::create(
            $homeStrength->attackStrength * $awayStrength->defenseStrength * $avgHomeGoals,
        );

        $expectedAwayGoals = ExpectedGoals::create(
            $awayStrength->attackStrength * $homeStrength->defenseStrength * $avgAwayGoals,
        );

        $existing = $this->predictionRepository->findByMatchId($matchId);

        if (null !== $existing) {
            $existing->recalculate($expectedHomeGoals, $expectedAwayGoals);
            $this->predictionRepository->save($existing);

            return;
        }

        $prediction = Prediction::create(
            matchId: $matchId,
            homeTeamId: $homeTeamId,
            awayTeamId: $awayTeamId,
            leagueId: $leagueId,
            homeTeamName: $match->getHomeTeam()->getName(),
            awayTeamName: $match->getAwayTeam()->getName(),
            leagueCode: $match->getLeague()->getCode(),
            matchStartDate: $match->getStartDate(),
            homeExpectedGoals: $expectedHomeGoals,
            awayExpectedGoals: $expectedAwayGoals,
        );

        $this->predictionRepository->save($prediction);
    }

    public function generatePredictionsForLeague(string $leagueId): int
    {
        $scheduledMatches = $this->matchFacade->getMatchesByFilters(
            leagueId: $leagueId,
            status: 'scheduled',
        );

        $count = 0;

        foreach ($scheduledMatches as $match) {
            try {
                $this->generatePredictionForMatch($match->getId()->value);
                ++$count;
            } catch (\Throwable) {
                // skip on any failure
            }
        }

        return $count;
    }

    public function generateAllPredictions(): int
    {
        $leagues = $this->matchFacade->getAllLeagues();
        $total = 0;

        foreach ($leagues as $league) {
            $total += $this->generatePredictionsForLeague($league->getId()->value);
        }

        return $total;
    }

    public function getPredictionForMatch(string $matchId): ?PredictionDTO
    {
        $prediction = $this->predictionRepository->findByMatchId($matchId);

        if (null === $prediction) {
            return null;
        }

        return $this->toDTO($prediction);
    }

    /**
     * @return list<PredictionDTO>
     */
    public function listPredictions(?string $leagueCode = null): array
    {
        if (null !== $leagueCode && '' !== $leagueCode) {
            $predictions = $this->predictionRepository->findByLeagueCode($leagueCode);
        } else {
            $predictions = $this->predictionRepository->findAll();
        }

        return \array_map($this->toDTO(...), $predictions);
    }

    public function getPredictionCount(): int
    {
        return $this->predictionRepository->count();
    }

    private function toDTO(Prediction $prediction): PredictionDTO
    {
        $matrix = ScoreMatrix::calculate(
            $prediction->getHomeExpectedGoals(),
            $prediction->getAwayExpectedGoals(),
        );

        $topScores = \array_map(
            static fn ($sp) => new ScoreProbabilityDTO(
                homeGoals: $sp->homeGoals,
                awayGoals: $sp->awayGoals,
                probability: $sp->probability->asPercentage(),
            ),
            $matrix->getTopN(10),
        );

        return new PredictionDTO(
            id: $prediction->getId()->value,
            matchId: $prediction->getMatchId(),
            homeTeamName: $prediction->getHomeTeamName(),
            awayTeamName: $prediction->getAwayTeamName(),
            leagueCode: $prediction->getLeagueCode(),
            matchStartDate: $prediction->getMatchStartDate(),
            homeWinProbability: $prediction->getHomeWinProbability()->asPercentage(),
            drawProbability: $prediction->getDrawProbability()->asPercentage(),
            awayWinProbability: $prediction->getAwayWinProbability()->asPercentage(),
            homeExpectedGoals: $prediction->getHomeExpectedGoals()->value,
            awayExpectedGoals: $prediction->getAwayExpectedGoals()->value,
            homeOdds: $prediction->getHomeOdds()->value,
            drawOdds: $prediction->getDrawOdds()->value,
            awayOdds: $prediction->getAwayOdds()->value,
            mostLikelyScore: \sprintf(
                '%d-%d',
                $prediction->getMostLikelyHomeGoals(),
                $prediction->getMostLikelyAwayGoals(),
            ),
            topScores: $topScores,
            scoreMatrixData: $prediction->getScoreMatrix(),
            calculatedAt: $prediction->getCalculatedAt(),
        );
    }

    /**
     * @param list<FootballMatch> $finishedMatches
     * @return list<HistoricalMatchData>
     */
    private function buildHistoricalData(array $finishedMatches): array
    {
        $result = [];

        foreach ($finishedMatches as $match) {
            $homeScore = $match->getHomeScore();
            $awayScore = $match->getAwayScore();

            if (null === $homeScore || null === $awayScore) {
                continue;
            }

            $result[] = new HistoricalMatchData(
                homeTeamId: $match->getHomeTeam()->getId()->value,
                awayTeamId: $match->getAwayTeam()->getId()->value,
                homeGoals: $homeScore,
                awayGoals: $awayScore,
            );
        }

        return $result;
    }

    /**
     * @param list<HistoricalMatchData> $historicalData
     */
    private function calculateTeamStrength(
        array $historicalData,
        string $teamId,
        string $side,
        float $avgHomeGoals,
        float $avgAwayGoals,
    ): TeamStrength {
        if ($side === 'home') {
            $teamMatches = \array_filter(
                $historicalData,
                static fn (HistoricalMatchData $d) => $d->homeTeamId === $teamId,
            );
        } else {
            $teamMatches = \array_filter(
                $historicalData,
                static fn (HistoricalMatchData $d) => $d->awayTeamId === $teamId,
            );
        }

        $teamMatches = \array_values($teamMatches);
        $count = \count($teamMatches);

        if ($count < self::MIN_MATCHES) {
            throw InsufficientDataException::notEnoughMatches($teamId, $side, self::MIN_MATCHES, $count);
        }

        if ($side === 'home') {
            $goalsScored = \array_sum(\array_column(
                \array_map(static fn (HistoricalMatchData $d) => ['g' => $d->homeGoals], $teamMatches),
                'g',
            ));
            $goalsConceded = \array_sum(\array_column(
                \array_map(static fn (HistoricalMatchData $d) => ['g' => $d->awayGoals], $teamMatches),
                'g',
            ));

            $attackStrength = ($goalsScored / $count) / $avgHomeGoals;
            $defenseStrength = ($goalsConceded / $count) / $avgAwayGoals;
        } else {
            $goalsScored = \array_sum(\array_column(
                \array_map(static fn (HistoricalMatchData $d) => ['g' => $d->awayGoals], $teamMatches),
                'g',
            ));
            $goalsConceded = \array_sum(\array_column(
                \array_map(static fn (HistoricalMatchData $d) => ['g' => $d->homeGoals], $teamMatches),
                'g',
            ));

            $attackStrength = ($goalsScored / $count) / $avgAwayGoals;
            $defenseStrength = ($goalsConceded / $count) / $avgHomeGoals;
        }

        return new TeamStrength(
            attackStrength: $attackStrength,
            defenseStrength: $defenseStrength,
        );
    }
}
