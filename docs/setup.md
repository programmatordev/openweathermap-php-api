# Setup

`OpenWeatherMap` uses PHP API SDK's `setup()` method for client-wide HTTP
configuration. Most applications can rely on PHP-HTTP discovery and use the
client without additional setup. Use the options below when the application
needs its own HTTP services or request behavior.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);
```

The examples below use objects supplied by the application:
`$httpClient` implements PSR-18, `$cachePool` implements PSR-6, and `$logger`
implements PSR-3.

Setup changes apply to subsequent requests made by the client. They do not
affect methods such as `maps()->tileUrl()` and `maps()->tileUrlTemplate()`,
which generate URLs without making an HTTP request.

## HTTP Client

PHP-HTTP discovery selects a compatible PSR-18 client and PSR-17 factories by
default. Packagist lists available
[PSR-18 client implementations](https://packagist.org/providers/psr/http-client-implementation)
and [PSR-17 factory implementations](https://packagist.org/providers/psr/http-factory-implementation).
Use `client()` to provide a specific PSR-18 client instead.

```php
$api->setup()->client($httpClient);
```

`client()` returns a builder that can also receive custom request and stream
factories.

See the official PHP API SDK
[HTTP client documentation](https://github.com/programmatordev/php-api-sdk/blob/main/docs/09-http-client.md)
for all client and factory options.

## Cache

Use `cache()` with a PSR-6 cache pool to cache supported HTTP responses. The
default TTL sets the cache lifetime when a response does not provide one. GET
and HEAD requests are cacheable by default. Packagist lists
available [PSR-6 cache implementations](https://packagist.org/providers/psr/cache-implementation).

```php
$api
    ->setup()
    ->cache($cachePool)
    ->defaultTtl(300);
```

Cache configuration is client-wide. After configuring a pool, `withCache()` can
change cache behavior for one request chain without changing the client-wide
settings.

```php
use ProgrammatorDev\Api\Builder\CacheBuilder;

$current = $api
    ->weather()
    ->withCache(fn (CacheBuilder $cache) => $cache->defaultTtl(60))
    ->current(latitude: 38.7223, longitude: -9.1393);
```

See the official PHP API SDK
[cache documentation](https://github.com/programmatordev/php-api-sdk/blob/main/docs/10-cache.md)
for all global and request-local options.

## Logging

Use `logger()` with a PSR-3 logger. Packagist lists available
[PSR-3 logger implementations](https://packagist.org/providers/psr/log-implementation).

```php
$api->setup()->logger($logger);
```

As with other HTTP logging, ensure the application's logging configuration does
not persist credentials or other sensitive request data.

See the official PHP API SDK
[logging documentation](https://github.com/programmatordev/php-api-sdk/blob/main/docs/11-logging.md)
for formatter and cache-logging details.

## Plugins

Use `plugins()` to add HTTPlug middleware. For example, a retry plugin can
retry failed requests or retryable responses.

```php
use Http\Client\Common\Plugin\RetryPlugin;

$retryPlugin = new RetryPlugin([
    'retries' => 2,
]);

$api->setup()->plugins()->add($retryPlugin, priority: 25);
```

Retries may send additional OpenWeather requests.
Plugin priority controls middleware order. Priority `25` places this retry
plugin after authentication and before cache; consult the linked documentation
when choosing priorities for other plugins.

See the official PHP API SDK
[plugin documentation](https://github.com/programmatordev/php-api-sdk/blob/main/docs/12-plugins.md)
for middleware ordering and priority guidance.

## Hooks

Hooks run immediately before a request is sent or after its response is
received. They can inspect the API context and optionally return a modified
PSR-7 request or response.

```php
use ProgrammatorDev\Api\Context\RequestContext;

$api->setup()->hooks()->beforeRequest(
    function (RequestContext $context) {
        $request = $context->request();

        // Modify the request here.

        return $request;
    },
);
```

Return a PSR-7 request to use it for the current request, or return `null` to
keep the original request. Response hooks follow the same pattern with a PSR-7
response.

See the official PHP API SDK
[hook documentation](https://github.com/programmatordev/php-api-sdk/blob/main/docs/13-hooks.md)
for hook return values, context, ordering, and priorities.

## Complete Setup Reference

See the complete
[PHP API SDK documentation](https://github.com/programmatordev/php-api-sdk/tree/main/docs)
for every setup method, builder, and extension point.
