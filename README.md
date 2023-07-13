# OpenWeatherMap PHP API

OpenWeatherMap PHP library that provides convenient access to the OpenWeatherMap API. 

This library supports [PSR-18 HTTP clients](https://www.php-fig.org/psr/psr-18), [PSR-17 HTTP factories](https://www.php-fig.org/psr/psr-17), [PSR-6 caches](https://www.php-fig.org/psr/psr-6) and [PSR-3 loggers](https://www.php-fig.org/psr/psr-3).

## Requirements

- PHP 8.1 and later.

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

### applicationKey

Required for all requests. Check the [API Key](#api-key) section for more information.

### measurementSystem



### httpClientBuilder

Configure your own PSR-18 HTTP client and PSR-17 HTTP factory.

By default, and for convenience, this library makes use of the [HTTPlug's Discovery](https://github.com/php-http/discovery).
This means that it will automatically find and install a well-known PSR-18 and PSR-17 implementation for you (if one was not found on your project):
- [List of PSR-18 compatible implementations](https://packagist.org/providers/psr/http-client-implementation)
- [List of PSR-17 compatible implementations]((https://packagist.org/providers/psr/http-factory-implementation))

If you want to create your own, it should look like this:

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
> All HttpClientBuilder parameters are optional. 
> If you only pass an HTTP Client, an HTTP Factory will still be discovered for you.

[HTTPlug's plugin system](https://docs.php-http.org/en/latest/plugins/index.html) is also implemented to give you full control of what happens during the request/response workflow.

In the example below, the [RetryPlugin](https://docs.php-http.org/en/latest/plugins/retry.html) was added:

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
> This library already uses HTTPlug's CachePlugin and LoggerPlugin.
> Re-adding those may lead to an unexpected behaviour.