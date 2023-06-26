<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\HttpClient\ResponseMediator;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class AbstractEndpoint
{
    protected string $measurementSystem;

    protected string $language;

    public function __construct(protected OpenWeatherMap $api)
    {
        $this->measurementSystem = $this->api->getConfig()->getMeasurementSystem();
        $this->language = $this->api->getConfig()->getLanguage();
    }

    /**
     * @throws Exception
     */
    protected function sendRequest(
        string $method,
        string|UriInterface $baseUrl,
        array $query = [],
        array $headers = [],
        string|StreamInterface $body = null
    ): array
    {
        if ($baseUrl instanceof UriInterface) {
            $baseUrl = (string) $baseUrl;
        }

        $uri = $this->buildUrl($baseUrl, $query);

        return ResponseMediator::toArray(
            $this->getHttpClient()->send($method, $uri, $headers, $body)
        );
    }

    private function getHttpClient(): HttpMethodsClient
    {
        return $this->api->getConfig()
            ->getHttpClientBuilder()
            ->getHttpClient();
    }

    private function buildUrl(string $baseUrl, array $query): string
    {
        // Add application key to all requests
        $query = $query + [
            'appid' => $this->api->getConfig()->getApplicationKey()
        ];

        return \sprintf('%s?%s', $baseUrl, http_build_query($query));
    }
}