<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence\Type;

use App\Match\Domain\ValueObject\LeagueId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class LeagueIdType extends StringType
{
    public const string NAME = 'league_id';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LeagueId) {
            return $value->value;
        }

        return (string) $value;
    }
}
