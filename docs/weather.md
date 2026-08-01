# Weather

## Current

The Current Weather API is available on OpenWeather's standard free and paid
subscriptions. See the
[official Current Weather API documentation](https://openweathermap.org/api/current)
for the upstream endpoint contract.

Use `current()` with a latitude and longitude. Both coordinates are validated
before the request is sent.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->weather()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

The method returns a `Current` entity. Every response property may be
absent or explicitly `null`; missing or `null` condition lists become empty
arrays.

```php
echo $current->name();
echo $current->latitude();
echo $current->longitude();
echo $current->temperature();
echo $current->feelsLikeTemperature();
echo $current->minimumTemperature();
echo $current->maximumTemperature();
echo $current->pressure();
echo $current->humidity();
echo $current->visibility();
```

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

Observation, sunrise, and sunset timestamps are nullable `DateTimeImmutable`
values normalized to UTC. `timezoneOffset()` retains the location's offset
from UTC in seconds.

## Forecast

The 5 Day / 3 Hour Forecast API is available on OpenWeather's standard free
and paid subscriptions. See the
[official forecast documentation](https://openweathermap.org/api/forecast5)
for the upstream endpoint contract.

Use `forecast()` with a latitude and longitude. The optional `count` limits the
number of three-hour periods returned. It must be a positive integer; no
maximum is imposed by this library because the official documentation does not
define one.

```php
$forecast = $api->weather()->forecast(
    latitude: 38.7223,
    longitude: -9.1393,
    count: 8,
);
```

The method returns a `Forecast` entity containing its periods and city
metadata. Missing or `null` period lists become empty arrays.

```php
use ProgrammatorDev\OpenWeatherMap\Enum\PartOfDay;

foreach ($forecast->periods() as $period) {
    echo $period->forecastAt()?->format(DATE_ATOM);
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
echo $forecast->city()?->latitude();
echo $forecast->city()?->longitude();
echo $forecast->city()?->timezoneOffset();
```

Forecast, sunrise, and sunset timestamps are nullable UTC
`DateTimeImmutable` values. The city timezone offset remains separate.

## Units And Language

Weather requests use the API configuration by default. Request-local fluent
overrides are immutable and do not affect later calls through the original
resource.

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

Raw measurement getters remain numeric. Companion `Unit` and `WithUnit`
methods expose the effective request unit and a locale-independent formatted
value. For example, when a metric response contains a temperature of `22.55`:

```php
$current->temperature();               // 22.55
$current->temperatureUnit();           // Unit::CELSIUS
$current->temperatureUnit()->symbol(); // '°C'
$current->temperatureWithUnit();       // '22.55 °C'
```
