# Air Pollution

## Current

The Current Air Pollution API is available on OpenWeather's standard free and
paid subscriptions. See the
[official Air Pollution API documentation](https://openweathermap.org/api/air-pollution)
for the upstream endpoint contract.

Use `current()` with a latitude and longitude. Both coordinates are validated
before the request is sent.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->airPollution()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

The returned `Current` entity exposes the single observation directly. Every
response property may be absent or explicitly `null`.

```php
echo $current->coordinates()?->latitude();
echo $current->coordinates()?->longitude();
echo $current->observedAt()?->format(DATE_ATOM);
echo $current->airQualityIndex()?->value;
```

The air quality index uses OpenWeather's native scale from 1 (good) through 5
(very poor). Pollutant concentrations are grouped under `components()` and use
the fixed `µg/m³` unit documented by OpenWeather; weather unit configuration
does not affect them.

```php
$components = $current->components();

echo $components?->carbonMonoxide();
echo $components?->nitrogenMonoxide();
echo $components?->nitrogenDioxide();
echo $components?->ozone();
echo $components?->sulphurDioxide();
echo $components?->fineParticulateMatter();
echo $components?->coarseParticulateMatter();
echo $components?->ammonia();
```

Raw concentration getters return nullable floats. Companion methods expose the
unit and a locale-independent formatted value:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;

$components?->fineParticulateMatter();          // 5.89
$components?->fineParticulateMatterUnit();      // Unit::MICROGRAMS_PER_CUBIC_METER
$components?->fineParticulateMatterWithUnit();  // '5.89 µg/m³'
```
