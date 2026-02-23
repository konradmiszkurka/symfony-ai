<?php

declare(strict_types=1);

namespace App\Betting\Infrastructure\Persistence\Type;

use App\Betting\Domain\ValueObject\PredictionId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class PredictionIdType extends StringType
{
    public const string NAME = 'prediction_id';

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?PredictionId
    {
        if ($value === null) {
            return null;
        }

        return PredictionId::fromString((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof PredictionId) {
            return $value->value;
        }

        return (string) $value;
    }
}
