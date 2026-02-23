<?php

declare(strict_types=1);

namespace App\Betting\Domain\Exception;

final class InvalidExpectedGoalsException extends \DomainException
{
    public static function negative(float $value): self
    {
        return new self(\sprintf(
            'Expected goals value must be >= 0.0, got %f.',
            $value,
        ));
    }
}
