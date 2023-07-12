<?php

namespace ProgrammatorDev\OpenWeatherMap\HttpClient\Listener;

use Http\Client\Common\Plugin\Cache\Listener\CacheListener;
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
        // If it is a cache hit
        if ($fromCache) {
            $this->logger->info(
                $this->formatMessage($request, 'Cache hit'), [
                    'key' => $cacheItem->getKey()
                ]
            );
        }
        // If it is using a cached response
        else if ($cacheItem !== null) {
            $this->logger->info(
                $this->formatMessage($request, 'Response was cached'), [
                    'expires' => $cacheItem->get()['expiresAt'],
                    'key' => $cacheItem->getKey()
                ]
            );
        }
        // If request was not cached (invalid method, etc.)
        else {
            $this->logger->info(
                $this->formatMessage($request, 'Request not cachable')
            );
        }

        return $response;
    }

    private function formatMessage(RequestInterface $request, string $message): string
    {
        return \sprintf(
            '%s: %s %s %s',
            $message,
            $request->getMethod(),
            $request->getUri(),
            $request->getProtocolVersion()
        );
    }
}