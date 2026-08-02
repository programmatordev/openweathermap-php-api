<?php

namespace ProgrammatorDev\OpenWeatherMap\Hydration;

use Http\Discovery\Psr17FactoryDiscovery;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

final class OneCallPaginationUrlNormalizer
{
    private function __construct() {}

    public static function normalize(string $url): string
    {
        $uri = Psr17FactoryDiscovery::findUriFactory()->createUri($url);
        $queryParameters = [];

        parse_str($uri->getQuery(), $queryParameters);

        // Authentication is reapplied by the SDK when the pagination link is followed,
        // so the response must not retain its embedded API key.
        unset($queryParameters[OpenWeatherMap::AUTHENTICATION_KEY]);

        // Keep relative references relative so the resolver can apply the configured base URL;
        // absolute references are upgraded to HTTPS.
        if ($uri->getHost() !== '') {
            $uri = $uri->withScheme('https');
        }

        return (string) $uri->withQuery(
            http_build_query(
                $queryParameters,
                '',
                '&',
                PHP_QUERY_RFC3986,
            ),
        );
    }
}
