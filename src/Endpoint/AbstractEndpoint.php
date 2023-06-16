<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Common\HttpMethodsClient;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

class AbstractEndpoint
{
    public function __construct(protected OpenWeatherMap $api) {}

    protected function getHttpClient(): HttpMethodsClient
    {
        return $this->api->getConfig()
            ->getHttpClientBuilder()
            ->getHttpClient();
    }

    protected function createUrl(string $baseUrl, array $query): string
    {
        // Add application key to all requests
        $query = $query + [
            'appid' => $this->api->getConfig()
                ->getApplicationKey()
        ];

        return \sprintf('%s?%s', $baseUrl, http_build_query($query));
    }
}