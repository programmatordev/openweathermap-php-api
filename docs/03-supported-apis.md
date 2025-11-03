# Supported APIs

- [APIs](#apis)
  - [One Call](#one-call)
    - [getWeather](#getweather)
    - [getWeatherByDate](#getweatherbydate)
    - [getWeatherSummaryByDate](#getweathersummarybydate)
    - [getWeatherOverviewByDate](#getweatheroverviewbydate)
  - [Weather](#weather)
    - [getCurrent](#getcurrent)
    - [getForecast](#getforecast)
  - [Air Pollution](#air-pollution)
    - [getCurrent](#getcurrent-1)
    - [getForecast](#getforecast-1)
    - [getHistory](#gethistory)
  - [Geocoding](#geocoding)
    - [getByLocationName](#getbylocationname)
    - [getByCoordinate](#getbycoordinate)
    - [getByZipCode](#getbyzipcode)
- [Common Methods](#common-methods)
  - [withUnitSystem](#withunitsystem)
  - [withLanguage](#withlanguage)
  - [withCacheTtl](#withcachettl)

## APIs

### One Call

#### `getWeather`

```php
getWeather(float $latitude, float $longitude): Weather
```

Get access to current weather, minute forecast for 1 hour, hourly forecast for 48 hours, 
daily forecast for 8 days and government weather alerts.

Returns a [`Weather`](05-entities.md#weather) object:

```php
$weather = $api->oneCall()->getWeather(50, 50);
```

#### `getWeatherByDate`

```php
getWeatherByDate(float $latitude, float $longitude, \DateTimeInterface $dateTime): WeatherMoment
```

Get access to weather data for any datetime.

Returns a [`WeatherMoment`](05-entities.md#weathermoment) object:

```php
$weather = $api->oneCall()->getWeatherByDate(50, 50, new \DateTime('2023-05-13 16:32:00'));
```

#### `getWeatherSummaryByDate`

```php
getWeatherSummaryByDate(float $latitude, float $longitude, \DateTimeInterface $date): WeatherSummary
```

Get access to aggregated weather data for a particular date.

Returns a [`WeatherSummary`](05-entities.md#weathersummary) object:

```php
$weatherSummary = $api->oneCall()->getWeatherSummaryByDate(50, 50, new \DateTime('1985-07-19'));
```

#### `getWeatherOverviewByDate`

```php
getWeatherOverviewByDate(float $latitude, float $longitude, \DateTimeInterface $date): WeatherOverview
```

Get the weather overview with a human-readable summary for today and tomorrow's forecast, 
using OpenWeather AI.

Returns a [`WeatherOverview`](05-entities.md#weatheroverview) object:

```php
$weatherOverview = $api->oneCall()->getWeatherOverviewByDate(50, 50, new \DateTime('today'));
```

### Weather

#### `getCurrent`

```php
getCurrent(float $latitude, float $longitude): Weather
```

Get access to current weather data.

Returns a [`Weather`](05-entities.md#weather-2) object:

```php
$currentWeather = $api->weather()->getCurrent(50, 50);
```

#### `getForecast`

```php
getForecast(float $latitude, float $longitude, int $numResults = 40): WeatherCollection
```

Get access to 5-day weather forecast data with 3-hour steps.

Returns a [`WeatherCollection`](05-entities.md#weathercollection) object:

```php
// Since it returns data in 3-hour steps,
// passing 8 as the numResults means it will return results for the next 24 hours
$weatherForecast = $api->weather()->getForecast(50, 50, 8);
```

### Air Pollution

#### `getCurrent`

```php
getCurrent(float $latitude, float $longitude): AirPollution
```

Get access to current air pollution data.

Returns a [`AirPollution`](05-entities.md#airpollution) object:

```php
$currentAirPollution = $api->airPollution()->getCurrent(50, 50);
```

#### `getForecast`

```php
getForecast(float $latitude, float $longitude): AirPollutionCollection
```

Get access to air pollution forecast data per hour.

Returns a [`AirPollutionCollection`](05-entities.md#airpollutioncollection) object:

```php
$airPollutionForecast = $api->airPollution()->getForecast(50, 50);
```

#### `getHistory`

```php
getHistory(float $latitude, float $longitude, \DateTimeInterface $startDate, \DateTimeInterface $endDate): AirPollutionCollection
```

Get access to historical air pollution data per hour between two dates.

Returns a [`AirPollutionCollection`](05-entities.md#airpollutioncollection) object:

```php
$startDate = new \DateTime('-1 day');
$endDate = new \DateTime('now');

// returns air pollution data for the last 24 hours
$airPollutionHistory = $api->airPollution()->getHistory(50, 50, $startDate, $endDate);
```

### Geocoding

#### `getByLocationName`

```php
/**
 * @return Location[]
 */
getByLocationName(string $locationName, int $numResults = 5): array
```

Get geographical coordinates (latitude, longitude) by using the name of the location (city name or area name). 

Returns an array of [`Location`](05-entities.md#location) objects.

```php
$locations = $api->geocoding()->getByLocationName('lisbon');
```

#### `getByCoordinate`

```php
/**
 * @return Location[]
 */
getByCoordinate(float $latitude, float $longitude, int $numResults = 5): array
```

Get name of the location (city name or area name) by using geographical coordinates (latitude, longitude). 

Returns an array of [`Location`](05-entities.md#location) objects.

```php
$locations = $api->geocoding()->getByCoordinate(50, 50);
```

#### `getByZipCode`

```php
getByZipCode(string $zipCode, string $countryCode): ZipLocation
```

Get geographical coordinates (latitude, longitude) by using the zip/postal code. 

Returns a [`ZipLocation`](05-entities.md#ziplocation) object.

```php
$location = $api->geocoding()->getByZipCode('1000-001', 'pt');
```

## Common Methods

#### `withLanguage`

```php
withLanguage(string $language): self
```

Set the language per request. 
Only available for [`OneCall`](#one-call) and [`Weather`](#weather) API requests.

```php
use ProgrammatorDev\OpenWeatherMap\Language\Language

// uses the "pt" language for this request alone
$api->weather()
    ->withLanguage(Language::PORTUGUESE)
    ->getCurrent(50, 50);
```

#### `withUnitSystem`

```php
withUnitSystem(string $unitSystem): self
```

Set the unit system per request.
Only available for [`OneCall`](#one-call) and [`Weather`](#weather) API requests.

```php
use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;

// uses the "imperial" unit system for this request alone
$api->weather()
    ->withUnitSystem(UnitSystem::IMPERIAL)
    ->getCurrent(50, 50);
```

#### `withCacheTtl`

```php
withCacheTtl(?int $ttl): self
```

Makes a request and saves into cache for the provided duration in seconds. 

Semantics of values:
- `0`, the response will not be cached (if the servers specifies no `max-age`).
- `null`, the response will be cached for as long as it can (forever).

> [!NOTE]
> Setting cache to `null` or `0` seconds will **not** invalidate any existing cache.

Available for all APIs if a cache adapter is set. 
Check the following [documentation](02-configuration.md#setcachebuilder) for more information.

```php
// cache will be saved for 1 hour for this request alone
$api->weather()
    ->withCacheTtl(3600)
    ->getCurrent(50, 50);
```