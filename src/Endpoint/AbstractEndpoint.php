<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithCacheInvalidationTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithCacheTtlTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Error;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\HttpClient\Plugin\CachePlugin;
use ProgrammatorDev\OpenWeatherMap\HttpClient\ResponseMediator;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;

class AbstractEndpoint
{
    use WithCacheTtlTrait;
    use WithCacheInvalidationTrait;

    private Config $config;

    private HttpClientBuilder $httpClientBuilder;

    private ?CacheItemPoolInterface $cache;

    private ?LoggerInterface $logger;

    protected string $measurementSystem;

    protected string $language;

    protected ?int $cacheTtl = 60 * 10; // 10 minutes

    private bool $cacheInvalidation = false;

    public function __construct(protected OpenWeatherMap $api)
    {
        $this->config = $this->api->getConfig();

        $this->httpClientBuilder = $this->config->getHttpClientBuilder();
        $this->cache = $this->config->getCache();
        $this->logger = $this->config->getLogger();
        $this->measurementSystem = $this->config->getMeasurementSystem();
        $this->language = $this->config->getLanguage();
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
        UriInterface|string $baseUrl,
        array $query = [],
        array $headers = [],
        StreamInterface|string $body = null
    ): array
    {
        if ($this->cache !== null) {
            /** @var CachePlugin $cachePlugin */
            $cachePlugin =  $this->httpClientBuilder->getPlugin(CachePlugin::class);

            $cachePlugin->setCacheTtl($this->cacheTtl);
            $cachePlugin->setCacheInvalidation($this->cacheInvalidation);
        }

        $uri = $this->buildUrl($baseUrl, $query);
        $response = $this->httpClientBuilder->getHttpClient()->send($method, $uri, $headers, $body);

        if (($statusCode = $response->getStatusCode()) >= 400) {
            $this->handleResponseErrors($response, $statusCode);
        }

        return ResponseMediator::toArray($response);
    }

    /**
     * @throws NotFoundException
     * @throws UnexpectedErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    private function handleResponseErrors(ResponseInterface $response, int $statusCode): void
    {
        $error = new Error(
            ResponseMediator::toArray($response)
        );

        match ($statusCode) {
            400 => throw new BadRequestException($error),
            401 => throw new UnauthorizedException($error),
            404 => throw new NotFoundException($error),
            429 => throw new TooManyRequestsException($error),
            default => throw new UnexpectedErrorException($error)
        };
    }

    private function buildUrl(UriInterface|string $baseUrl, array $query): string
    {
        if ($baseUrl instanceof UriInterface) {
            $baseUrl = (string) $baseUrl;
        }

        // Add application key to all requests
        $query = $query + [
            'appid' => $this->api->getConfig()->getApplicationKey()
        ];

        return \sprintf('%s?%s', $baseUrl, http_build_query($query));
    }
}