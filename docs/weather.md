# Weather

## Current

The Current Weather API is available on OpenWeather's standard free and paid
subscriptions. See the
[official Current Weather API documentation](https://openweathermap.org/api/current)
for API details.

Use `current()` with a latitude and longitude.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->weather()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

`current()` returns a `Current` entity. Every property may be absent or
explicitly `null`; missing or `null` condition lists become empty arrays.
Coordinates are available through `coordinates()`.

```php
echo $current->name();
echo $current->coordinates()?->latitude();
echo $current->coordinates()?->longitude();
echo $current->temperature();
echo $current->feelsLikeTemperature();
echo $current->minimumTemperature();
echo $current->maximumTemperature();
echo $current->pressure();
echo $current->humidity();
echo $current->visibility();
```

`minimumTemperature()` and `maximumTemperature()` are the lowest and highest
temperatures currently observed within the requested location. OpenWeather
notes that they are mainly useful for geographically large cities; they are not
the day's forecast low and high.

Conditions, wind, and clouds are exposed as nested entities. A condition keeps
the raw OpenWeather icon code and provides its absolute image URL. A response
can contain multiple conditions; OpenWeather defines the first as the primary
condition.

```php
foreach ($current->conditions() as $condition) {
    echo $condition->group();
    echo $condition->description();
    echo $condition->icon();
    echo $condition->iconUrl();
}

echo $current->wind()?->speed();
echo $current->wind()?->direction();
echo $current->wind()?->gust();
echo $current->clouds()?->coverage();
```

Rain and snow are conditional. When present, `lastHour()` returns the
precipitation reported for the preceding hour in millimetres per hour.

```php
echo $current->rain()?->lastHour();
echo $current->snow()?->lastHour();
```

Observation, sunrise, and sunset timestamps are nullable UTC
`DateTimeImmutable` values. `timezoneOffset()` provides the location's offset
from UTC in seconds.

## Forecast

The 5 Day / 3 Hour Forecast API is available on OpenWeather's standard free
and paid subscriptions. See the
[official forecast documentation](https://openweathermap.org/api/forecast5)
for API details.

Use `forecast()` with a latitude and longitude. The optional `count` limits the
number of three-hour periods returned and must be positive.

```php
$forecast = $api->weather()->forecast(
    latitude: 38.7223,
    longitude: -9.1393,
    count: 8,
);
```

`forecast()` returns a `Forecast` entity containing its periods and city
metadata. Missing or `null` period lists become empty arrays.

```php
use ProgrammatorDev\OpenWeatherMap\Enum\PartOfDay;

foreach ($forecast->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->temperature();
    echo $period->precipitationProbability();
    echo $period->wind()?->speed();
    echo $period->rain()?->lastThreeHours();
    echo $period->snow()?->lastThreeHours();

    if ($period->partOfDay() === PartOfDay::DAY) {
        // This period occurs during daytime at the forecast location.
    }
}

echo $forecast->city()?->name();
echo $forecast->city()?->coordinates()?->latitude();
echo $forecast->city()?->coordinates()?->longitude();
echo $forecast->city()?->timezoneOffset();
```

OpenWeather returns precipitation probability as a fraction. The library
normalizes it to a percentage so it follows the same getter pattern as other
measurements:

```php
$period->precipitationProbability();          // 60.0
$period->precipitationProbabilityUnit();      // Unit::PERCENT
$period->precipitationProbabilityWithUnit();  // '60 %'
```

Forecast, sunrise, and sunset timestamps are nullable UTC
`DateTimeImmutable` values. The city timezone offset remains separate.

## Units And Language

Weather requests use the API configuration by default. Configure units and
language for a request with `withUnits()` and `withLanguage()`.

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;

$current = $api
    ->weather()
    ->withUnits(Units::METRIC)
    ->withLanguage(Language::PORTUGUESE)
    ->current(
        latitude: 38.7223,
        longitude: -9.1393,
    );
```

Measurement getters remain numeric. Companion `Unit` and `WithUnit` methods
provide the unit and a formatted value. For example, when a metric response
contains a temperature of `22.55`:

```php
$current->temperature();               // 22.55
$current->temperatureUnit();           // Unit::CELSIUS
$current->temperatureUnit()->symbol(); // '°C'
$current->temperatureWithUnit();       // '22.55 °C'
```
