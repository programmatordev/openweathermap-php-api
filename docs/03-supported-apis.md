# Supported APIs

- [APIs](#apis)
  - [One Call](#one-call)
    - [getWeather](#getweatherfloat-latitude-float-longitude)
    - [getHistoryMoment](#gethistorymomentfloat-latitude-float-longitude-datetimeinterface-datetime)
    - [getHistoryAggregate](#gethistoryaggregatefloat-latitude-float-longitude-datetimeinterface-date)
  - [Weather](#weather)
    - [getCurrent](#getcurrentfloat-latitude-float-longitude)
    - [getForecast](#getforecastfloat-latitude-float-longitude-int-numresults--40)
  - [Air Pollution](#air-pollution)
    - [getCurrent](#getcurrentfloat-latitude-float-longitude-1)
    - [getForecast](#getforecastfloat-latitude-float-longitude)
    - [getHistory](#gethistoryfloat-latitude-float-longitude-datetimeinterface-startdate-datetimeinterface-enddate)
  - [Geocoding](#geocoding)
    - [getByLocationName](#getbylocationnamestring-locationname-int-numresults--5)
    - [getByZipCode](#getbyzipcodestring-zipcode-string-countrycode)
    - [getByCoordinate](#getbycoordinatefloat-latitude-float-longitude-int-numresults--5)
- [Common Methods](#common-methods)
  - [withUnitSystem](#withunitsystemstring-unitsystem)
  - [withLanguage](#withlanguagestring-language)
  - [withCacheTtl](#withcachettlint-time)

## APIs

### One Call

#### `getWeather(float $latitude, float $longitude)`

Get current and forecast (minutely, hourly and daily) weather data.

Returns a [`OneCall`](05-objects.md#onecall) object.

```php
$weather = $openWeatherMap->getOneCall()->getWeather(50, 50);

echo $weather->getCurrent()->getTemperature();
```

#### `getHistoryMoment(float $latitude, float $longitude, \DateTimeInterface $dateTime)`

Get weather data from a single moment in the past.

Returns a [`WeatherLocation`](05-objects.md#weatherlocation) object.

```php
$weather = $openWeatherMap->getOneCall()->getHistoryMoment(50, 50, new \DateTime('2023-01-01 12:00:00'));

echo $weather->getTemperature();
```

#### `getHistoryAggregate(float $latitude, float $longitude, \DateTimeInterface $date)`

Get aggregated weather data from a single day in the past.

Returns a [`WeatherAggregate`](05-objects.md#weatheraggregate) object.

```php
$weather = $openWeatherMap->getOneCall()->getHistoryAggregate(50, 50, new \DateTime('1985-07-19'));

echo $weather->getTemperature();
```

### Weather

#### `getCurrent(float $latitude, float $longitude)`

Get current weather data.

Returns a [`WeatherLocation`](05-objects.md#weatherlocation-1) object.

```php
$weather = $openWeatherMap->getWeather()->getCurrent(50, 50);

echo $weather->getTemperature();
```

#### `getForecast(float $latitude, float $longitude, int $numResults = 40)`

Get weather forecast data per 3-hour steps for the next 5 days.

Returns a [`WeatherLocationList`](05-objects.md#weatherlocationlist) object.

```php
// Since it returns 3-hour steps,
// passing 8 as the numResults means it will return results for the next 24 hours
$weatherForecast = $openWeatherMap->getWeather()->getForecast(50, 50, 8);

foreach ($weatherForecast->getList() as $weather) {
    echo $weather->getDateTime()->format('Y-m-d H:i:s');
    echo $weather->getTemperature();
}
```

### Air Pollution

#### `getCurrent(float $latitude, float $longitude)`

Get current air pollution data.

Returns a [`AirPollutionLocation`](05-objects.md#airpollutionlocation) object.

```php
$airPollution = $openWeatherMap->getAirPollution()->getCurrent(50, 50);

echo $airPollution->getAirQuality()->getQualitativeName();
echo $airPollution->getCarbonMonoxide();
```

#### `getForecast(float $latitude, float $longitude)`

Get air pollution forecast data per 1-hour for the next 24 hours.

Returns a [`AirPollutionLocationList`](05-objects.md#airpollutionlocationlist) object.

```php
$airPollutionForecast = $openWeatherMap->getAirPollution()->getForecast(50, 50);

foreach ($airPollutionForecast->getList() as $airPollution) {
    echo $airPollution->getDateTime()->format('Y-m-d H:i:s');
    echo $airPollution->getAirQuality()->getQualitativeName();
    echo $airPollution->getCarbonMonoxide();
}
```

#### `getHistory(float $latitude, float $longitude, \DateTimeInterface $startDate, \DateTimeInterface $endDate)`

Get air pollution history data between two dates.

Returns a [`AirPollutionLocationList`](05-objects.md#airpollutionlocationlist) object.

```php
$startDate = new \DateTime('-7 days'); // 7 days ago
$endDate = new \DateTime('-6 days'); // 6 days ago
$airPollutionHistory = $openWeatherMap->getAirPollution()->getHistory(50, 50, $startDate, $endDate);

foreach ($airPollutionHistory->getList() as $airPollution) {
    echo $airPollution->getDateTime()->format('Y-m-d H:i:s');
    echo $airPollution->getAirQuality()->getQualitativeName();
    echo $airPollution->getCarbonMonoxide();
}
```

### Geocoding

#### `getByLocationName(string $locationName, int $numResults = 5)`

Get locations data by location name.

Returns an array of [`Location`](05-objects.md#location) objects.

```php
$locations = $openWeatherMap->getGeocoding()->getByLocationName('lisbon');

foreach ($locations as $location) {
    echo $location->getName();
    echo $location->getCountryCode();
}
```

#### `getByZipCode(string $zipCode, string $countryCode)`

Get location data by zip/post code.

Returns a [`ZipCodeLocation`](05-objects.md#zipcodelocation) object.

```php
$location = $openWeatherMap->getGeocoding()->getByZipCode('1000-001', 'pt');

echo $location->getName();
```

#### `getByCoordinate(float $latitude, float $longitude, int $numResults = 5)`

Get locations data by coordinate.

Returns an array of [`Location`](05-objects.md#location) objects.

```php
$locations = $openWeatherMap->getGeocoding()->getByCoordinate(50, 50);

foreach ($locations as $location) {
    echo $location->getName();
    echo $location->getCountryCode();
}
```

## Common Methods

#### `withUnitSystem(string $unitSystem)`

Makes a request with a different unit system from the one globally defined in the [configuration](02-configuration.md#unitsystem).

Only available for [`OneCall`](#one-call) and [`Weather`](#weather) APIs.

```php
use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;

// Uses 'imperial' unit system for this request alone
$openWeatherMap->getWeather()
    ->withUnitSystem(UnitSystem::IMPERIAL)
    ->getCurrent(50, 50);
```

#### `withLanguage(string $language)`

Makes a request with a different language from the one globally defined in the [configuration](02-configuration.md#language).

Only available for [`OneCall`](#one-call) and [`Weather`](#weather) APIs.

```php
use ProgrammatorDev\OpenWeatherMap\Language\Language

// Uses 'pt' language for this request alone
$openWeatherMap->getWeather()
    ->withLanguage(Language::PORTUGUESE)
    ->getCurrent(50, 50);
```

#### `withCacheTtl(?int $time)`

Makes a request and saves into cache with the provided time duration value (in seconds). 
Check the [Cache TTL](02-configuration.md#cache-ttl) section for more information regarding default values.

Available for all APIs if `cache` is enabled in the [configuration](02-configuration.md#cache).

```php
use ProgrammatorDev\OpenWeatherMap\Language\Language

// Cache will be saved for 1 hour for this request alone
$openWeatherMap->getWeather()
    ->withCacheTtl(3600)
    ->getCurrent(50, 50);
```