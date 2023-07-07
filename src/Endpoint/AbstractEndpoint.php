<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithCacheInvalidationTrait;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\WithCacheTtlTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Error;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\ResponseMediator;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class AbstractEndpoint
{
    use WithCacheTtlTrait;
    use WithCacheInvalidationTrait;

    private HttpMethodsClient $httpClient;

    private ?CacheItemPoolInterface $cache;

    private bool $cacheInvalidation = false;

    protected \DateInterval|int|null $cacheTtl = 60 * 10; // 10 minutes

    protected string $measurementSystem;

    protected string $language;

    public function __construct(protected OpenWeatherMap $api)
    {
        $config = $this->api->getConfig();

        $this->httpClient = $config->getHttpClientBuilder()->getHttpClient();
        $this->cache = $config->getCache();
        $this->measurementSystem = $config->getMeasurementSystem();
        $this->language = $config->getLanguage();
    }

    /**
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws InvalidArgumentException
     */
    protected function sendRequest(
        string $method,
        UriInterface|string $baseUrl,
        array $query = [],
        array $headers = [],
        StreamInterface|string $body = null
    ): array
    {
        $uri = $this->buildUrl($baseUrl, $query);

        // If there is a cache adapter, save responses into cache
        if ($this->cache !== null) {
            $cacheKey = $this->getCacheKey($uri);

            // Invalidate cache (if exists) to force renewal
            if ($this->cacheInvalidation === true) {
                $this->cache->deleteItem($cacheKey);
            }

            $cacheItem = $this->cache->getItem($cacheKey);

            // If cache does not exist...
            if (!$cacheItem->isHit()) {
                $response = ResponseMediator::toArray(
                    $this->handleRequest($method, $uri, $headers, $body)
                );

                $cacheItem->set($response);
                $cacheItem->expiresAfter($this->cacheTtl);

                $this->cache->save($cacheItem);
            }

            return $cacheItem->get();
        }

        return ResponseMediator::toArray(
            $this->handleRequest($method, $uri, $headers, $body)
        );
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     * @throws UnexpectedErrorException
     * @throws TooManyRequestsException
     * @throws BadRequestException
     * @throws UnauthorizedException
     */
    private function handleRequest(
        string $method,
        string $uri,
        array $headers,
        StreamInterface|string $body = null
    ): ResponseInterface
    {
        $response = $this->httpClient->send($method, $uri, $headers, $body);
        $statusCode = $response->getStatusCode();

        // If API returns an error, throw exception
        if ($statusCode >= 400) {
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

        return $response;
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

    private function getCacheKey(string $value): string
    {
        return md5($value);
    }
}