<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Api;

final class FootballDataApiException extends \RuntimeException
{
    public static function requestFailed(string $endpoint, int $statusCode): self
    {
        return new self(\sprintf(
            'Football-data.org API request to "%s" failed with status code %d.',
            $endpoint,
            $statusCode,
        ));
    }

    public static function connectionError(string $endpoint, string $message): self
    {
        return new self(\sprintf(
            'Could not connect to football-data.org API endpoint "%s": %s',
            $endpoint,
            $message,
        ));
    }
}
