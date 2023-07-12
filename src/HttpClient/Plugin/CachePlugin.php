<?php

namespace ProgrammatorDev\OpenWeatherMap\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Cache\Generator\SimpleGenerator;
use Http\Promise\Promise;
use ProgrammatorDev\OpenWeatherMap\HttpClient\Plugin\Listener\LoggerCacheListener;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

class CachePlugin implements Plugin
{
    private ?int $cacheTtl = 60 * 10;

    private bool $cacheInvalidation = false;

    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ?LoggerInterface $logger = null
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $plugin = new Plugin\CachePlugin($this->cache, $this->streamFactory, [
            'default_ttl' => $this->cacheTtl,
            'cache_lifetime' => 0,
            'cache_listeners' => ($this->logger !== null)
                ? [new LoggerCacheListener($this->logger)]
                : []
        ]);

        if ($this->cacheInvalidation === true) {
            $cacheKey = $this->createCacheKey($request);

            $this->cache->deleteItem($cacheKey);

            $this->logger?->info(\sprintf('Cache invalidated: %s %s', $request->getMethod(), $request->getUri()), [
                'key' => $cacheKey
            ]);
        }

        return $plugin->handleRequest($request, $next, $first);
    }

    public function setCacheTtl(?int $cacheTtl): self
    {
        $this->cacheTtl = $cacheTtl;

        return $this;
    }

    private function createCacheKey(RequestInterface $request): string
    {
        $cacheKeyGenerator = new SimpleGenerator();

        return hash('sha1', $cacheKeyGenerator->generate($request));
    }
}