<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Persistence\Type;

use App\Match\Domain\ValueObject\TeamId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class TeamIdType extends StringType
{
    public const string NAME = 'team_id';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof TeamId) {
            return $value->value;
        }

        return (string) $value;
    }
}
