<?php

namespace ProgrammatorDev\OpenWeatherMap\Hydration\OneCall;

use Http\Discovery\Psr17FactoryDiscovery;

final class PaginationUrlNormalizer
{
    private function __construct() {}

    public static function normalize(string $url): string
    {
        $uri = Psr17FactoryDiscovery::findUriFactory()->createUri($url);

        // Keep relative references relative so the resolver can apply the configured base URL;
        // absolute references are upgraded to HTTPS without changing their query.
        return (string) ($uri->getHost() === '' ? $uri : $uri->withScheme('https'));
    }
}
