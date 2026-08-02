<?php

namespace ProgrammatorDev\OpenWeatherMap\Hydration;

use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;

final class OneCallPaginationUrlNormalizer
{
    private const HOST = 'api.openweathermap.org';

    private function __construct() {}

    public static function normalize(
        string $url,
        string $entity,
        string $path,
        string $expectedPath,
    ): string {
        $parts = parse_url($url);

        if (
            !is_array($parts)
            || !isset($parts['scheme'], $parts['host'], $parts['path'])
            || !in_array(strtolower($parts['scheme']), ['http', 'https'], true)
            || strtolower($parts['host']) !== self::HOST
            || $parts['path'] !== $expectedPath
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])
            || isset($parts['fragment'])
        ) {
            throw self::invalidUrl($entity, $path);
        }

        $query = self::withoutApiKey($parts['query'] ?? '');

        return sprintf(
            'https://%s%s%s',
            self::HOST,
            $parts['path'],
            $query === '' ? '' : sprintf('?%s', $query),
        );
    }

    private static function withoutApiKey(string $query): string
    {
        $parameters = [];

        foreach (explode('&', $query) as $parameter) {
            if ($parameter === '') {
                continue;
            }

            $name = rawurldecode(explode('=', $parameter, 2)[0]);

            if (strtolower($name) !== 'appid') {
                $parameters[] = $parameter;
            }
        }

        return implode('&', $parameters);
    }

    private static function invalidUrl(string $entity, string $path): HydrationException
    {
        // Pagination URLs may contain credentials, so invalid values are never
        // copied into exception messages.
        return HydrationException::invalidValue(
            $entity,
            $path,
            'safe One Call pagination URL',
            '[redacted]',
        );
    }
}
