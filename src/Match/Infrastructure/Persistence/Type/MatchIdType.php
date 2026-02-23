<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence\Type;

use App\Match\Domain\ValueObject\MatchId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class MatchIdType extends StringType
{
    public const string NAME = 'match_id';

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?MatchId
    {
        if ($value === null) {
            return null;
        }

        return MatchId::fromString((string) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof MatchId) {
            return $value->value;
        }

        return (string) $value;
    }
}
