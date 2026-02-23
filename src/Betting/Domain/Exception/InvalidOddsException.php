<?php

declare(strict_types=1);

namespace App\Betting\Domain\Exception;

final class InvalidOddsException extends \DomainException
{
    public static function belowMinimum(float $value): self
    {
        return new self(\sprintf(
            'Odds value must be >= 1.0, got %f.',
            $value,
        ));
    }
}
