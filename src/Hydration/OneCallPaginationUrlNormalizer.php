<?php

namespace ProgrammatorDev\OpenWeatherMap\Hydration;

use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

final class OneCallPaginationUrlNormalizer
{
    private const HOST = 'api.openweathermap.org';

    private function __construct() {}

    public static function normalize(
        string $url,
        string $entity,
        string $path,
    ): string {
        $parts = parse_url($url);

        if (
            !is_array($parts)
            || !isset($parts['host'], $parts['path'])
            || strtolower($parts['host']) !== self::HOST
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

            if (strtolower($name) !== OpenWeatherMap::AUTHENTICATION_KEY) {
                $parameters[] = $parameter;
            }
        }

        return implode('&', $parameters);
    }

    private static function invalidUrl(string $entity, string $path): HydrationException
    {
        // Pagination URLs may contain credentials,
        // so invalid values are never copied into exception messages.
        return HydrationException::invalidValue(
            $entity,
            $path,
            'safe One Call pagination URL',
            '[redacted]',
        );
    }
}
