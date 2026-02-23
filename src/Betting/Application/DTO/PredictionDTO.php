<?php

declare(strict_types=1);

namespace App\Betting\Application\DTO;

final readonly class PredictionDTO
{
    public function __construct(
        public string $id,
        public string $matchId,
        public string $homeTeamName,
        public string $awayTeamName,
        public string $leagueCode,
        public \DateTimeImmutable $matchStartDate,
        public float $homeWinProbability,
        public float $drawProbability,
        public float $awayWinProbability,
        public float $homeExpectedGoals,
        public float $awayExpectedGoals,
        public float $homeOdds,
        public float $drawOdds,
        public float $awayOdds,
        public string $mostLikelyScore,
        /** @var list<ScoreProbabilityDTO> */
        public array $topScores,
        /** @var array<string, float> keys "i:j" => probability float */
        public array $scoreMatrixData,
        public \DateTimeImmutable $calculatedAt,
    ) {}
}
