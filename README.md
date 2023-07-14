# OpenWeatherMap PHP API

OpenWeatherMap PHP library that provides convenient access to the OpenWeatherMap API. 

This library supports [PSR-18 HTTP clients](https://www.php-fig.org/psr/psr-18), [PSR-17 HTTP factories](https://www.php-fig.org/psr/psr-17), [PSR-6 caches](https://www.php-fig.org/psr/psr-6) and [PSR-3 loggers](https://www.php-fig.org/psr/psr-3).

## Requirements

PHP 8.1 and later.

## API Key

A key is required to be able to make requests to the API.
You must sign up for an [OpenWeatherMap account](https://openweathermap.org/appid#signup) to get one.

## Installation

You can install the library via [Composer](https://getcomposer.org/). Run the following command:

```bash
$ composer require programmatordev/openweathermap-php-api
```

To use the library, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once 'vendor/autoload.php';
```

## Basic Usage

Simple usage looks like:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

// Initialize
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

## Configuration

Full default configuration:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'measurementSystem' => 'metric',
        'language' => 'en',
        'httpClientBuilder' => new HttpClientBuilder(),
        'cache' => null,
        'logger' => null
    ])
);
```

### `applicationKey`

Required for all requests. Check the [API Key](#api-key) section for more information.

### `measurementSystem`

Measurement system used when retrieving data. 
Affects temperature and speed values.

Available options are `metric`, `imperial` and `standard`. 
Pre-defined [constants](src/MeasurementSystem/MeasurementSystem.php) are also available.

Example:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\MeasurementSystem\MeasurementSystem;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'measurementSystem' => MeasurementSystem::IMPERIAL
    ])
);
```

### `language`

Language used when retrieving data. 
It seems to only affect weather conditions descriptions.

List of all available languages can be found [here](https://openweathermap.org/api/one-call-3#multi). 
Pre-defined [constants](src/Language/Language.php) are also available.

Example:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Language\Language;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'language' => Language::PORTUGUESE
    ])
);
```

### `httpClientBuilder`

Configure a PSR-18 HTTP client and PSR-17 HTTP factory adapters.

By default, and for convenience, this library makes use of the [HTTPlug's Discovery](https://github.com/php-http/discovery) library.
This means that it will automatically find and install a well-known PSR-18 and PSR-17 implementation for you (if one was not found on your project):
- [List of PSR-18 compatible implementations](https://packagist.org/providers/psr/http-client-implementation)
- [List of PSR-17 compatible implementations](https://packagist.org/providers/psr/http-factory-implementation)

If you want to manually provide one, it should look like this:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$httpClient = new Symfony\Component\HttpClient\Psr18Client();
$httpFactory = new Nyholm\Psr7\Factory\Psr17Factory();

// HttpClientBuilder(
//      ?ClientInterface $client = null,
//      ?RequestFactoryInterface $requestFactory = null,
//      ?StreamFactoryInterface $streamFactory = null
// );
$httpClientBuilder = new HttpClientBuilder($httpClient, $httpFactory, $httpFactory);

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'httpClientBuilder' => $httpClientBuilder
    ])
);
```

> **Note**
> All `HttpClientBuilder` parameters are optional. 
> If you only pass an HTTP client, an HTTP factory will still be discovered for you.

#### Plugin System

[HTTPlug's plugin system](https://docs.php-http.org/en/latest/plugins/index.html) is also implemented to give you full control of what happens during the request/response workflow.

In the example below, the [RetryPlugin](https://docs.php-http.org/en/latest/plugins/retry.html) is added:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$httpClientBuilder = new HttpClientBuilder();

// Plugin that will attempt to re-send a request in case of failure
// (service temporarily down because of unreliable connections/servers, etc.)
$httpClientBuilder->addPlugin(
    new \Http\Client\Common\Plugin\RetryPlugin([
        'retries' => 3
    ])
);

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'httpClientBuilder' => $httpClientBuilder
    ])
);
```

You can check their [plugin list](https://docs.php-http.org/en/latest/plugins/index.html) or [create your own](https://docs.php-http.org/en/latest/plugins/build-your-own.html).

> **Note**
> This library already uses HTTPlug's `CachePlugin` and `LoggerPlugin`.
> Re-adding those may lead to an unexpected behaviour.

### `cache`

Configure a PSR-6 cache adapter.

By default, no responses are cached. 
To enable cache, you must provide a PSR-6 implementation:
- [List of PSR-6 compatible implementations](https://packagist.org/providers/psr/cache-implementation)

In the example below, a filesystem-based cache is used:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'cache' => $cache
    ])
);
```

#### Cache Duration

By default, all responses are cached for `10 minutes`, with the exception to `Geocoding` requests 
where responses are cached for `30 days` (due to the low update frequency, location data doesn't change that often).

It is possible to change the cache duration per request:

```php
// Response will be cached for 1 hour
$currentWeather = $openWeatherMap->getWeather()
    ->withCacheTtl(3600)
    ->getCurrent(50, 50);
```

### `logger`

Configure a PSR-3 logger adapter.

By default, no logs are created. To enable logs, you must provide a PSR-3 implementation:
- [List of PSR-3 compatible implementations](https://packagist.org/providers/psr/log-implementation)

In the example below, a file-based logger is used...

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$logger = new \Monolog\Logger('openweathermap');
$logger->pushHandler(
    new \Monolog\Handler\StreamHandler(__DIR__ . '/logs/openweathermap.log')
);

$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey',
        'logger' => $logger
    ])
);
```

...and will provide logs similar to this:

```
[2023-07-12T12:25:02.235721+00:00] openweathermap.INFO: Sending request: GET https://api.openweathermap.org/data/3.0/onecall?lat=50&lon=50&units=metric&lang=en&appid=[REDACTED] 1.1 {"request":{},"uid":"64ae9b9e394ff6.24668056"}
[2023-07-12T12:25:02.682278+00:00] openweathermap.INFO: Received response: 200 OK 1.1 {"milliseconds":447,"uid":"64ae9b9e394ff6.24668056"}
```

> **Note**
> If a `cache` implementation is configured, cache events will also be logged.