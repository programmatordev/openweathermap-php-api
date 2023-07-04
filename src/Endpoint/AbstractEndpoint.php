<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\Error;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiError\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\ResponseMediator;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Http\Message\ResponseInterface;
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
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
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
        $response = $this->getHttpClient()->send($method, $uri, $headers, $body);

        $this->handleApiError($response);

        return ResponseMediator::toArray($response);
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

    /**
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws TooManyRequestsException
     * @throws NotFoundException
     */
    private function handleApiError(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            $data = ResponseMediator::toArray($response);
            $error = new Error($data);

            match ($statusCode) {
                400 => throw new BadRequestException($error->getMessage(), $error->getCode(), $error->getParameters()),
                401 => throw new UnauthorizedException($error->getMessage(), $error->getCode(), $error->getParameters()),
                404 => throw new NotFoundException($error->getMessage(), $error->getCode(), $error->getParameters()),
                429 => throw new TooManyRequestsException($error->getMessage(), $error->getCode(), $error->getParameters()),
                default => throw new UnexpectedErrorException($error->getMessage(), $error->getCode(), $error->getParameters())
            };
        }
    }
}