<?php

namespace ProgrammatorDev\OpenWeatherMap\HttpClient\Plugin\Listener;

use Http\Client\Common\Plugin\Cache\Listener\CacheListener;
use Psr\Cache\CacheItemInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LoggerCacheListener implements CacheListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function onCacheResponse(RequestInterface $request, ResponseInterface $response, $fromCache, $cacheItem): ResponseInterface
    {
        if ($fromCache) {
            $this->logger->info(\sprintf('Cache hit: %s %s', $request->getMethod(), $request->getUri()), [
                'key' => $cacheItem->getKey()
            ]);
        }
        else {
            $this->logger->info('Cached response', [
                'expires' => $cacheItem->get()['expiresAt'],
                'key' => $cacheItem->getKey()
            ]);
        }

        return $response;
    }
}