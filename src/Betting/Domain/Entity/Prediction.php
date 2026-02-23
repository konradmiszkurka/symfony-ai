<?php

declare(strict_types=1);

namespace App\Betting\Domain\Entity;

use App\Betting\Domain\ValueObject\ExpectedGoals;
use App\Betting\Domain\ValueObject\Odds;
use App\Betting\Domain\ValueObject\Probability;
use App\Betting\Domain\ValueObject\PredictionId;
use App\Betting\Domain\ValueObject\ScoreMatrix;
use App\Shared\Domain\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'predictions')]
final class Prediction extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $matchId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $homeTeamId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $awayTeamId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $leagueId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $homeTeamName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $awayTeamName;

    #[ORM\Column(type: 'string', length: 10)]
    private string $leagueCode;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $matchStartDate;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private float $homeWinProbability;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private float $drawProbability;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private float $awayWinProbability;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 3)]
    private float $homeExpectedGoals;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 3)]
    private float $awayExpectedGoals;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private float $homeOdds;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private float $drawOdds;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private float $awayOdds;

    #[ORM\Column(type: 'integer')]
    private int $mostLikelyHomeGoals;

    #[ORM\Column(type: 'integer')]
    private int $mostLikelyAwayGoals;

    #[ORM\Column(type: 'json')]
    private array $scoreMatrix;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $calculatedAt;

    private function __construct(
        PredictionId $id,
        string $matchId,
        string $homeTeamId,
        string $awayTeamId,
        string $leagueId,
        string $homeTeamName,
        string $awayTeamName,
        string $leagueCode,
        \DateTimeImmutable $matchStartDate,
        float $homeWinProbability,
        float $drawProbability,
        float $awayWinProbability,
        float $homeExpectedGoals,
        float $awayExpectedGoals,
        float $homeOdds,
        float $drawOdds,
        float $awayOdds,
        int $mostLikelyHomeGoals,
        int $mostLikelyAwayGoals,
        array $scoreMatrix,
        \DateTimeImmutable $calculatedAt,
    ) {
        $this->id = $id->value;
        $this->matchId = $matchId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->leagueId = $leagueId;
        $this->homeTeamName = $homeTeamName;
        $this->awayTeamName = $awayTeamName;
        $this->leagueCode = $leagueCode;
        $this->matchStartDate = $matchStartDate;
        $this->homeWinProbability = $homeWinProbability;
        $this->drawProbability = $drawProbability;
        $this->awayWinProbability = $awayWinProbability;
        $this->homeExpectedGoals = $homeExpectedGoals;
        $this->awayExpectedGoals = $awayExpectedGoals;
        $this->homeOdds = $homeOdds;
        $this->drawOdds = $drawOdds;
        $this->awayOdds = $awayOdds;
        $this->mostLikelyHomeGoals = $mostLikelyHomeGoals;
        $this->mostLikelyAwayGoals = $mostLikelyAwayGoals;
        $this->scoreMatrix = $scoreMatrix;
        $this->calculatedAt = $calculatedAt;
    }

    public static function create(
        string $matchId,
        string $homeTeamId,
        string $awayTeamId,
        string $leagueId,
        string $homeTeamName,
        string $awayTeamName,
        string $leagueCode,
        \DateTimeImmutable $matchStartDate,
        ExpectedGoals $homeExpectedGoals,
        ExpectedGoals $awayExpectedGoals,
    ): self {
        $matrix = ScoreMatrix::calculate($homeExpectedGoals, $awayExpectedGoals);
        $mostLikely = $matrix->getMostLikely();

        $homeWinProb = $matrix->getHomeWinProbability();
        $drawProb = $matrix->getDrawProbability();
        $awayWinProb = $matrix->getAwayWinProbability();

        $homeOdds = Odds::fromProbability($homeWinProb);
        $drawOdds = Odds::fromProbability($drawProb);
        $awayOdds = Odds::fromProbability($awayWinProb);

        return new self(
            PredictionId::generate(),
            $matchId,
            $homeTeamId,
            $awayTeamId,
            $leagueId,
            $homeTeamName,
            $awayTeamName,
            $leagueCode,
            $matchStartDate,
            $homeWinProb->value,
            $drawProb->value,
            $awayWinProb->value,
            $homeExpectedGoals->value,
            $awayExpectedGoals->value,
            $homeOdds->value,
            $drawOdds->value,
            $awayOdds->value,
            $mostLikely->homeGoals,
            $mostLikely->awayGoals,
            $matrix->probabilities,
            new \DateTimeImmutable(),
        );
    }

    public function recalculate(
        ExpectedGoals $homeExpectedGoals,
        ExpectedGoals $awayExpectedGoals,
    ): void {
        $matrix = ScoreMatrix::calculate($homeExpectedGoals, $awayExpectedGoals);
        $mostLikely = $matrix->getMostLikely();

        $homeWinProb = $matrix->getHomeWinProbability();
        $drawProb = $matrix->getDrawProbability();
        $awayWinProb = $matrix->getAwayWinProbability();

        $homeOdds = Odds::fromProbability($homeWinProb);
        $drawOdds = Odds::fromProbability($drawProb);
        $awayOdds = Odds::fromProbability($awayWinProb);

        $this->homeWinProbability = $homeWinProb->value;
        $this->drawProbability = $drawProb->value;
        $this->awayWinProbability = $awayWinProb->value;
        $this->homeExpectedGoals = $homeExpectedGoals->value;
        $this->awayExpectedGoals = $awayExpectedGoals->value;
        $this->homeOdds = $homeOdds->value;
        $this->drawOdds = $drawOdds->value;
        $this->awayOdds = $awayOdds->value;
        $this->mostLikelyHomeGoals = $mostLikely->homeGoals;
        $this->mostLikelyAwayGoals = $mostLikely->awayGoals;
        $this->scoreMatrix = $matrix->probabilities;
        $this->calculatedAt = new \DateTimeImmutable();
    }

    public function getId(): PredictionId
    {
        return PredictionId::fromString($this->id);
    }

    public function getMatchId(): string
    {
        return $this->matchId;
    }

    public function getHomeTeamId(): string
    {
        return $this->homeTeamId;
    }

    public function getAwayTeamId(): string
    {
        return $this->awayTeamId;
    }

    public function getLeagueId(): string
    {
        return $this->leagueId;
    }

    public function getHomeTeamName(): string
    {
        return $this->homeTeamName;
    }

    public function getAwayTeamName(): string
    {
        return $this->awayTeamName;
    }

    public function getLeagueCode(): string
    {
        return $this->leagueCode;
    }

    public function getMatchStartDate(): \DateTimeImmutable
    {
        return $this->matchStartDate;
    }

    public function getHomeWinProbability(): Probability
    {
        return Probability::create((float) $this->homeWinProbability);
    }

    public function getDrawProbability(): Probability
    {
        return Probability::create((float) $this->drawProbability);
    }

    public function getAwayWinProbability(): Probability
    {
        return Probability::create((float) $this->awayWinProbability);
    }

    public function getHomeExpectedGoals(): ExpectedGoals
    {
        return ExpectedGoals::create((float) $this->homeExpectedGoals);
    }

    public function getAwayExpectedGoals(): ExpectedGoals
    {
        return ExpectedGoals::create((float) $this->awayExpectedGoals);
    }

    public function getHomeOdds(): Odds
    {
        return Odds::create((float) $this->homeOdds);
    }

    public function getDrawOdds(): Odds
    {
        return Odds::create((float) $this->drawOdds);
    }

    public function getAwayOdds(): Odds
    {
        return Odds::create((float) $this->awayOdds);
    }

    public function getMostLikelyHomeGoals(): int
    {
        return $this->mostLikelyHomeGoals;
    }

    public function getMostLikelyAwayGoals(): int
    {
        return $this->mostLikelyAwayGoals;
    }

    public function getScoreMatrix(): array
    {
        return $this->scoreMatrix;
    }

    public function getCalculatedAt(): \DateTimeImmutable
    {
        return $this->calculatedAt;
    }
}
