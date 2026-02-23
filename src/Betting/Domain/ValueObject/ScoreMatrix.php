<?php

declare(strict_types=1);

namespace App\Betting\Domain\ValueObject;

final readonly class ScoreMatrix
{
    /**
     * @param array<string, float> $probabilities keys like "i:j"
     */
    private function __construct(
        public array $probabilities,
        public int $maxGoals,
    ) {}

    public static function calculate(
        ExpectedGoals $home,
        ExpectedGoals $away,
        int $maxGoals = 5,
    ): self {
        $probabilities = [];

        for ($i = 0; $i <= $maxGoals; ++$i) {
            for ($j = 0; $j <= $maxGoals; ++$j) {
                $prob = self::poissonPmf($home->value, $i) * self::poissonPmf($away->value, $j);
                $probabilities[$i . ':' . $j] = $prob;
            }
        }

        return new self($probabilities, $maxGoals);
    }

    public function getMostLikely(): ScoreProbability
    {
        $maxKey = '';
        $maxProb = -1.0;

        foreach ($this->probabilities as $key => $prob) {
            if ($prob > $maxProb) {
                $maxProb = $prob;
                $maxKey = $key;
            }
        }

        [$homeGoals, $awayGoals] = \array_map('intval', \explode(':', $maxKey));

        return new ScoreProbability(
            $homeGoals,
            $awayGoals,
            Probability::create(\min(1.0, \max(0.0, $maxProb))),
        );
    }

    /**
     * @return list<ScoreProbability>
     */
    public function getTopN(int $n): array
    {
        $sorted = $this->probabilities;
        \arsort($sorted);

        $result = [];
        $count = 0;

        foreach ($sorted as $key => $prob) {
            if ($count >= $n) {
                break;
            }
            [$homeGoals, $awayGoals] = \array_map('intval', \explode(':', (string) $key));
            $result[] = new ScoreProbability(
                $homeGoals,
                $awayGoals,
                Probability::create(\min(1.0, \max(0.0, $prob))),
            );
            ++$count;
        }

        return $result;
    }

    public function getHomeWinProbability(): Probability
    {
        $total = 0.0;

        foreach ($this->probabilities as $key => $prob) {
            [$i, $j] = \array_map('intval', \explode(':', (string) $key));
            if ($i > $j) {
                $total += $prob;
            }
        }

        return Probability::create(\min(1.0, \max(0.0, $total)));
    }

    public function getDrawProbability(): Probability
    {
        $total = 0.0;

        foreach ($this->probabilities as $key => $prob) {
            [$i, $j] = \array_map('intval', \explode(':', (string) $key));
            if ($i === $j) {
                $total += $prob;
            }
        }

        return Probability::create(\min(1.0, \max(0.0, $total)));
    }

    public function getAwayWinProbability(): Probability
    {
        $total = 0.0;

        foreach ($this->probabilities as $key => $prob) {
            [$i, $j] = \array_map('intval', \explode(':', (string) $key));
            if ($i < $j) {
                $total += $prob;
            }
        }

        return Probability::create(\min(1.0, \max(0.0, $total)));
    }

    private static function poissonPmf(float $lambda, int $k): float
    {
        if ($lambda <= 0.0) {
            return $k === 0 ? 1.0 : 0.0;
        }

        // P(X=k) = e^(-λ) * λ^k / k!
        return \exp(-$lambda) * ($lambda ** $k) / self::factorial($k);
    }

    private static function factorial(int $n): float
    {
        if ($n <= 1) {
            return 1.0;
        }

        $result = 1.0;
        for ($i = 2; $i <= $n; ++$i) {
            $result *= $i;
        }

        return $result;
    }
}
