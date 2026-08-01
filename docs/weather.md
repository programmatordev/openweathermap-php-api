# Weather

The Current Weather API is available on OpenWeather's standard free and paid
subscriptions. See the
[official Current Weather API documentation](https://openweathermap.org/api/current)
for the upstream endpoint contract.

## Current Weather

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

The method returns a `CurrentWeather` entity. Every response property may be
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
the raw OpenWeather icon code and provides its absolute image URL.

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
precipitation volume reported for the preceding hour in millimetres.

```php
echo $current->rain()?->lastHour();
echo $current->snow()?->lastHour();
```

Observation, sunrise, and sunset timestamps are nullable `DateTimeImmutable`
values normalized to UTC. `timezoneOffset()` retains the location's offset
from UTC in seconds.

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
