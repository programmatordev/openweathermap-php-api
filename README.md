# OpenWeatherMap PHP API

[![Latest Version](https://img.shields.io/github/release/programmatordev/openweathermap-php-api.svg?style=flat-square)](https://github.com/programmatordev/openweathermap-php-api/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Tests](https://github.com/programmatordev/openweathermap-php-api/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/programmatordev/openweathermap-php-api/actions/workflows/ci.yml?query=branch%3Amain)

A fluent PHP client for OpenWeather APIs covering current and forecast weather,
air pollution, geocoding, maps, stations, and One Call. Responses are mapped to
typed entities that safely handle conditional, missing, and `null` data while
keeping common requests concise.

The library is built on
[`programmatordev/php-api-sdk`](https://github.com/programmatordev/php-api-sdk)
and supports client-wide and request-local configuration.

## Requirements

- PHP 8.1 or higher
- An OpenWeather API key

## Installation

Install the library with Composer:

```bash
composer require programmatordev/openweathermap-php-api
```

## Getting Started

Create the API client with an OpenWeather API key, then choose an API and call
one of its methods:

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->weather()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);

echo $current->temperature();
echo $current->temperatureWithUnit();
```

Response properties may be missing or explicitly `null`, so entity getters
return nullable values where appropriate. Collection getters return empty
arrays when the corresponding response collection is absent or `null`.

## Configuration

The client defaults to metric units and English. The equivalent explicit
configuration is:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap(
    apiKey: $_ENV['OPENWEATHERMAP_API_KEY'],
    options: [
        'units' => Units::METRIC,
        'language' => Language::ENGLISH,
    ],
);
```

Weather and One Call requests can override those values for one fluent request
chain. The client-wide configuration remains unchanged for later requests:

```php
$current = $api
    ->weather()
    ->withUnits(Units::IMPERIAL)
    ->withLanguage(Language::PORTUGUESE)
    ->current(latitude: 38.7223, longitude: -9.1393);
```

`withLanguage()` also accepts a non-empty language-code string, allowing new
OpenWeather languages to be used without waiting for an enum update.

See OpenWeather's
[units of measurement](https://openweathermap.org/api/current?collection=current_forecast#data) and
[multilingual support](https://openweathermap.org/api/current?collection=current_forecast#multi)
documentation for the currently supported values.

## Documentation

See [Setup](docs/setup.md) to configure a custom HTTP client, cache, logger,
plugins, or request hooks. The API guides cover endpoints, response entities,
and usage examples:

- [One Call 4.0](docs/one-call.md)
- [Air Pollution](docs/air-pollution.md)
- [Weather](docs/weather.md)
- [Maps](docs/maps.md)
- [Stations](docs/stations.md)
- [Geocoding](docs/geocoding.md)

## License

This project is licensed under the [MIT License](LICENSE).
