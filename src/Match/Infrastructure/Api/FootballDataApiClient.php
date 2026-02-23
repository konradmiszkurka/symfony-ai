<?php

declare(strict_types=1);

namespace App\Match\Infrastructure\Api;

use App\Match\Infrastructure\Api\DTO\ApiCompetitionDTO;
use App\Match\Infrastructure\Api\DTO\ApiMatchDTO;
use App\Match\Infrastructure\Api\DTO\ApiTeamDTO;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class FootballDataApiClient
{
    public function __construct(
        private HttpClientInterface $footballDataClient,
    ) {
    }

    public function getCompetition(string $code): ApiCompetitionDTO
    {
        $data = $this->request(\sprintf('competitions/%s', $code));

        return ApiCompetitionDTO::fromArray($data);
    }

    /** @return list<ApiTeamDTO> */
    public function getTeams(string $code): array
    {
        $data = $this->request(\sprintf('competitions/%s/teams', $code));

        return array_map(
            static fn (array $team) => ApiTeamDTO::fromArray($team),
            $data['teams'] ?? [],
        );
    }

    /** @return list<ApiMatchDTO> */
    public function getMatches(string $code): array
    {
        $data = $this->request(\sprintf('competitions/%s/matches', $code));

        return array_map(
            static fn (array $match) => ApiMatchDTO::fromArray($match),
            $data['matches'] ?? [],
        );
    }

    private function request(string $endpoint): array
    {
        try {
            $response = $this->footballDataClient->request('GET', $endpoint);
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 400) {
                throw FootballDataApiException::requestFailed($endpoint, $statusCode);
            }

            return $response->toArray();
        } catch (FootballDataApiException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw FootballDataApiException::connectionError($endpoint, $e->getMessage());
        }
    }
}
