# OpenWeatherMap PHP API

OpenWeatherMap PHP library that provides convenient access to the OpenWeatherMap API. 

Supports [PSR-18 HTTP clients](https://www.php-fig.org/psr/psr-18), [PSR-17 HTTP factories](https://www.php-fig.org/psr/psr-17), [PSR-6 caches](https://www.php-fig.org/psr/psr-6) and [PSR-3 logs](https://www.php-fig.org/psr/psr-3).

## Installation

You can install the library via [Composer](https://getcomposer.org/). Run the following command:

```bash
$ composer require programmatordev/openweathermap-php-api
```

## Basic Usage

Simple usage looks like:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

// Init
$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey'
    ])
);

// Get current weather by coordinate (latitude, longitude)
$currentWeather = $openWeatherMap->getWeather()->getCurrent(50, 50);
// Show current temperature
echo $currentWeather->getTemperature();
```

## Documentation

- [Usage Instructions](docs/01-usage-instructions.md)
- [Configuration](docs/02-configuration.md)
- ...

## Contribution