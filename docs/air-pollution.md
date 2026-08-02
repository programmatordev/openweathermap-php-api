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

## Forecast

The Air Pollution Forecast API provides hourly periods for four days. See the
[official Air Pollution API documentation](https://openweathermap.org/api/air-pollution)
for the upstream endpoint contract.

Use `forecast()` with a latitude and longitude. Both coordinates are validated
before the request is sent.

```php
$forecast = $api->airPollution()->forecast(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

The returned `Forecast` entity exposes the response coordinates and a typed
collection of hourly periods. Missing or `null` period lists become empty
arrays, and every period property may be absent or explicitly `null`.

```php
echo $forecast->coordinates()?->latitude();
echo $forecast->coordinates()?->longitude();

foreach ($forecast->periods() as $period) {
    echo $period->forecastAt()?->format(DATE_ATOM);
    echo $period->airQualityIndex()?->value;
    echo $period->components()?->fineParticulateMatter();
}
```

Forecast periods use the same OpenWeather Air Quality Index and fixed
`µg/m³` pollutant units as current observations.

## History

The Historical Air Pollution API returns hourly observations for a coordinate
and date range. OpenWeather documents historical availability from November 27,
2020, although actual availability may vary. See the
[official Air Pollution API documentation](https://openweathermap.org/api/air-pollution)
for the upstream endpoint contract.

Use `history()` with a latitude, longitude, start date, and end date. The date
arguments accept any `DateTimeInterface` implementation and are sent as Unix
timestamps. The end must be after or equal to the start and cannot be in the
future.

```php
$history = $api->airPollution()->history(
    latitude: 38.7223,
    longitude: -9.1393,
    start: new DateTimeImmutable('2 days ago'),
    end: new DateTimeImmutable('1 day ago'),
);
```

The returned `History` entity exposes the response coordinates and a typed
collection of hourly periods. A valid range for which OpenWeather has no data
returns an empty collection.

```php
echo $history->coordinates()?->latitude();
echo $history->coordinates()?->longitude();

foreach ($history->periods() as $period) {
    echo $period->observedAt()?->format(DATE_ATOM);
    echo $period->airQualityIndex()?->value;
    echo $period->components()?->fineParticulateMatter();
}
```

Historical periods use the same OpenWeather Air Quality Index and fixed
`µg/m³` pollutant units as current observations and forecast periods.
