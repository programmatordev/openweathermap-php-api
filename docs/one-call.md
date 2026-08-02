# One Call 4.0

One Call 4.0 requires a separate OpenWeather subscription and includes free
daily API calls. Consult OpenWeather's official documentation for the current
allowance, pricing, usage limits, and account configuration before using these
endpoints in production.

## Current

See OpenWeather's
[official One Call API 4.0 documentation](https://openweathermap.org/api/one-call-4#current)
for the upstream endpoint contract and current subscription terms.

Use `current()` with a latitude and longitude. Both coordinates are validated
before the request is sent.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->oneCall()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

Every response property may be absent or explicitly `null`. Coordinates and
timezone metadata describe the requested location, while `dateTime()` and the
astronomical timestamps remain UTC values.

```php
echo $current->coordinates()?->latitude();
echo $current->coordinates()?->longitude();
echo $current->timezone()?->identifier();
echo $current->dateTime()?->format(DATE_ATOM);
echo $current->temperature();

foreach ($current->conditions() as $condition) {
    echo $condition->description();
}
```

Units and language can be overridden for one immutable resource chain:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Language;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;

$current = $api
    ->oneCall()
    ->withUnits(Units::IMPERIAL)
    ->withLanguage(Language::PORTUGUESE)
    ->current(38.7223, -9.1393);
```

Raw measurement getters return nullable values. Companion methods expose the
effective unit and a locale-independent formatted value. With the default
metric configuration, for example:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;

$current->temperature();          // 24.34
$current->temperatureUnit();      // Unit::CELSIUS
$current->temperatureWithUnit();  // '24.34 °C'
```
