<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\CacheTtlTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Error;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\HttpClient\Listener\LoggerCacheListener;
use ProgrammatorDev\OpenWeatherMap\HttpClient\ResponseMediator;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

class AbstractEndpoint
{
    use CacheTtlTrait;

    private Config $config;

    private HttpClientBuilder $httpClientBuilder;

    private ?CacheItemPoolInterface $cache;

    private ?LoggerInterface $logger;

    protected string $unitSystem;

    protected string $language;

    protected int $cacheTtl = 60 * 10; // 10 minutes

    public function __construct(protected OpenWeatherMap $api)
    {
        $this->config = $this->api->config();

        $this->httpClientBuilder = $this->config->getHttpClientBuilder();
        $this->cache = $this->config->getCache();
        $this->logger = $this->config->getLogger();
        $this->unitSystem = $this->config->getUnitSystem();
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
        string $path,
        array $query = [],
        array $headers = [],
        StreamInterface|string $body = null
    ): array
    {
        $this->configurePlugins();

        $response = $this->httpClientBuilder->getHttpClient()->send(
            $method,
            $this->buildUrl($path, $query),
            $headers,
            $body
        );

        if ($response->getStatusCode() >= 400) {
            $this->handleResponseErrors($response);
        }

        return ResponseMediator::toArray($response);
    }

    private function configurePlugins(): void
    {
        // Plugin order is important
        // CachePlugin should come first, otherwise the LoggerPlugin will log requests even if they are cached

        if ($this->cache !== null) {
            $this->httpClientBuilder->addPlugin(
                new CachePlugin($this->cache, $this->httpClientBuilder->getStreamFactory(), [
                    'default_ttl' => $this->cacheTtl,
                    'cache_lifetime' => 0,
                    'cache_listeners' => ($this->logger !== null) ? [new LoggerCacheListener($this->logger)] : []
                ])
            );
        }

        if ($this->logger !== null) {
            $this->httpClientBuilder->addPlugin(
                new LoggerPlugin($this->logger)
            );
        }
    }

    /**
     * @throws NotFoundException
     * @throws UnexpectedErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    private function handleResponseErrors(ResponseInterface $response): void
    {
        $error = new Error(
            ResponseMediator::toArray($response)
        );

        match ($response->getStatusCode()) {
            400 => throw new BadRequestException($error),
            401 => throw new UnauthorizedException($error),
            404 => throw new NotFoundException($error),
            429 => throw new TooManyRequestsException($error),
            default => throw new UnexpectedErrorException($error)
        };
    }

    private function buildUrl(string $path, array $query): string
    {
        // Add application key to all requests
        $query = $query + [
            'appid' => $this->config->getApplicationKey()
        ];

        return \sprintf('%s%s?%s', $this->config->getBaseUrl(), $path, http_build_query($query));
    }
}