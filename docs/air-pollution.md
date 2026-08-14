# Air Pollution

This guide covers current, forecast, and historical air pollution.

## Current

See OpenWeather's
[official Air Pollution API documentation](https://openweathermap.org/api/air-pollution)
for API details.

Use `current()` with a latitude and longitude.

```php
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

$api = new OpenWeatherMap($_ENV['OPENWEATHERMAP_API_KEY']);

$current = $api->airPollution()->current(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

`current()` returns the air-quality observation for the requested coordinates.
Every property may be absent or explicitly `null`.

```php
echo $current->coordinates()?->latitude();
echo $current->coordinates()?->longitude();
echo $current->dateTime()?->format(DATE_ATOM);
echo $current->airQualityIndex()?->value;
```

The air quality index uses OpenWeather's native scale from 1 (good) through 5
(very poor). Pollutant concentrations are grouped under `components()` and use
the fixed `µg/m³` unit documented by OpenWeather; weather unit configuration
does not affect them.

OpenWeather also documents the UK, European, US, and Mainland China scales in
its [Air Pollution Index levels](https://openweathermap.org/api/air-pollution-index-levels)
reference.

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

Concentration getters return nullable floats. Companion methods provide the
unit and a formatted value:

```php
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;

$components?->fineParticulateMatter();          // 5.89
$components?->fineParticulateMatterUnit();      // Unit::MICROGRAMS_PER_CUBIC_METER
$components?->fineParticulateMatterWithUnit();  // '5.89 µg/m³'
```

## Forecast

The Air Pollution Forecast API provides hourly periods for four days. See the
[official Air Pollution API documentation](https://openweathermap.org/api/air-pollution)
for API details.

Use `forecast()` with a latitude and longitude.

```php
$forecast = $api->airPollution()->forecast(
    latitude: 38.7223,
    longitude: -9.1393,
);
```

The returned `Forecast` entity exposes the response coordinates and hourly
periods. Missing or `null` period lists become empty arrays, and every period
property may be absent or explicitly `null`.

```php
echo $forecast->coordinates()?->latitude();
echo $forecast->coordinates()?->longitude();

foreach ($forecast->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->airQualityIndex()?->value;
    echo $period->components()?->fineParticulateMatter();
}
```

Forecast periods use the same OpenWeather Air Quality Index and fixed
`µg/m³` pollutant units as current observations.

## History

The Historical Air Pollution API returns hourly observations for a coordinate
and date range. See the
[official Air Pollution API documentation](https://openweathermap.org/api/air-pollution)
for API details.

Use `history()` with a latitude, longitude, start date, and end date. The date
arguments accept any `DateTimeInterface` implementation. The end must be after
or equal to the start and cannot be in the future.

```php
$history = $api->airPollution()->history(
    latitude: 38.7223,
    longitude: -9.1393,
    startAt: new DateTimeImmutable('2 days ago'),
    endAt: new DateTimeImmutable('1 day ago'),
);
```

The returned `History` entity exposes the response coordinates and hourly
periods. A valid range for which OpenWeather has no data returns an empty
collection.

```php
echo $history->coordinates()?->latitude();
echo $history->coordinates()?->longitude();

foreach ($history->periods() as $period) {
    echo $period->dateTime()?->format(DATE_ATOM);
    echo $period->airQualityIndex()?->value;
    echo $period->components()?->fineParticulateMatter();
}
```

Historical periods use the same OpenWeather Air Quality Index and fixed
`µg/m³` pollutant units as current observations and forecast periods.
