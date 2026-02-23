<?php

declare(strict_types=1);

namespace App\Betting\Domain\Exception;

final class InvalidProbabilityException extends \DomainException
{
    public static function outOfRange(float $value): self
    {
        return new self(\sprintf(
            'Probability value must be between 0.0 and 1.0, got %f.',
            $value,
        ));
    }
}
