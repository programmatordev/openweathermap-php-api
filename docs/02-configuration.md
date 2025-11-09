# Configuration

- [Default Configuration](#default-configuration)
- [Options](#options)
  - [unitSystem](#unitsystem)
  - [language](#language)
- [Methods](#methods)
  - [setClientBuilder](#setclientbuilder)
  - [setCacheBuilder](#setcachebuilder)
  - [setLoggerBuilder](#setloggerbuilder)

## Default Configuration

```php
OpenWeatherMap(string $apiKey, array $options => []);
```

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap('yourapikey', [
    'unitSystem' => 'metric',
    'language' => 'en'
]);
```

## Options

### `unitSystem`

Unit system used when retrieving data.
Affects temperature and speed values.

Available options:
- `metric`
- `imperial`
- `standard`

Example:

```php
use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap('yourapikey', [
    'unitSystem' => UnitSystem::IMPERIAL
]);
```

### `language`

Language used when retrieving data.
It seems to only affect weather conditions descriptions.

List of all available languages can be found [here](https://openweathermap.org/api/one-call-3#multi).

Example:

```php
use ProgrammatorDev\OpenWeatherMap\Language\Language;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap('yourapikey', [
    'language' => Language::PORTUGUESE
]);
```

## Methods

> [!IMPORTANT]
> The [PHP API SDK](https://github.com/programmatordev/php-api-sdk) library was used to create the OpenWeatherMap PHP API.
> To get to know about all the available methods, make sure to check the documentation [here](https://github.com/programmatordev/php-api-sdk?tab=readme-ov-file#documentation).

The following sections have examples of some of the most important methods, 
particularly related to the configuration of the client, cache and logger.

### `setClientBuilder`

By default, this library makes use of the [HTTPlug's Discovery](https://github.com/php-http/discovery) library.
This means that it will automatically find and install a well-known PSR-18 client and PSR-17 factory implementation for you
(if they were not found on your project):
- [PSR-18 compatible implementations](https://packagist.org/providers/psr/http-client-implementation)
- [PSR-17 compatible implementations](https://packagist.org/providers/psr/http-factory-implementation)

If you don't want to rely on the discovery of implementations, you can set the ones you want:

```php
use Nyholm\Psr7\Factory\Psr17Factory;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Symfony\Component\HttpClient\Psr18Client;

$api = new OpenWeatherMap('yourapikey');

$client = new Psr18Client();
$requestFactory = $streamFactory = new Psr17Factory();

$api->setClientBuilder(
    new ClientBuilder(
        client: $client, 
        requestFactory: $requestFactory, 
        streamFactory: $streamFactory
    )
);
```

Check the full documentation [here](https://github.com/programmatordev/php-api-sdk?tab=readme-ov-file#http-client-psr-18-and-http-factories-psr-17).

### `setCacheBuilder`

This library allows configuring the cache layer of the client for making API requests.
It uses a standard PSR-6 implementation and provides methods to fine-tune how HTTP caching behaves:
- [PSR-6 compatible implementations](https://packagist.org/providers/psr/cache-implementation)

Example:

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$api = new OpenWeatherMap('yourapikey');

$pool = new FilesystemAdapter();

// set a file-based cache adapter with a 1-hour default cache lifetime
$api->setCacheBuilder(
    new CacheBuilder(
        pool: $pool, 
        ttl: 3600
    )
);
```

Check the full documentation [here](https://github.com/programmatordev/php-api-sdk?tab=readme-ov-file#cache-psr-6).

### `setLoggerBuilder`

This library allows configuring a logger to save data for making API requests.
It uses a standard PSR-3 implementation and provides methods to fine-tune how logging behaves:
- [PSR-3 compatible implementations](https://packagist.org/providers/psr/log-implementation)

Example:

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap('yourapikey');

$logger = new Logger('api');
$logger->pushHandler(new StreamHandler('/logs/api.log'));

$api->setLoggerBuilder(
    new LoggerBuilder(
        logger: $logger
    )
);
```

Check the full documentation [here](https://github.com/programmatordev/php-api-sdk?tab=readme-ov-file#logger-psr-3).